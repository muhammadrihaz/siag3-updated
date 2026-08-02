<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanPersembahanModel extends Model
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('persembahan');
    }

    public function getPersembahanByFilter($id_ibadah = null, $jenis = null, $metode = null)
    {
        try {
            // Reset builder
            $this->builder = $this->db->table('persembahan');
            
            $this->builder->select('
                persembahan.*,
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                ibadah.waktu_mulai,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            // Filter ibadah
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('persembahan.id_ibadah', $id_ibadah);
            }
            
            // Filter jenis
            if ($jenis !== null && $jenis !== '' && $jenis !== 'null') {
                $this->builder->where('persembahan.jenis', $jenis);
            }
            
            // Filter metode
            if ($metode !== null && $metode !== '' && $metode !== 'null') {
                $this->builder->where('persembahan.metode', $metode);
            }
            
            $this->builder->orderBy('persembahan.created_at', 'DESC');
            $query = $this->builder->get();
            $result = $query->getResult();
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'getPersembahanByFilter error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllIbadah()
    {
        try {
            $this->builder = $this->db->table('ibadah');
            $this->builder->select('ibadah.*, sektor_pelayanan.nama_sektor');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->where('ibadah.status !=', 'batal');
            $this->builder->orderBy('ibadah.tanggal', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getAllIbadah error: ' . $e->getMessage());
            return [];
        }
    }

    public function getStatistik($id_ibadah = null, $jenis = null, $metode = null)
    {
        try {
            $this->builder = $this->db->table('persembahan');
            $this->builder->select('
                COUNT(*) as total_transaksi,
                SUM(persembahan.nominal) as total_nominal,
                SUM(CASE WHEN persembahan.jenis = "kantong_putih" THEN persembahan.nominal ELSE 0 END) as total_putih,
                SUM(CASE WHEN persembahan.jenis = "kantong_cokelat" THEN persembahan.nominal ELSE 0 END) as total_cokelat,
                SUM(CASE WHEN persembahan.jenis = "persembahan_khusus" THEN persembahan.nominal ELSE 0 END) as total_khusus,
                SUM(CASE WHEN persembahan.metode = "tunai" THEN persembahan.nominal ELSE 0 END) as total_tunai,
                SUM(CASE WHEN persembahan.metode = "transfer" THEN persembahan.nominal ELSE 0 END) as total_transfer,
                SUM(CASE WHEN persembahan.metode = "qris" THEN persembahan.nominal ELSE 0 END) as total_qris
            ');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('persembahan.id_ibadah', $id_ibadah);
            }
            if ($jenis !== null && $jenis !== '' && $jenis !== 'null') {
                $this->builder->where('persembahan.jenis', $jenis);
            }
            if ($metode !== null && $metode !== '' && $metode !== 'null') {
                $this->builder->where('persembahan.metode', $metode);
            }
            
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getStatistik error: ' . $e->getMessage());
            return null;
        }
    }

    public function getJenisCount($id_ibadah = null, $metode = null)
    {
        try {
            $this->builder = $this->db->table('persembahan');
            $this->builder->select('persembahan.jenis, COUNT(*) as total, SUM(persembahan.nominal) as nominal');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('persembahan.id_ibadah', $id_ibadah);
            }
            if ($metode !== null && $metode !== '' && $metode !== 'null') {
                $this->builder->where('persembahan.metode', $metode);
            }
            
            $this->builder->groupBy('persembahan.jenis');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getJenisCount error: ' . $e->getMessage());
            return [];
        }
    }

    public function getMetodeCount($id_ibadah = null, $jenis = null)
    {
        try {
            $this->builder = $this->db->table('persembahan');
            $this->builder->select('persembahan.metode, COUNT(*) as total, SUM(persembahan.nominal) as nominal');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('persembahan.id_ibadah', $id_ibadah);
            }
            if ($jenis !== null && $jenis !== '' && $jenis !== 'null') {
                $this->builder->where('persembahan.jenis', $jenis);
            }
            
            $this->builder->groupBy('persembahan.metode');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getMetodeCount error: ' . $e->getMessage());
            return [];
        }
    }

    public function getJenisOptions()
    {
        return [
            'kantong_putih' => 'Kantong Putih',
            'kantong_cokelat' => 'Kantong Cokelat',
            'persembahan_khusus' => 'Persembahan Khusus'
        ];
    }

    public function getMetodeOptions()
    {
        return [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS'
        ];
    }
}