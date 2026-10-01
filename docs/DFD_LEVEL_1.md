# Analisis dan DFD Level 1 SIAG3

## Ringkasan sistem

SIAG3 adalah aplikasi administrasi gereja berbasis CodeIgniter 4. Cakupan yang tampak pada kode meliputi autentikasi dan hak akses, organisasi gereja, keluarga dan jemaat, jadwal ibadah dan penugasan pelayan, absensi QR/manual, persembahan beserta persetujuannya, permohonan sakramen dengan lampiran, dashboard analitik, live report, dan laporan cetak.

DFD Level 1 dibuat dari implementasi aktif pada `app/Config/Routes.php`, controller di `app/Controllers`, model di `app/Models`, aturan akses pada `app/Helpers/permission_helper.php`, migration, dan `siag3.sql`. Seluruh artefak diagram menggunakan tampilan monokrom hitam-putih tanpa warna dekoratif.

## Entitas eksternal

| ID | Entitas | Interaksi utama |
|---|---|---|
| E1 | Jemaat / Publik | Registrasi/login, pencarian kartu anggota dan QR, permohonan sakramen, status permohonan, serta informasi ibadah live. |
| E2 | Petugas Administrasi | Admin area, pendeta, dan sekretaris mengelola data master, ibadah/pelayan, absensi, sakramen, dashboard, dan laporan sesuai izin. |
| E3 | Master / Admin Master | Mengelola pengguna, role, status akun, permission modul, dan cakupan akses. |
| E4 | Kasir / Bendahara | Memasukkan data persembahan, memeriksa/menyetujui, dan menerima rekap keuangan. |
| E5 | Ketua 5 | Memeriksa dan menyetujui data ibadah sebelum status ibadah dapat diselesaikan. |
| E6* | Pemakai Informasi | Duplikasi logis E1–E5 sebagai penerima dashboard, live report, kartu/QR, rekap, dan laporan cetak; digunakan untuk menjaga keterbacaan diagram. |

## Dekomposisi proses

| Proses | Nama | Tanggung jawab berdasarkan kode |
|---|---|---|
| 1.0 | Kelola Akses & Akun | Registrasi akun jemaat, autentikasi, sesi, profil, pengguna, role, dan permission. Registrasi mencocokkan `no_anggota` serta nomor HP dengan jemaat aktif. |
| 2.0 | Kelola Data Jemaat | CRUD cabang gereja, sektor pelayanan, keluarga, dan jemaat; menghasilkan nomor anggota, QR, dan kartu anggota. |
| 3.0 | Kelola Ibadah & Pelayan | Jadwal/status ibadah, lingkup cabang, penugasan pelayan, dan approval Ketua 5. Status `selesai` ditolak bila approval Ketua 5 belum `approved`. |
| 4.0 | Catat Absensi | Absensi manual/QR, validasi jemaat aktif dan akses cabang, pencegahan absensi ganda per ibadah, serta pembaruan jumlah hadir/total peserta pada ibadah. |
| 5.0 | Kelola Persembahan | Entri nominal/jenis/metode, relasi jemaat dan ibadah, workflow draft–approved, serta pencatatan pemberi persetujuan dan waktunya. |
| 6.0 | Kelola Sakramen | Permohonan sakramen, status, catatan pemohon/petugas, kontrol kepemilikan data, dan lampiran PDF/JPG/PNG. |
| 7.0 | Sajikan Informasi & Laporan | Dashboard, analitik kehadiran/persembahan, live report, filter, rekap, dan keluaran cetak sesuai otorisasi dan cabang. |

## Data store logis

| ID | Data store | Implementasi aktual |
|---|---|---|
| D1 | Akses & Pengguna | `user`, `modules`, `permissions` |
| D2 | Organisasi & Jemaat | `cabang_gereja`, `sektor_pelayanan`, `keluarga`, `jemaat` |
| D3 | Ibadah & Pelayan | `ibadah`, `pelayan` |
| D4 | Absensi | `absensi` |
| D5 | Persembahan | `persembahan` |
| D6 | Permohonan Sakramen | `waitlist_sakramen` |
| D7 | Lampiran Sakramen | `writable/uploads/sakramen` |

## Aturan penting yang memengaruhi aliran data

- Akses route dibatasi oleh filter login dan role, lalu diperketat lagi oleh permission modul serta cakupan cabang pada controller/helper.
- Pendaftaran akun jemaat hanya berhasil bila nomor anggota dan nomor HP cocok dengan data jemaat aktif, dan satu jemaat hanya boleh mempunyai satu akun.
- Scan QR menggunakan nomor anggota, menolak jemaat tidak aktif atau duplikasi absensi pada ibadah yang sama, lalu memperbarui agregat kehadiran di data ibadah.
- Data persembahan yang sudah disetujui tidak dapat diedit atau dihapus melalui workflow ibadah; dashboard menjumlahkan persembahan berstatus `approved`.
- Ibadah tidak dapat dipindahkan ke status `selesai` sebelum approval Ketua 5 berstatus `approved`.
- Jemaat hanya dapat melihat permohonan sakramennya sendiri; petugas yang diizinkan dapat memproses status, catatan admin, dan lampiran.

## Artefak

- `dfd-level-1.drawio` — sumber diagram yang dapat diedit di diagrams.net/draw.io.
- `dfd-level-1.svg` — pratinjau vektor untuk dokumen atau browser.
- `dfd-level-1.png` — pratinjau raster siap pakai.
