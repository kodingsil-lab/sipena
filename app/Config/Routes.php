<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'PublicPortal::index');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->post('/logout', 'Auth::logout', ['filter' => 'auth']);
$routes->get('/publik/peraturan', 'PublicPortal::peraturan');
$routes->get('/publik/kebijakan-mutu', 'PublicPortal::kebijakanMutu');
$routes->get('/publik/kebijakan-spmi', 'PublicPortal::kebijakanSpmi');
$routes->get('/publik/pedoman-ppepp', 'PublicPortal::pedomanPpepp');
$routes->get('/publik/audit-mutu-internal', 'PublicPortal::auditMutuInternal');
$routes->get('/publik/standar-mutu', 'PublicPortal::standarMutu');
$routes->get('/publik/standar-mutu/detail/(:num)', 'PublicPortal::standarDetail/$1');

// API Integrasi Dokumen Standar (untuk sinkronisasi aplikasi AMI eksternal)
$routes->group('api/v1', static function ($routes) {
    $routes->get('standar', 'Api\StandarApiController::index');
    $routes->get('standar/changes', 'Api\StandarApiController::changes');
    $routes->get('standar/(:num)', 'Api\StandarApiController::show/$1');
});

$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('/profil', 'ProfilController::index', ['filter' => 'auth']);
$routes->post('/profil/update', 'ProfilController::update', ['filter' => 'auth']);
$routes->get('/peraturan', 'Dokumen::peraturan', ['filter' => 'auth']);
$routes->get('/peraturan/tambah', 'Dokumen::tambahPeraturan', ['filter' => 'editor']);
$routes->post('/peraturan/simpan', 'Dokumen::simpanPeraturan', ['filter' => 'editor']);
$routes->get('/peraturan/detail/(:num)', 'Dokumen::detailPeraturan/$1', ['filter' => 'auth']);
$routes->get('/peraturan/edit/(:num)', 'Dokumen::editPeraturan/$1', ['filter' => 'editor']);
$routes->post('/peraturan/update/(:num)', 'Dokumen::updatePeraturan/$1', ['filter' => 'editor']);
$routes->post('/peraturan/hapus/(:num)', 'Dokumen::hapusPeraturan/$1', ['filter' => 'editor']);
$routes->get('/kebijakan-mutu', 'Dokumen::kebijakanMutu', ['filter' => 'auth']);
$routes->get('/kebijakan-mutu/tambah', 'Dokumen::tambahKebijakanMutu', ['filter' => 'editor']);
$routes->post('/kebijakan-mutu/simpan', 'Dokumen::simpanKebijakanMutu', ['filter' => 'editor']);
$routes->get('/kebijakan-mutu/detail/(:num)', 'Dokumen::detailKebijakanMutu/$1', ['filter' => 'auth']);
$routes->get('/kebijakan-mutu/edit/(:num)', 'Dokumen::editKebijakanMutu/$1', ['filter' => 'editor']);
$routes->post('/kebijakan-mutu/update/(:num)', 'Dokumen::updateKebijakanMutu/$1', ['filter' => 'editor']);
$routes->post('/kebijakan-mutu/hapus/(:num)', 'Dokumen::hapusKebijakanMutu/$1', ['filter' => 'editor']);
$routes->get('/kebijakan-spmi', 'Dokumen::kebijakanSpmi', ['filter' => 'auth']);
$routes->get('/kebijakan-spmi/tambah', 'Dokumen::tambahKebijakanSpmi', ['filter' => 'editor']);
$routes->post('/kebijakan-spmi/simpan', 'Dokumen::simpanKebijakanSpmi', ['filter' => 'editor']);
$routes->get('/kebijakan-spmi/detail/(:num)', 'Dokumen::detailKebijakanSpmi/$1', ['filter' => 'auth']);
$routes->get('/kebijakan-spmi/edit/(:num)', 'Dokumen::editKebijakanSpmi/$1', ['filter' => 'editor']);
$routes->post('/kebijakan-spmi/update/(:num)', 'Dokumen::updateKebijakanSpmi/$1', ['filter' => 'editor']);
$routes->post('/kebijakan-spmi/hapus/(:num)', 'Dokumen::hapusKebijakanSpmi/$1', ['filter' => 'editor']);
$routes->get('/pedoman-ppepp', 'Dokumen::ppepp', ['filter' => 'auth']);
$routes->get('/pedoman-ppepp/dokumen', 'Dokumen::dokumenPpepp', ['filter' => 'auth']);
$routes->get('/pedoman-ppepp/sop', 'Dokumen::sopPpepp', ['filter' => 'auth']);
$routes->get('/pedoman-ppepp/formulir', 'Dokumen::formulirPpepp', ['filter' => 'auth']);
$routes->get('/pedoman-ppepp/tambah', 'Dokumen::tambahPedomanPpepp', ['filter' => 'editor']);
$routes->post('/pedoman-ppepp/simpan', 'Dokumen::simpanPedomanPpepp', ['filter' => 'editor']);
$routes->get('/pedoman-ppepp/detail/(:num)', 'Dokumen::detailPedomanPpepp/$1', ['filter' => 'auth']);
$routes->get('/pedoman-ppepp/edit/(:num)', 'Dokumen::editPedomanPpepp/$1', ['filter' => 'editor']);
$routes->post('/pedoman-ppepp/update/(:num)', 'Dokumen::updatePedomanPpepp/$1', ['filter' => 'editor']);
$routes->post('/pedoman-ppepp/hapus/(:num)', 'Dokumen::hapusPedomanPpepp/$1', ['filter' => 'editor']);
$routes->get('/standar-mutu', 'Dokumen::standarMutu', ['filter' => 'auth']);
$routes->get('/standar-mutu/tambah', 'Dokumen::tambahStandarMutu', ['filter' => 'editor']);
$routes->post('/standar-mutu/simpan', 'Dokumen::simpanStandarMutu', ['filter' => 'editor']);
$routes->get('/standar-mutu/detail/(:num)', 'Dokumen::detailStandarMutu/$1', ['filter' => 'auth']);
$routes->get('/standar-mutu/edit/(:num)', 'Dokumen::editStandarMutu/$1', ['filter' => 'editor']);
$routes->post('/standar-mutu/update/(:num)', 'Dokumen::updateStandarMutu/$1', ['filter' => 'editor']);
$routes->post('/standar-mutu/hapus/(:num)', 'Dokumen::hapusStandarMutu/$1', ['filter' => 'editor']);
$routes->get('/standar-mutu/(:num)/dokumen', 'Dokumen::dokumenStandar/$1', ['filter' => 'auth']);
$routes->get('/standar-mutu/(:num)/dokumen/tambah', 'Dokumen::tambahDokumenStandar/$1', ['filter' => 'editor']);
$routes->post('/standar-mutu/(:num)/dokumen/simpan', 'Dokumen::simpanDokumenStandar/$1', ['filter' => 'editor']);

