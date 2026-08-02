<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DashboardDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $faker = \Faker\Factory::create('id_ID');

        // Check if there are any sectors, if not, wait.
        $sektorQuery = $db->query("SELECT id FROM sektor_pelayanan LIMIT 5");
        $sektors = $sektorQuery->getResultArray();
        
        if (empty($sektors)) {
            echo "Error: No Sektor Pelayanan found. Please add at least one Sektor Pelayanan first.\n";
            return;
        }

        // Get some Jemaat IDs or create dummy ones for Absensi
        $jemaatQuery = $db->query("SELECT id FROM jemaat LIMIT 20");
        $jemaatList = $jemaatQuery->getResultArray();
        if (empty($jemaatList)) {
            echo "Error: No Jemaat found. Please add Jemaat data first to bind absensi to.\n";
            return;
        }

        echo "Seeding Dashboard Analytics Data (Ibadah, Absensi, Persembahan)...\n";

        $ibadahTypes = ['Ibadah Minggu Umum', 'Ibadah Pelkat PKB', 'Ibadah Pelkat PKP', 'Ibadah Pelkat PT', 'Ibadah Pemuda'];
        
        // Generate data for the past 12 weeks
        for ($week = 11; $week >= 0; $week--) {
            // Pick a Sunday for this week
            $date = date('Y-m-d', strtotime("-$week weeks sunday"));
            
            foreach ($sektors as $sektor) {
                // Generate 2 Ibadah sessions per sector every Sunday
                for ($session = 1; $session <= 2; $session++) {
                    $waktu = $session == 1 ? '07:00:00' : '09:00:00';
                    $type = $ibadahTypes[array_rand($ibadahTypes)];
                    
                    // 1. Insert Ibadah
                    $dataIbadah = [
                        'nama_ibadah' => $type,
                        'tanggal' => $date,
                        'waktu' => $waktu,
                        'id_sektor_pelayanan' => $sektor['id'],
                        'keterangan' => 'Seeder Generated',
                        'approval_ketua5' => 'approved' // Automatically approved to show on dashboard
                    ];
                    $db->table('ibadah')->insert($dataIbadah);
                    $idIbadah = $db->insertID();

                    // 2. Insert Absensi (Random between 10 to 18 people)
                    $absensiCount = rand(10, count($jemaatList));
                    shuffle($jemaatList);
                    $subset = array_slice($jemaatList, 0, $absensiCount);
                    
                    $absensiBatch = [];
                    foreach ($subset as $j) {
                        $absensiBatch[] = [
                            'id_ibadah' => $idIbadah,
                            'id_jemaat' => $j['id'],
                            'waktu_hadir' => $date . ' ' . $waktu,
                            'status_kehadiran' => 'hadir'
                        ];
                    }
                    if (!empty($absensiBatch)) {
                        $db->table('absensi')->insertBatch($absensiBatch);
                    }

                    // 3. Insert Persembahan
                    // Generate realistic offering amounts
                    $persembahanBatch = [
                        [
                            'id_ibadah' => $idIbadah,
                            'id_kategori' => 1, // Usually 1: Kantong Putih (Pemeliharaan)
                            'jumlah' => rand(500, 2500) * 1000,
                            'status_approval' => 'approved',
                            'keterangan' => 'Seeder'
                        ],
                        [
                            'id_ibadah' => $idIbadah,
                            'id_kategori' => 2, // Usually 2: Kantong Cokelat (Pelayanan)
                            'jumlah' => rand(300, 1500) * 1000,
                            'status_approval' => 'approved',
                            'keterangan' => 'Seeder'
                        ],
                        [
                            'id_ibadah' => $idIbadah,
                            'id_kategori' => 3, // Usually 3: Amplop Khusus
                            'jumlah' => rand(100, 1000) * 1000,
                            'status_approval' => 'approved',
                            'keterangan' => 'Seeder'
                        ]
                    ];
                    $db->table('persembahan')->insertBatch($persembahanBatch);
                }
            }
        }
        
        echo "Successfully seeded dummy Analytics Data for the past 12 weeks!\n";
    }
}
