<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanIbadahModel extends Model
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('ibadah');
    }

    public function getIbadahByFilter($id_cabang_gereja = null, $tanggal_awal = null, $tanggal_akhir = null, $status = null)
    {
        try {
            $this->builder->select('
                ibadah.*,
                cabang_gereja.nama_cabang,
                (SELECT COUNT(*) FROM absensi WHERE absensi.id_ibadah = ibadah.id) as total_absensi,
                (SELECT COUNT(*) FROM absensi WHERE absensi.id_ibadah = ibadah.id AND absensi.status = "hadir") as total_hadir,
                (SELECT COUNT(*) FROM pelayan WHERE pelayan.id_ibadah = ibadah.id) as total_pelayan,
                (SELECT SUM(nominal) FROM persembahan WHERE persembahan.id_ibadah = ibadah.id) as total_persembahan
            ');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            
            // Filter wilayah
            if ($id_cabang_gereja) {
                $this->builder->where('ibadah.id_cabang_gereja', $id_cabang_gereja);
            }
            
            // Filter tanggal
            if ($tanggal_awal) {
                $this->builder->where('ibadah.tanggal >=', $tanggal_awal);
            }
            if ($tanggal_akhir) {
                $this->builder->where('ibadah.tanggal <=', $tanggal_akhir);
            }
            
            // Filter status
            if ($status) {
                $this->builder->where('ibadah.status', $status);
            }
            
            $this->builder->orderBy('ibadah.tanggal', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getIbadahByFilter error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllCabang()
    {
        try {
            $this->builder = $this->db->table('cabang_gereja');
            $this->builder->orderBy('nama_cabang', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getAllCabang error: ' . $e->getMessage());
            return [];
        }
    }

    public function getStatistik($id_cabang_gereja = null, $tanggal_awal = null, $tanggal_akhir = null, $status = null)
    {
        try {
            $this->builder = $this->db->table('ibadah');
            $this->builder->select('
                COUNT(*) as total_ibadah,
                SUM(ibadah.jumlah_hadir) as total_hadir,
                SUM(ibadah.total_peserta) as total_peserta,
                (SELECT SUM(nominal) FROM persembahan WHERE persembahan.id_ibadah = ibadah.id) as total_persembahan
            ');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            
            if ($id_cabang_gereja) {
                $this->builder->where('ibadah.id_cabang_gereja', $id_cabang_gereja);
            }
            if ($tanggal_awal) {
                $this->builder->where('ibadah.tanggal >=', $tanggal_awal);
            }
            if ($tanggal_akhir) {
                $this->builder->where('ibadah.tanggal <=', $tanggal_akhir);
            }
            if ($status) {
                $this->builder->where('ibadah.status', $status);
            }
            
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getStatistik error: ' . $e->getMessage());
            return null;
        }
    }

    public function getStatusCount($id_cabang_gereja = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        try {
            $this->builder = $this->db->table('ibadah');
            $this->builder->select('status, COUNT(*) as total');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            
            if ($id_cabang_gereja) {
                $this->builder->where('ibadah.id_cabang_gereja', $id_cabang_gereja);
            }
            if ($tanggal_awal) {
                $this->builder->where('ibadah.tanggal >=', $tanggal_awal);
            }
            if ($tanggal_akhir) {
                $this->builder->where('ibadah.tanggal <=', $tanggal_akhir);
            }
            
            $this->builder->groupBy('status');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getStatusCount error: ' . $e->getMessage());
            return [];
        }
    }
}