$routes->get('/dokumen-standar/detail/(:num)', 'Dokumen::detailDokumenStandar/$1', ['filter' => 'auth']);
$routes->get('/dokumen-standar/riwayat/(:num)', 'Dokumen::riwayatDokumenStandar/$1', ['filter' => 'auth']);
$routes->get('/dokumen-standar/edit/(:num)', 'Dokumen::editDokumenStandar/$1', ['filter' => 'editor']);
$routes->post('/dokumen-standar/update/(:num)', 'Dokumen::updateDokumenStandar/$1', ['filter' => 'editor']);
$routes->post('/dokumen-standar/hapus/(:num)', 'Dokumen::hapusDokumenStandar/$1', ['filter' => 'editor']);
$routes->get('/dokumen-standar/cetak/(:num)', 'Dokumen::cetakDokumenStandar/$1', ['filter' => 'auth']);
$routes->get('/dokumen-standar/pdf/(:num)', 'Dokumen::pdfDokumenStandar/$1', ['filter' => 'auth']);

$routes->get('/audit-mutu-internal', 'Dokumen::ami', ['filter' => 'auth']);
$routes->get('/audit-mutu-internal/tambah', 'Dokumen::tambahAmi', ['filter' => 'editor']);
$routes->post('/audit-mutu-internal/simpan', 'Dokumen::simpanAmi', ['filter' => 'editor']);
$routes->get('/audit-mutu-internal/detail/(:num)', 'Dokumen::detailAmi/$1', ['filter' => 'auth']);
$routes->get('/audit-mutu-internal/edit/(:num)', 'Dokumen::editAmi/$1', ['filter' => 'editor']);
$routes->post('/audit-mutu-internal/update/(:num)', 'Dokumen::updateAmi/$1', ['filter' => 'editor']);
$routes->post('/audit-mutu-internal/hapus/(:num)', 'Dokumen::hapusAmi/$1', ['filter' => 'editor']);

