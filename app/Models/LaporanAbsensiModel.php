<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanAbsensiModel extends Model
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('absensi');
    }

    public function getAbsensiByFilter($id_ibadah = null, $status = null, $metode = null)
    {
        try {
            // Reset builder
            $this->builder = $this->db->table('absensi');
            
            $this->builder->select('
                absensi.*,
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                jemaat.jenis_kelamin,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                ibadah.waktu_mulai,
                cabang_gereja.nama_cabang
            ');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            
            // Filter ibadah
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('absensi.id_ibadah', $id_ibadah);
            }
            
            // Filter status
            if ($status !== null && $status !== '' && $status !== 'null') {
                $this->builder->where('absensi.status', $status);
            }
            
            // Filter metode
            if ($metode !== null && $metode !== '' && $metode !== 'null') {
                $this->builder->where('absensi.metode', $metode);
            }
            
            $this->builder->orderBy('absensi.waktu', 'DESC');
            $query = $this->builder->get();
            $result = $query->getResult();
            
            log_message('debug', 'SQL: ' . $this->builder->getCompiledSelect());
            log_message('debug', 'Result count: ' . count($result));
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'getAbsensiByFilter error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllIbadah()
    {
        try {
            $this->builder = $this->db->table('ibadah');
            $this->builder->select('ibadah.*, cabang_gereja.nama_cabang');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            $this->builder->where('ibadah.status !=', 'batal');
            $this->builder->orderBy('ibadah.tanggal', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getAllIbadah error: ' . $e->getMessage());
            return [];
        }
    }

    public function getStatistik($id_ibadah = null, $status = null, $metode = null)
    {
        try {
            $this->builder = $this->db->table('absensi');
            $this->builder->select('
                COUNT(*) as total_absensi,
                SUM(CASE WHEN absensi.status = "hadir" THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi.status = "izin" THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN absensi.status = "sakit" THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN absensi.status = "alpa" THEN 1 ELSE 0 END) as total_alpa,
                SUM(CASE WHEN absensi.metode = "qr" THEN 1 ELSE 0 END) as total_qr,
                SUM(CASE WHEN absensi.metode = "manual" THEN 1 ELSE 0 END) as total_manual
            ');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('absensi.id_ibadah', $id_ibadah);
            }
            if ($status !== null && $status !== '' && $status !== 'null') {
                $this->builder->where('absensi.status', $status);
            }
            if ($metode !== null && $metode !== '' && $metode !== 'null') {
                $this->builder->where('absensi.metode', $metode);
            }
            
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getStatistik error: ' . $e->getMessage());
            return null;
        }
    }

    public function getStatusCount($id_ibadah = null, $metode = null)
    {
        try {
            $this->builder = $this->db->table('absensi');
            $this->builder->select('absensi.status, COUNT(*) as total');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('absensi.id_ibadah', $id_ibadah);
            }
            if ($metode !== null && $metode !== '' && $metode !== 'null') {
                $this->builder->where('absensi.metode', $metode);
            }
            
            $this->builder->groupBy('absensi.status');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getStatusCount error: ' . $e->getMessage());
            return [];
        }
    }

    public function getMetodeCount($id_ibadah = null, $status = null)
    {
        try {
            $this->builder = $this->db->table('absensi');
            $this->builder->select('absensi.metode, COUNT(*) as total');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('absensi.id_ibadah', $id_ibadah);
            }
            if ($status !== null && $status !== '' && $status !== 'null') {
                $this->builder->where('absensi.status', $status);
            }
            
            $this->builder->groupBy('absensi.metode');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getMetodeCount error: ' . $e->getMessage());
            return [];
        }
    }
}