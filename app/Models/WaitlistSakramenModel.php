<?php

namespace App\Models;

use CodeIgniter\Model;

class WaitlistSakramenModel extends Model
{
    protected $table            = 'waitlist_sakramen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_jemaat',
        'jenis_sakramen',
        'status_pendaftaran',
        'keterangan_admin',
        'pendaftar_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get Waitlist Data Left Joined to Jemaat
     */
    public function getWaitlistData()
    {
        return $this->select('waitlist_sakramen.*, jemaat.nama_jemaat, jemaat.no_anggota')
                    ->join('jemaat', 'jemaat.id = waitlist_sakramen.id_jemaat', 'left')
                    ->orderBy('waitlist_sakramen.created_at', 'DESC')
                    ->findAll();
    }
}