$routes->get('/master-data/jenis-standar', 'MasterStandar::jenisStandar', ['filter' => 'admin']);
$routes->post('/master-data/jenis-standar/simpan', 'MasterStandar::simpanJenisStandar', ['filter' => 'admin']);
$routes->get('/master-data/jenis-standar/edit/(:num)', 'MasterStandar::editJenisStandar/$1', ['filter' => 'admin']);
$routes->post('/master-data/jenis-standar/update/(:num)', 'MasterStandar::updateJenisStandar/$1', ['filter' => 'admin']);
$routes->post('/master-data/jenis-standar/hapus/(:num)', 'MasterStandar::hapusJenisStandar/$1', ['filter' => 'admin']);
$routes->get('/master-data/kategori-standar', 'MasterStandar::kategoriStandar', ['filter' => 'admin']);
$routes->post('/master-data/kategori-standar/simpan', 'MasterStandar::simpanKategoriStandar', ['filter' => 'admin']);
$routes->get('/master-data/kategori-standar/edit/(:num)', 'MasterStandar::editKategoriStandar/$1', ['filter' => 'admin']);
$routes->post('/master-data/kategori-standar/update/(:num)', 'MasterStandar::updateKategoriStandar/$1', ['filter' => 'admin']);
$routes->post('/master-data/kategori-standar/hapus/(:num)', 'MasterStandar::hapusKategoriStandar/$1', ['filter' => 'admin']);

$routes->get('/pengaturan/profil-institusi', 'MasterStandar::profilInstitusi', ['filter' => 'admin']);
$routes->post('/pengaturan/profil-institusi/simpan', 'MasterStandar::simpanProfilInstitusi', ['filter' => 'admin']);
$routes->get('/pengaturan/pengguna', 'MasterUser::index', ['filter' => 'admin']);
$routes->get('/pengaturan/pengguna/tambah', 'MasterUser::tambah', ['filter' => 'admin']);
$routes->post('/pengaturan/pengguna/simpan', 'MasterUser::simpan', ['filter' => 'admin']);
$routes->get('/pengaturan/pengguna/edit/(:num)', 'MasterUser::edit/$1', ['filter' => 'admin']);
$routes->post('/pengaturan/pengguna/update/(:num)', 'MasterUser::update/$1', ['filter' => 'admin']);
$routes->post('/pengaturan/pengguna/hapus/(:num)', 'MasterUser::hapus/$1', ['filter' => 'admin']);
$routes->get('/pengaturan/aplikasi', 'PengaturanAplikasiController::index', ['filter' => 'admin']);
$routes->post('/pengaturan/aplikasi/update', 'PengaturanAplikasiController::update', ['filter' => 'admin']);

// Alias lama (kompatibilitas link lama)
$routes->get('/master-standar/profil-institusi', 'MasterStandar::profilInstitusi', ['filter' => 'admin']);
$routes->post('/master-standar/profil-institusi/simpan', 'MasterStandar::simpanProfilInstitusi', ['filter' => 'admin']);
$routes->get('/master-data/profil-institusi', 'MasterStandar::profilInstitusi', ['filter' => 'admin']);
$routes->post('/master-data/profil-institusi/simpan', 'MasterStandar::simpanProfilInstitusi', ['filter' => 'admin']);
$routes->get('/master-data/users', 'MasterUser::index', ['filter' => 'admin']);
$routes->get('/master-data/users/tambah', 'MasterUser::tambah', ['filter' => 'admin']);
$routes->post('/master-data/users/simpan', 'MasterUser::simpan', ['filter' => 'admin']);
$routes->get('/master-data/users/edit/(:num)', 'MasterUser::edit/$1', ['filter' => 'admin']);
$routes->post('/master-data/users/update/(:num)', 'MasterUser::update/$1', ['filter' => 'admin']);
$routes->post('/master-data/users/hapus/(:num)', 'MasterUser::hapus/$1', ['filter' => 'admin']);
