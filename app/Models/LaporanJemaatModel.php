<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanJemaatModel extends Model
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('jemaat');
    }

    public function getJemaatByFilter($id_sektor_pelayanan = null, $jenis_kelamin = null, $status_aktif = null)
    {
        try {
            $this->builder->select('
                jemaat.*,
                keluarga.nama_kepala,
                keluarga.no_kk,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            
            // Filter wilayah
            if ($id_sektor_pelayanan) {
                $this->builder->where('keluarga.id_sektor_pelayanan', $id_sektor_pelayanan);
            }
            
            // Filter jenis kelamin
            if ($jenis_kelamin) {
                $this->builder->where('jemaat.jenis_kelamin', $jenis_kelamin);
            }
            
            // Filter status aktif
            if ($status_aktif !== null && $status_aktif !== '') {
                $this->builder->where('jemaat.status_aktif', $status_aktif);
            }
            
            $this->builder->orderBy('jemaat.nama_jemaat', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getJemaatByFilter error: ' . $e->getMessage());
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

    public function getStatistik($id_sektor_pelayanan = null, $jenis_kelamin = null, $status_aktif = null)
    {
        try {
            $this->builder = $this->db->table('jemaat');
            $this->builder->select('COUNT(*) as total');
            $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
            
            if ($id_sektor_pelayanan) {
                $this->builder->where('keluarga.id_sektor_pelayanan', $id_sektor_pelayanan);
            }
            if ($jenis_kelamin) {
                $this->builder->where('jemaat.jenis_kelamin', $jenis_kelamin);
            }
            if ($status_aktif !== null && $status_aktif !== '') {
                $this->builder->where('jemaat.status_aktif', $status_aktif);
            }
            
            $query = $this->builder->get();
            $result = $query->getRow();
            return $result->total ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'getStatistik error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getStatistikByWilayah($id_sektor_pelayanan = null, $jenis_kelamin = null, $status_aktif = null)
    {
        try {
            $this->builder = $this->db->table('jemaat');
            $this->builder->select('
                sektor_pelayanan.nama_sektor,
                COUNT(*) as total,
                SUM(CASE WHEN jemaat.jenis_kelamin = "L" THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN jemaat.jenis_kelamin = "P" THEN 1 ELSE 0 END) as perempuan,
                SUM(CASE WHEN jemaat.status_aktif = 1 THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN jemaat.status_aktif = 0 THEN 1 ELSE 0 END) as tidak_aktif
            ');
            $this->builder->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            
            if ($id_sektor_pelayanan) {
                $this->builder->where('keluarga.id_sektor_pelayanan', $id_sektor_pelayanan);
            }
            if ($jenis_kelamin) {
                $this->builder->where('jemaat.jenis_kelamin', $jenis_kelamin);
            }
            if ($status_aktif !== null && $status_aktif !== '') {
                $this->builder->where('jemaat.status_aktif', $status_aktif);
            }
            
            $this->builder->groupBy('sektor_pelayanan.id');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getStatistikBySektor Pelayanan error: ' . $e->getMessage());
            return [];
        }
    }
}