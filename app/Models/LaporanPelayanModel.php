<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanPelayanModel extends Model
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('pelayan');
    }

    public function getPelayanByFilter($id_ibadah = null, $tugas = null, $status = null)
    {
        try {
            // Reset builder
            $this->builder = $this->db->table('pelayan');
            
            $this->builder->select('
                pelayan.*,
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                ibadah.waktu_mulai,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            // Filter ibadah
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('pelayan.id_ibadah', $id_ibadah);
            }
            
            // Filter tugas
            if ($tugas !== null && $tugas !== '' && $tugas !== 'null') {
                $this->builder->where('pelayan.tugas', $tugas);
            }
            
            // Filter status
            if ($status !== null && $status !== '' && $status !== 'null') {
                $this->builder->where('pelayan.status', $status);
            }
            
            $this->builder->orderBy('pelayan.id', 'DESC');
            $query = $this->builder->get();
            $result = $query->getResult();
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'getPelayanByFilter error: ' . $e->getMessage());
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

    public function getTugasList()
    {
        try {
            $this->builder = $this->db->table('pelayan');
            $this->builder->select('tugas');
            $this->builder->distinct();
            $this->builder->orderBy('tugas', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getTugasList error: ' . $e->getMessage());
            return [];
        }
    }

    public function getStatistik($id_ibadah = null, $tugas = null, $status = null)
    {
        try {
            $this->builder = $this->db->table('pelayan');
            $this->builder->select('
                COUNT(*) as total_pelayan,
                SUM(CASE WHEN pelayan.status = "ditugaskan" THEN 1 ELSE 0 END) as total_ditugaskan,
                SUM(CASE WHEN pelayan.status = "konfirmasi" THEN 1 ELSE 0 END) as total_konfirmasi,
                SUM(CASE WHEN pelayan.status = "hadir" THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN pelayan.status = "tidak_hadir" THEN 1 ELSE 0 END) as total_tidak_hadir
            ');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('pelayan.id_ibadah', $id_ibadah);
            }
            if ($tugas !== null && $tugas !== '' && $tugas !== 'null') {
                $this->builder->where('pelayan.tugas', $tugas);
            }
            if ($status !== null && $status !== '' && $status !== 'null') {
                $this->builder->where('pelayan.status', $status);
            }
            
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getStatistik error: ' . $e->getMessage());
            return null;
        }
    }

    public function getStatusCount($id_ibadah = null, $tugas = null)
    {
        try {
            $this->builder = $this->db->table('pelayan');
            $this->builder->select('pelayan.status, COUNT(*) as total');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('pelayan.id_ibadah', $id_ibadah);
            }
            if ($tugas !== null && $tugas !== '' && $tugas !== 'null') {
                $this->builder->where('pelayan.tugas', $tugas);
            }
            
            $this->builder->groupBy('pelayan.status');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getStatusCount error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTugasCount($id_ibadah = null, $status = null)
    {
        try {
            $this->builder = $this->db->table('pelayan');
            $this->builder->select('pelayan.tugas, COUNT(*) as total');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            
            if ($id_ibadah !== null && $id_ibadah !== '' && $id_ibadah !== 'null') {
                $this->builder->where('pelayan.id_ibadah', $id_ibadah);
            }
            if ($status !== null && $status !== '' && $status !== 'null') {
                $this->builder->where('pelayan.status', $status);
            }
            
            $this->builder->groupBy('pelayan.tugas');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getTugasCount error: ' . $e->getMessage());
            return [];
        }
    }
}