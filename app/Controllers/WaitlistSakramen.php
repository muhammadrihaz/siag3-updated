<?php

namespace App\Controllers;

use App\Models\JemaatModel;
use App\Models\WaitlistSakramenModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class WaitlistSakramen extends Controller
{
    private const STAFF_ROLES = ['master', 'admin_master', 'admin_area', 'pendeta', 'sekretaris'];
    private const ALLOWED_TYPES = ['baptis_anak', 'baptis_dewasa', 'sidi', 'pernikahan'];

    protected WaitlistSakramenModel $waitlistModel;
    protected JemaatModel $jemaatModel;
    protected $session;

    public function __construct()
    {
        $this->waitlistModel = new WaitlistSakramenModel();
        $this->jemaatModel = new JemaatModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $isStaff = $this->isStaff();
        $idJemaat = $isStaff ? null : (int) $this->session->get('id_jemaat');

        if (!$isStaff && $idJemaat < 1) {
            return redirect()->to('/logout')->with('error', 'Akun tidak terhubung dengan data jemaat. Silakan hubungi admin.');
        }

        return view('waitlist/index', [
            'active_menu' => 'pelayanan',
            'sub_menu' => 'waitlist',
            'title' => $isStaff ? 'Waitlist Pelayanan Sakramen' : 'Permohonan Sakramen Saya',
            'waitlist' => $this->waitlistModel->getWaitlistData($idJemaat ?: null),
            'jemaat' => $isStaff ? $this->jemaatModel->getActive() : [],
            'current_jemaat' => $idJemaat ? $this->jemaatModel->find($idJemaat) : null,
            'is_staff' => $isStaff,
        ]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonError('Permintaan tidak valid.', 400);
        }

        $isStaff = $this->isStaff();
        $id = (int) ($this->request->getPost('id') ?: 0);

        if (!$isStaff && $id > 0) {
            return $this->jsonError('Jemaat tidak dapat mengubah permohonan yang sudah dikirim.', 403);
        }

        $jenisSakramen = (string) $this->request->getPost('jenis_sakramen');
        if (!in_array($jenisSakramen, self::ALLOWED_TYPES, true)) {
            return $this->jsonError('Jenis pelayanan sakramen tidak valid.', 422);
        }

        $idJemaat = $isStaff
            ? (int) $this->request->getPost('id_jemaat')
            : (int) $this->session->get('id_jemaat');
        $jemaat = $this->jemaatModel->where('id', $idJemaat)->where('status_aktif', 1)->first();

        if (!$jemaat) {
            return $this->jsonError('Data jemaat aktif tidak ditemukan.', 422);
        }

        $existing = null;
        if ($id > 0) {
            $existing = $this->waitlistModel->find($id);
            if (!$existing || !$isStaff) {
                return $this->jsonError('Permohonan tidak ditemukan atau tidak dapat diakses.', 404);
            }
        } else {
            $duplicate = $this->waitlistModel
                ->where('id_jemaat', $idJemaat)
                ->where('jenis_sakramen', $jenisSakramen)
                ->whereIn('status_pendaftaran', ['pending', 'proses'])
                ->first();
            if ($duplicate) {
                return $this->jsonError('Permohonan untuk jenis pelayanan ini masih aktif. Silakan tunggu proses sebelumnya.', 409);
            }
        }

        $attachment = $this->request->getFile('attachment');
        $hasNewAttachment = $attachment && $attachment->getError() !== UPLOAD_ERR_NO_FILE;

        if ($id === 0 && !$hasNewAttachment) {
            return $this->jsonError('Dokumen persyaratan wajib dilampirkan.', 422);
        }

        if ($hasNewAttachment) {
            $rules = [
                'attachment' => 'uploaded[attachment]|max_size[attachment,5120]|ext_in[attachment,pdf,jpg,jpeg,png]|mime_in[attachment,application/pdf,image/jpeg,image/png]',
            ];
            $messages = [
                'attachment' => [
                    'uploaded' => 'Dokumen persyaratan wajib dilampirkan.',
                    'max_size' => 'Ukuran dokumen maksimal 5 MB.',
                    'ext_in' => 'Dokumen harus berformat PDF, JPG, JPEG, atau PNG.',
                    'mime_in' => 'Isi dokumen tidak sesuai dengan format yang diizinkan.',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return $this->jsonError(implode(' ', $this->validator->getErrors()), 422, $this->validator->getErrors());
            }
        }

        $status = $isStaff ? (string) ($this->request->getPost('status_pendaftaran') ?: 'pending') : 'pending';
        if (!in_array($status, ['pending', 'proses', 'selesai', 'batal'], true)) {
            return $this->jsonError('Status permohonan tidak valid.', 422);
        }

        $data = [
            'id_jemaat' => $idJemaat,
            'jenis_sakramen' => $jenisSakramen,
            'status_pendaftaran' => $status,
        ];

        if ($id === 0 || !$isStaff) {
            $data['catatan_pemohon'] = trim((string) $this->request->getPost('catatan_pemohon')) ?: null;
            $data['pendaftar_by'] = (int) $this->session->get('user_id');
        }
        if ($isStaff) {
            $data['keterangan_admin'] = trim((string) $this->request->getPost('keterangan_admin')) ?: null;
        }

        $newStoredName = null;
        if ($hasNewAttachment) {
            $uploadDirectory = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'sakramen';
            if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0750, true) && !is_dir($uploadDirectory)) {
                return $this->jsonError('Folder penyimpanan dokumen tidak dapat dibuat.', 500);
            }

            $newStoredName = $attachment->getRandomName();
            // Ambil metadata sebelum move(); setelah dipindahkan path file
            // sementara tidak lagi tersedia untuk pemeriksaan MIME.
            $originalName = $this->sanitizeDownloadName($attachment->getClientName());
            $detectedMime = $attachment->getMimeType();
            $fileSize = $attachment->getSize();
            try {
                $attachment->move($uploadDirectory, $newStoredName);
            } catch (\Throwable $e) {
                log_message('error', 'Upload lampiran sakramen gagal: ' . $e->getMessage());
                return $this->jsonError('Dokumen gagal disimpan. Silakan coba lagi.', 500);
            }

            $data['attachment_path'] = $newStoredName;
            $data['attachment_name'] = $originalName;
            $data['attachment_mime'] = $detectedMime;
            $data['attachment_size'] = $fileSize;
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $saved = $id === 0
                ? $this->waitlistModel->insert($data, true)
                : $this->waitlistModel->update($id, $data);

            if (!$saved || $db->transStatus() === false) {
                throw new \RuntimeException('Model gagal menyimpan permohonan.');
            }

            $db->transCommit();

            if ($newStoredName && $existing && !empty($existing->attachment_path)) {
                $this->removeAttachment((string) $existing->attachment_path);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $id === 0 ? 'Permohonan sakramen berhasil dikirim.' : 'Permohonan berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($newStoredName) {
                $this->removeAttachment($newStoredName);
            }
            log_message('error', 'Penyimpanan permohonan sakramen gagal: ' . $e->getMessage());
            return $this->jsonError('Permohonan belum dapat disimpan. Silakan coba lagi.', 500);
        }
    }

    public function get($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonError('Permintaan tidak valid.', 400);
        }

        $data = $this->waitlistModel->find((int) $id);
        if (!$data || !$this->canAccess($data)) {
            return $this->jsonError('Permohonan tidak ditemukan atau tidak dapat diakses.', 404);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function attachment($id)
    {
        $data = $this->waitlistModel->find((int) $id);
        if (!$data || !$this->canAccess($data) || empty($data->attachment_path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lampiran tidak ditemukan.');
        }

        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'sakramen' . DIRECTORY_SEPARATOR . basename((string) $data->attachment_path);
        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berkas lampiran tidak ditemukan.');
        }

        return $this->response
            ->download($path, null)
            ->setFileName($this->sanitizeDownloadName((string) ($data->attachment_name ?: 'lampiran')));
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonError('Permintaan tidak valid.', 400);
        }
        if (!$this->isStaff()) {
            return $this->jsonError('Hanya petugas yang dapat menghapus permohonan.', 403);
        }

        $data = $this->waitlistModel->find((int) $id);
        if (!$data) {
            return $this->jsonError('Permohonan tidak ditemukan.', 404);
        }

        if (!$this->waitlistModel->delete((int) $id)) {
            return $this->jsonError('Data gagal dihapus.', 500);
        }

        if (!empty($data->attachment_path)) {
            $this->removeAttachment((string) $data->attachment_path);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Permohonan berhasil dihapus.']);
    }

    private function canAccess(object $data): bool
    {
        return $this->isStaff() || (int) $data->id_jemaat === (int) $this->session->get('id_jemaat');
    }

    private function isStaff(): bool
    {
        return in_array((string) $this->session->get('role'), self::STAFF_ROLES, true);
    }

    private function removeAttachment(string $storedName): void
    {
        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'sakramen' . DIRECTORY_SEPARATOR . basename($storedName);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function sanitizeDownloadName(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($name)) ?? 'lampiran';
        return trim($name) !== '' ? $name : 'lampiran';
    }

    private function jsonError(string $message, int $status, array $errors = []): ResponseInterface
    {
        $payload = ['status' => 'error', 'message' => $message];
        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return $this->response->setStatusCode($status)->setJSON($payload);
    }
}
