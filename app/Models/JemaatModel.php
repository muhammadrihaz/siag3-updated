<?php

namespace App\Models;

use CodeIgniter\Model;

class JemaatModel extends Model
{
    protected $table = 'jemaat';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_keluarga', 'nama_jemaat', 'no_anggota', 'status_dalam_keluarga', 
        'tanggal_lahir', 'jenis_kelamin', 'no_hp', 'email', 'alamat',
        'status_aktif', 'qr_token', 'keterangan'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'nama_jemaat', 'no_anggota', 'status_dalam_keluarga', 'nama_kepala', 'nama_sektor'];
    protected $column_search = ['nama_jemaat', 'no_anggota', 'status_dalam_keluarga', 'nama_kepala', 'nama_sektor'];
    protected $order = ['id' => 'DESC'];
    
    protected $request;
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table($this->table);
        $this->request = \Config\Services::request();
    }

    private function _getDatatablesQuery()
    {
        $this->builder->select('
            jemaat.*, 
            keluarga.nama_kepala, 
            keluarga.no_kk,
            keluarga.id_sektor_pelayanan,
            sektor_pelayanan.nama_sektor
        ');
        $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
        $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
        
        $i = 0;
        $searchValue = $this->request->getPost('search')['value'] ?? '';
        
        foreach ($this->column_search as $item) {
            if ($searchValue) {
                if ($i === 0) {
                    $this->builder->groupStart();
                    $this->builder->like($item, $searchValue);
                } else {
                    $this->builder->orLike($item, $searchValue);
                }
                
                if (count($this->column_search) - 1 == $i) {
                    $this->builder->groupEnd();
                }
            }
            $i++;
        }
        
        $orderColumn = $this->request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'DESC';
        
        if (isset($this->column_order[$orderColumn])) {
            $this->builder->orderBy($this->column_order[$orderColumn], $orderDir);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->builder->orderBy(key($order), $order[key($order)]);
        }
    }

    public function getDatatables()
    {
        try {
            $this->_getDatatablesQuery();
            
            $length = $this->request->getPost('length') ?? 10;
            $start = $this->request->getPost('start') ?? 0;
            
            if ($length != -1) {
                $this->builder->limit($length, $start);
            }
            
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getDatatables error: ' . $e->getMessage());
            return [];
        }
    }

    public function countFiltered()
    {
        try {
            $this->_getDatatablesQuery();
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countFiltered error: ' . $e->getMessage());
            return 0;
        }
    }

    public function countAll()
    {
        try {
            $this->builder->select('jemaat.*');
            $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getJemaat()
    {
        try {
            $this->builder->select('
                jemaat.*, 
                keluarga.nama_kepala, 
                keluarga.no_kk,
                keluarga.id_sektor_pelayanan,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            $this->builder->orderBy('jemaat.nama_jemaat', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getJemaat error: ' . $e->getMessage());
            return [];
        }
    }

    public function getJemaatById($id)
    {
        try {
            $this->builder->select('
                jemaat.*, 
                keluarga.nama_kepala, 
                keluarga.no_kk,
                keluarga.id_sektor_pelayanan,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            $this->builder->where('jemaat.id', $id);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getJemaatById error: ' . $e->getMessage());
            return null;
        }
    }

    public function getByKeluarga($id_keluarga)
    {
        try {
            return $this->where('id_keluarga', $id_keluarga)->where('status_aktif', 1)->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getByKeluarga error: ' . $e->getMessage());
            return [];
        }
    }

    public function countByKeluarga($id_keluarga)
    {
        try {
            return $this->where('id_keluarga', $id_keluarga)->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countByKeluarga error: ' . $e->getMessage());
            return 0;
        }
    }

    public function generateNoAnggota()
    {
        $tahun = date('Y');
        $last = $this->getLastNumber($tahun);
        $no_urut = $last + 1;
        return "JMT-{$tahun}-{$no_urut}";
    }

    public function getLastNumber($tahun)
    {
        try {
            $this->builder->select('MAX(CAST(SUBSTRING(no_anggota, 10) AS UNSIGNED)) as last');
            $this->builder->like('no_anggota', "JMT-{$tahun}-", 'after');
            $query = $this->builder->get();
            $result = $query->getRow();
            return $result->last ? $result->last : 0;
        } catch (\Exception $e) {
            log_message('error', 'getLastNumber error: ' . $e->getMessage());
            return 0;
        }
    }

    public function generateQrToken()
    {
        return md5(uniqid(rand(), true));
    }

    public function getActive()
    {
        try {
            return $this->where('status_aktif', 1)->orderBy('nama_jemaat', 'ASC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getActive error: ' . $e->getMessage());
            return [];
        }
    }

    public function countActive()
    {
        try {
            return $this->where('status_aktif', 1)->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countActive error: ' . $e->getMessage());
            return 0;
        }
    }
    /**
     * Generate QR Code menggunakan API QR Server
     * Simpan file di folder assets/qrcodes/
     * 
     * @param int $id ID jemaat
     * @param string $data Data untuk QR Code (no_anggota)
     * @return bool
     */
    public function generateQrCode($id, $data)
    {
        try {
            // Pastikan folder qrcodes ada
            $folder = FCPATH . 'assets/qrcodes';
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            
            $filename = $folder . '/jemaat_' . $id . '.png';
            
            // Gunakan API QR Server (simple dan reliable)
            $qrData = urlencode($data);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $qrData;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $qrUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $qrImage = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($qrImage !== false && $httpCode == 200) {
                file_put_contents($filename, $qrImage);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            log_message('error', 'generateQrCode error: ' . $e->getMessage());
            return false;
        }
    }
}