<?php

namespace Config;

$routes = Services::routes();

// =============================================
// ROUTES TANPA LOGIN (PUBLIC)
// =============================================
$routes->get('/', 'Home::index');
$routes->post('home/search', 'Home::search');
$routes->get('login', 'Auth::login');
$routes->post('auth/loginProcess', 'Auth::loginProcess');
$routes->get('logout', 'Auth::logout');

// =============================================
// ROUTES DENGAN LOGIN (PROTECTED)
// =============================================
$routes->group('', ['filter' => 'login'], function($routes) {
    
    // =============================================
    // DASHBOARD - Semua User
    // =============================================
    $routes->get('dashboard', 'Dashboard::index');
    $routes->post('dashboard/getAnalytics', 'Dashboard::getAnalytics');
    
    // =============================================
    // PROFILE - Semua User
    // =============================================
    $routes->get('user/profile', 'User::profile');
    $routes->post('user/updateProfile', 'User::updateProfile');
    
    // =============================================
    // DATA MASTER - Admin Area, Pendeta, Sekretaris, Master
    // =============================================
    $routes->group('', ['filter' => 'role:admin_area,pendeta,sekretaris,master'], function($routes) {
        
        // SEKTOR PELAYANAN
        $routes->group('sektorpelayanan', function($routes) {
            $routes->get('/', 'SektorPelayanan::index');
            $routes->post('getData', 'SektorPelayanan::getData');
            $routes->post('save', 'SektorPelayanan::save');
            $routes->get('getById/(:num)', 'SektorPelayanan::getById/$1');
            $routes->post('delete/(:num)', 'SektorPelayanan::delete/$1');
        });
        
        // CABANG GEREJA
        $routes->group('cabanggereja', function($routes) {
            $routes->get('/', 'CabangGereja::index');
            $routes->post('getData', 'CabangGereja::getData');
            $routes->post('save', 'CabangGereja::save');
            $routes->get('getById/(:num)', 'CabangGereja::getById/$1');
            $routes->post('delete/(:num)', 'CabangGereja::delete/$1');
        });
        
        // WAITLIST SAKRAMEN
        $routes->group('waitlistsakramen', function($routes) {
            $routes->get('/', 'WaitlistSakramen::index');
            $routes->post('save', 'WaitlistSakramen::save');
            $routes->get('get/(:num)', 'WaitlistSakramen::get/$1');
            $routes->post('delete/(:num)', 'WaitlistSakramen::delete/$1');
        });
        
        // KELUARGA
        $routes->group('keluarga', function($routes) {
            $routes->get('/', 'Keluarga::index');
            $routes->post('getData', 'Keluarga::getData');
            $routes->post('save', 'Keluarga::save');
            $routes->get('getById/(:num)', 'Keluarga::getById/$1');
            $routes->post('delete/(:num)', 'Keluarga::delete/$1');
            $routes->get('getWilayah', 'Keluarga::getWilayah');
        });
        
        // JEMAAT
        $routes->group('jemaat', function($routes) {
            $routes->get('/', 'Jemaat::index');
            $routes->post('getData', 'Jemaat::getData');
            $routes->post('save', 'Jemaat::save');
            $routes->get('getById/(:num)', 'Jemaat::getById/$1');
            $routes->post('delete/(:num)', 'Jemaat::delete/$1');
            $routes->get('getKeluarga', 'Jemaat::getKeluarga');
            $routes->get('detail/(:num)', 'Jemaat::detail/$1');
            $routes->get('downloadQr/(:num)', 'Jemaat::downloadQr/$1');
            $routes->get('kartuAnggota/(:num)', 'Jemaat::kartuAnggota/$1');
        });
    });
    
    // =============================================
    // PELAYANAN - Admin Area, Pendeta, Sekretaris, Master
    // =============================================
    $routes->group('', ['filter' => 'role:admin_area,pendeta,sekretaris,master'], function($routes) {
        
        // Ibadah Routes
        $routes->group('ibadah', ['filter' => 'login'], function($routes) {
            $routes->get('/', 'Ibadah::index');
            $routes->post('getData', 'Ibadah::getData');
            $routes->post('save', 'Ibadah::save');
            $routes->get('getById/(:num)', 'Ibadah::getById/$1');
            $routes->post('delete/(:num)', 'Ibadah::delete/$1');
            $routes->get('getCabangGereja', 'Ibadah::getCabangGereja');
            $routes->get('detail/(:num)', 'Ibadah::detail/$1');
            $routes->get('absensi/(:num)', 'Ibadah::absensi/$1');
            $routes->get('absensi/scan/(:num)', 'Absensi::scanWithId/$1');
            $routes->get('live/(:num)', 'Ibadah::liveReport/$1');
            $routes->get('getLiveData/(:num)', 'Ibadah::getLiveData/$1');
            $routes->get('setpelayan/(:num)', 'Ibadah::setPelayan/$1');
            $routes->get('persembahan/(:num)', 'Ibadah::persembahanIbadah/$1'); 
            $routes->post('savePelayan', 'Ibadah::savePelayan');
            $routes->post('savePersembahanIbadah', 'Ibadah::savePersembahanIbadah'); 
            $routes->post('deletePelayan/(:num)', 'Ibadah::deletePelayan/$1');
            $routes->post('deletePersembahanIbadah/(:num)', 'Ibadah::deletePersembahanIbadah/$1'); 
            $routes->post('approvePersembahan/(:num)', 'Ibadah::approvePersembahan/$1');

        });
        
        // PELAYAN
        $routes->group('pelayan', function($routes) {
            $routes->get('/', 'Pelayan::index');
            $routes->post('getData', 'Pelayan::getData');
            $routes->post('save', 'Pelayan::save');
            $routes->get('getById/(:num)', 'Pelayan::getById/$1');
            $routes->post('delete/(:num)', 'Pelayan::delete/$1');
            $routes->get('getIbadah', 'Pelayan::getIbadah');
            $routes->get('getJemaat', 'Pelayan::getJemaat');
            $routes->get('getJemaatByIbadah/(:num)', 'Pelayan::getJemaatByIbadah/$1');
            $routes->get('detail/(:num)', 'Pelayan::detail/$1');
        });
        
        // ABSENSI
        $routes->group('absensi', function($routes) {
            $routes->get('/', 'Absensi::index');
            $routes->post('getData', 'Absensi::getData');
            $routes->post('save', 'Absensi::save');
            $routes->get('getById/(:num)', 'Absensi::getById/$1');
            $routes->post('delete/(:num)', 'Absensi::delete/$1');
            $routes->get('getIbadah', 'Absensi::getIbadah');
            $routes->get('getJemaat', 'Absensi::getJemaat');
            $routes->get('getJemaatByIbadah/(:num)', 'Absensi::getJemaatByIbadah/$1');
            $routes->get('scan', 'Absensi::scan');
            $routes->post('processScan', 'Absensi::processScan');
            $routes->get('detail/(:num)', 'Absensi::detail/$1');
        });
    });
    
    // =============================================
    // PERSEMBAHAN - Admin Area, Pendeta, Sekretaris, Bendahara, Master
    // =============================================
    $routes->group('', ['filter' => 'role:admin_area,pendeta,sekretaris,bendahara,master'], function($routes) {
        
        // PERSEMBAHAN
        $routes->group('persembahan', function($routes) {
            $routes->get('/', 'Persembahan::index');
            $routes->post('getData', 'Persembahan::getData');
            $routes->post('save', 'Persembahan::save');
            $routes->get('getById/(:num)', 'Persembahan::getById/$1');
            $routes->post('delete/(:num)', 'Persembahan::delete/$1');
            $routes->get('getIbadah', 'Persembahan::getIbadah');
            $routes->get('getJemaat', 'Persembahan::getJemaat');
            $routes->get('getJenis', 'Persembahan::getJenis');
            $routes->get('getMetode', 'Persembahan::getMetode');
            $routes->get('detail/(:num)', 'Persembahan::detail/$1');
            $routes->get('laporan', 'Persembahan::laporan');
            $routes->post('getLaporan', 'Persembahan::getLaporan');
        });
    });
    
    // =============================================
    // LAPORAN - Admin Area, Pendeta, Sekretaris, Master
    // =============================================
    $routes->group('', ['filter' => 'role:admin_area,pendeta,sekretaris,master'], function($routes) {
        
        // LAPORAN KELUARGA
        $routes->group('laporankeluarga', function($routes) {
            $routes->get('/', 'LaporanKeluarga::index');
            $routes->post('getData', 'LaporanKeluarga::getData');
            $routes->get('print/(:any)', 'LaporanKeluarga::print/$1');
        });
        
        // LAPORAN JEMAAT
        $routes->group('laporanjemaa', function($routes) {
            $routes->get('/', 'LaporanJemaat::index');
            $routes->post('getData', 'LaporanJemaat::getData');
            $routes->get('print/(:any)/(:any)/(:any)', 'LaporanJemaat::print/$1/$2/$3');
            $routes->get('print/(:any)/(:any)', 'LaporanJemaat::print/$1/$2');
            $routes->get('print/(:any)', 'LaporanJemaat::print/$1');
            $routes->get('print', 'LaporanJemaat::print');
        });
        
        // LAPORAN IBADAH
        $routes->group('laporanibadah', function($routes) {
            $routes->get('/', 'LaporanIbadah::index');
            $routes->post('getData', 'LaporanIbadah::getData');
            $routes->get('print/(:any)/(:any)/(:any)/(:any)', 'LaporanIbadah::print/$1/$2/$3/$4');
            $routes->get('print/(:any)/(:any)/(:any)', 'LaporanIbadah::print/$1/$2/$3');
            $routes->get('print/(:any)/(:any)', 'LaporanIbadah::print/$1/$2');
            $routes->get('print/(:any)', 'LaporanIbadah::print/$1');
            $routes->get('print', 'LaporanIbadah::print');
        });
        
        // LAPORAN ABSENSI
        $routes->group('laporanabsensi', function($routes) {
            $routes->get('/', 'LaporanAbsensi::index');
            $routes->post('getData', 'LaporanAbsensi::getData');
            $routes->get('print/(:any)/(:any)/(:any)', 'LaporanAbsensi::print/$1/$2/$3');
            $routes->get('print/(:any)/(:any)', 'LaporanAbsensi::print/$1/$2');
            $routes->get('print/(:any)', 'LaporanAbsensi::print/$1');
            $routes->get('print', 'LaporanAbsensi::print');
        });
        
        // LAPORAN PELAYAN
        $routes->group('laporanpelayan', function($routes) {
            $routes->get('/', 'LaporanPelayan::index');
            $routes->post('getData', 'LaporanPelayan::getData');
            $routes->get('print/(:any)/(:any)/(:any)', 'LaporanPelayan::print/$1/$2/$3');
            $routes->get('print/(:any)/(:any)', 'LaporanPelayan::print/$1/$2');
            $routes->get('print/(:any)', 'LaporanPelayan::print/$1');
            $routes->get('print', 'LaporanPelayan::print');
        });
    });
    
    // =============================================
    // LAPORAN PERSEMBAHAN - Admin Area, Pendeta, Sekretaris, Bendahara, Master
    // =============================================
    $routes->group('', ['filter' => 'role:admin_area,pendeta,sekretaris,bendahara,master'], function($routes) {
        
        // LAPORAN PERSEMBAHAN
        $routes->group('laporanpersembahan', function($routes) {
            $routes->get('/', 'LaporanPersembahan::index');
            $routes->post('getData', 'LaporanPersembahan::getData');
            $routes->get('print/(:any)/(:any)/(:any)', 'LaporanPersembahan::print/$1/$2/$3');
            $routes->get('print/(:any)/(:any)', 'LaporanPersembahan::print/$1/$2');
            $routes->get('print/(:any)', 'LaporanPersembahan::print/$1');
            $routes->get('print', 'LaporanPersembahan::print');
        });
    });
    
    // =============================================
    // USER MANAGEMENT - Hanya Master
    // =============================================
    $routes->group('user', ['filter' => 'role:master'], function($routes) {
        $routes->get('/', 'User::index');
        $routes->post('getData', 'User::getData');
        $routes->post('save', 'User::save');
        $routes->get('getById/(:num)', 'User::getById/$1');
        $routes->post('delete/(:num)', 'User::delete/$1');
        $routes->post('toggleStatus/(:num)', 'User::toggleStatus/$1');
        $routes->get('getJemaat', 'User::getJemaat');
        $routes->get('getWilayah', 'User::getWilayah');
        $routes->get('getRoles', 'User::getRoles');
    });

    // Permission Routes (Hanya Master)
$routes->group('permission', ['filter' => 'role:master'], function($routes) {
    $routes->get('/', 'Permission::index');
    $routes->get('getPermissions', 'Permission::getPermissions');
    $routes->post('save', 'Permission::save');
    $routes->get('getRolePermissions/(:any)', 'Permission::getRolePermissions/$1');
});
});

// =============================================
// ROUTES 404
// =============================================
$routes->set404Override(function() {
    return view('errors/html/error_404');
});