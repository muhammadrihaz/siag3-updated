<?php

$path = 'app/Controllers/Ibadah.php';
$content = file_get_contents($path);

$approvalLogic = <<<EOD
    /**
     * Setujui Persembahan (Bendahara / Master)
     */
    public function approvePersembahan(\$id)
    {
        try {
            if (\$this->request->isAJAX()) {
                \$role = \$this->session->get('role');
                if (!in_array(\$role, ['bendahara', 'master', 'admin_master'])) {
                    return \$this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki hak akses untuk menyetujui!'
                    ]);
                }
                
                \$persembahan = \$this->persembahanModel->find(\$id);
                if (!\$persembahan) {
                    return \$this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan!'
                    ]);
                }
                
                \$update = \$this->persembahanModel->update(\$id, [
                    'status_approval' => 'approved',
                    'approved_by' => \$this->session->get('id_jemaat') ?? 1,
                    'approved_at' => date('Y-m-d H:i:s')
                ]);
                
                if (\$update) {
                    return \$this->response->setJSON(['status' => 'success', 'message' => 'Persembahan disetujui!']);
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
    echo "API Approval Injected.";
} else {
    echo "Failed to inject API Approval.";
}
