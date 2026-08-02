<?php

$path = 'app/Controllers/Ibadah.php';
$content = file_get_contents($path);

$approvalLogic = <<<EOD
    /**
     * Approve Ketua 5
     */
    public function approveKetua5(\$id)
    {
        try {
            if (\$this->request->isAJAX()) {
                \$role = \$this->session->get('role');
                if (!in_array(\$role, ['master', 'admin_master', 'ketua_5'])) {
                    return \$this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki hak akses untuk menyetujui jadwal ini!'
                    ]);
                }
                
                \$ibadah = \$this->ibadahModel->find(\$id);
                if (!\$ibadah) {
                    return \$this->response->setJSON(['status' => 'error', 'message' => 'Data ibadah tidak ditemukan!']);
                }
                
                \$update = \$this->ibadahModel->update(\$id, [
                    'approval_ketua5' => 'approved'
                ]);
                
                if (\$update) {
                    return \$this->response->setJSON(['status' => 'success', 'message' => 'Jadwal disetujui Ketua 5!']);
                } else {
                    return \$this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyetujui.']);
                }
            }
        } catch (\Exception \$e) {
            return \$this->response->setJSON(['status' => 'error', 'message' => \$e->getMessage()]);
        }
    }
}
EOD;

// find the last closing brace
$pos = strrpos($content, '}');
if ($pos !== false) {
    $content = substr_replace($content, $approvalLogic, $pos, 1);
    file_put_contents($path, $content);
    echo "API Approval Ketua 5 Injected.";
} else {
    echo "Failed to inject API Approval.";
}
