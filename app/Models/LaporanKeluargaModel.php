<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanKeluargaModel extends Model
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('keluarga');
    }

    public function getKeluargaByWilayah($id_sektor_pelayanan = null)
    {
        try {
            $this->builder->select('
                keluarga.*,
                sektor_pelayanan.nama_sektor,
                sektor_pelayanan.koordinator_sektor,
                (SELECT COUNT(*) FROM jemaat WHERE jemaat.id_keluarga = keluarga.id AND jemaat.status_aktif = 1) as jumlah_anggota
            ');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            
            if ($id_sektor_pelayanan) {
                $this->builder->where('keluarga.id_sektor_pelayanan', $id_sektor_pelayanan);
            }
            
            $this->builder->orderBy('keluarga.nama_kepala', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getKeluargaBySektor Pelayanan error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAnggotaKeluarga($id_keluarga)
    {
        try {
            $this->builder = $this->db->table('jemaat');
            $this->builder->select('
                jemaat.*,
                (SELECT COUNT(*) FROM absensi WHERE absensi.id_jemaat = jemaat.id) as total_absensi
            ');
            $this->builder->where('jemaat.id_keluarga', $id_keluarga);
            $this->builder->where('jemaat.status_aktif', 1);
            $this->builder->orderBy('jemaat.status_dalam_keluarga', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getAnggotaKeluarga error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllWilayah()
    {
        try {
            $this->builder = $this->db->table('sektor_pelayanan');
            $this->builder->orderBy('nama_sektor', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getAllSektor Pelayanan error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTotalKeluarga($id_sektor_pelayanan = null)
    {
        try {
            $this->builder = $this->db->table('keluarga');
            if ($id_sektor_pelayanan) {
                $this->builder->where('id_sektor_pelayanan', $id_sektor_pelayanan);
            }
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'getTotalKeluarga error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getTotalJemaat($id_sektor_pelayanan = null)
    {
        try {
            $this->builder = $this->db->table('jemaat');
            $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
            $this->builder->where('jemaat.status_aktif', 1);
            if ($id_sektor_pelayanan) {
                $this->builder->where('keluarga.id_sektor_pelayanan', $id_sektor_pelayanan);
            }
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'getTotalJemaat error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getStatistikWilayah()
    {
        try {
            $this->builder = $this->db->table('sektor_pelayanan');
            $this->builder->select('
                sektor_pelayanan.*,
                (SELECT COUNT(*) FROM keluarga WHERE keluarga.id_sektor_pelayanan = sektor_pelayanan.id) as total_keluarga,
                (SELECT COUNT(*) FROM jemaat 
                 JOIN keluarga ON keluarga.id = jemaat.id_keluarga 
                 WHERE keluarga.id_sektor_pelayanan = sektor_pelayanan.id AND jemaat.status_aktif = 1) as total_jemaat
            ');
            $this->builder->orderBy('sektor_pelayanan.nama_sektor', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getStatistikSektor Pelayanan error: ' . $e->getMessage());
            return [];
        }
    }
}