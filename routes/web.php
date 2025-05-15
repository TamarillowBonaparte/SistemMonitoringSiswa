<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataGuruController;
use App\Http\Controllers\DataPelajaranController;
use App\Http\Controllers\DataSiswaOrtuController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\LaporanAbsensiController;
use App\Http\Controllers\ListPelanggaranController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginPageController;
use App\Http\Controllers\NilaiAkhirController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UjianController;
use App\Models\ListPelanggaran;
use App\Models\NilaiAkhir;
use Illuminate\Support\Facades\Route;

// Halaman Login & Authentication Routes
Route::get('/', [LoginPageController::class, 'index'])->name('login');
Route::post('/', [LoginPageController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginPageController::class, 'logout'])->name('logout');

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/update-filter', [DashboardController::class, 'updateFilter']);
    Route::get('/chart/pelanggaran', [DashboardController::class, 'chartPelanggaran'])->name('chart.pelanggaran');

    Route::middleware(['role:admin,bk'])->group(function () {
        Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran');
        Route::post('/pelanggaran', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
        Route::get('/listpelanggaran',[PelanggaranController::class, 'listPelanggaran'])->name('Pelanggaran.list');
        Route::get('/pelanggaran/detail', [PelanggaranController::class, 'detail'])->name('pelanggaran.detail');
        Route::put('/pelanggaran/update', [PelanggaranController::class, 'update'])->name('pelanggaran.update');
        Route::delete('/pelanggaran/destroy', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');
        Route::get('/pelanggaran/stats', [PelanggaranController::class, 'getViolationStats'])->name('pelanggaran.stats');
        Route::get('/pelanggaran/trends', [PelanggaranController::class, 'getTrendStats'])->name('pelanggaran.trends');

        Route::get('/datapelanggaran', [ListPelanggaranController::class, 'index'])->name('datapelanggaran');
        Route::post('/listpelanggaran', [ListPelanggaranController::class, 'store'])->name('datapelanggaran.store');
        Route::put('/listpelanggaran/{id}', [ListPelanggaranController::class, 'update'])->name('datapelanggaran.update');
        Route::delete('/listpelanggaran/{id}', [ListPelanggaranController::class, 'destroy'])->name('datapelanggaran.destroy');


    });


    Route::get('/test-role', function () {
            return response()->json([
                'role_id' => auth()->user()->roleuser->id_roleuser,
                'roles' => ['guru'],
            ]);
        })->middleware('role:guru');

    // Routes accessible by Guru
    Route::middleware(['role:guru'])->group(function () {
        // Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
        // Route::get('/informasi/{id}/edit', [InformasiController::class, 'edit'])->name('informasi.edit');
        // Route::post('/informasi', [InformasiController::class, 'store'])->name('informasi.store');
        // Route::put('/informasi/{id}', [InformasiController::class, 'update'])->name('informasi.update');
        // Route::delete('/informasi/{id}', [InformasiController::class, 'destroy'])->name('informasi.destroy');


        // // Tugas
        // Route::get('/tugas', [TugasController::class, 'index'])->name('tugas');
        // Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
        // Route::get('/lihat-pdf', [TugasController::class, 'lihatPdf'])->name('lihat.pdf');
        // Route::delete('/tugas/{id}', [TugasController::class, 'destroy'])->name('tugas.destroy');
        // Route::put('/tugas/update/{id}', [TugasController::class, 'update'])->name('tugas.update');
        // Route::delete('/tugas/delete/{id_tugas}', [TugasController::class, 'destroy'])->name('tugas.destroy');

        // // Ujian
        // Route::get('/ujian',[UjianController::class, 'index'])->name('ujian');
        // Route::post('/ujian/store', [UjianController::class, 'store'])->name('ujian.store');
        // Route::get('/ujian/edit/{id}', [UjianController::class, 'edit'])->name('ujian.edit');
        // Route::put('/ujian/update/{id}', [UjianController::class, 'update'])->name('ujian.update');
        // Route::delete('/ujian/destroy/{id}', [UjianController::class, 'destroy'])->name('ujian.destroy');

        // // Nilai Akhir
        // Route::get('/nilaiakhir', [NilaiAkhirController::class, 'index'])->name('nilaiakhir');
        // Route::post('/nilai-akhir/store', [NilaiAkhirController::class, 'store'])->name('nilaiakhir.store');
        // Route::get('/getSiswaByKelas/{id}', [NilaiAkhirController::class, 'getSiswaByKelas']);
        // Route::get('/getMapelByKelas/{id_kelas}', [NilaiAkhirController::class, 'getMapelByKelas']);

        // // Lokasi
        // Route::get('/lokasi', [LocationController::class, 'index'])->name('lokasi');
        // Route::post('/locations.store', [LocationController::class, 'store'])->name('locations.store');
        // Route::get('/locations.delete', [LocationController::class, 'destroy'])->name('locations.destroy');
        // Route::get('/locations.update', [LocationController::class, 'update'])->name('locations.update');
        // Route::get('/locations.edit', [LocationController::class, 'edit'])->name('locations.edit');


        // // Absensi
        // Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
        // Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi');
        // Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        // Route::put('/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
        // Route::get('/getSiswaByKelas/{idKelas}', [AbsensiController::class, 'getSiswaByKelas']);
        // Route::get('/getMapelByKelas/{idKelas}', [AbsensiController::class, 'getMapelByKelas']);
        // // Route::put('/absensi/{id}/keterangan', [AbsensiController::class, 'updateketerangan'])->name('absensi.keterangan.update');
        // Route::post('/absensi/keterangan/update/{id}', [AbsensiController::class, 'updateketerangan'])->name('absensi.keterangan.update');
        // Route::put('/absensi/validasi/massal', [AbsensiController::class, 'validasiMassal'])->name('absensi.validasi.massal');
        // Route::post('/absensi/selesai', [AbsensiController::class, 'selesaikanValidasi'])->name('absensi.selesai');
        // Route::get('/get-pelajaran-by-kelas', [LaporanAbsensiController::class, 'getPelajaranByKelas'])->name('get.pelajaran.by.kelas');

        // // Laporan Absensi
        // Route::get('/laporanabsensi', [LaporanAbsensiController::class, 'index'])->name('absensi.laporan');
        // Route::get('/laporan-absensi', [LaporanAbsensiController::class, 'index'])->name('laporan.absensi');
        // Route::get('/absensi/cetak', [LaporanAbsensiController::class, 'cetakLaporan'])->name('absensi.cetak');

    });

    // Admin Only Routes
    Route::middleware(['role:admin,guru,bk',])->group(function () {
        // Data User Management
        Route::get('/datauser', [DataUserController::class, 'index'])->name('datauser');
        Route::post('/datauser', [DataUserController::class, 'store'])->name('datauser.store');
        Route::put('/datauser/{id}', [DataUserController::class, 'update'])->name('datauser.update');
        Route::delete('/datauser/{id}', [DataUserController::class, 'destroy'])->name('datauser.destroy');

        // Data Guru & Kelas
        Route::get('/dataguru', [DataGuruController::class, 'index'])->name('dataguru');
        Route::post('/store-guru', [DataGuruController::class, 'tambahGuru'])->name('tambahguru');
        Route::match(['get', 'post'],'/edit{id}', [DataGuruController::class, 'edit']);
        Route::get('/delete{id}', [DataGuruController::class, 'delete']);
        Route::post('/kelas/tambah', [DataGuruController::class, 'storeKelas'])->name('kelas.tambah');
        Route::match(['get', 'post'],'/kelas/edit{id}', [DataGuruController::class, 'editKelas']);
        Route::get('/delete/kelas{id}', [DataGuruController::class, 'deletekelas']);
        Route::get('/delete/jurusan/{id}', [DataGuruController::class, 'deleteJurusan']);


        // Data Pelajaran
        Route::get('/datapelajaran', [DataPelajaranController::class, 'index'])->name('datapelajaran');
        Route::post('/kodepembelajaran', [DataPelajaranController::class, 'storekodepelajaran'])->name('storekodepelajaran');
        Route::put('/editkodepelajaran/{id}', [DataPelajaranController::class, 'editkodepelajaran'])->name('editkodepembelajaran');
        Route::get('/delete/kodepelajaran/{id}', [DataPelajaranController::class, 'deletekodepelajaran'])->name('deletekodepelajaran');

        Route::post('/storepelajaran', [DataPelajaranController::class, 'storepelajaran'])->name('storepelajaran');
        Route::PUT('/pelajaran/edit/{id}', [DataPelajaranController::class, 'editPelajaran']);
        Route::get('/delete/pelajaran/{id}', [DataPelajaranController::class, 'deletepelajaran']);

        Route::post('/storejampelajaran', [DataPelajaranController::class, 'storejampelajaran'])->name('storejampelajaran');
        Route::put('/edit/jampelajaran/{id}', [DataPelajaranController::class, 'editJamPelajaran'])->name('editjampelajaran');
        Route::get('/delete/jampelajaran/{id}', [DataPelajaranController::class, 'deleteJamPelajaran'])->name('delete.jampelajaran');

        Route::post('/storehari', [DataPelajaranController::class, 'storehari'])->name('storehari');
        Route::put('/hari/{id}', [DataPelajaranController::class, 'update'])->name('hari.update');
        Route::get('/delete/hari{id}', [DataPelajaranController::class, 'destroyhari']);

        // Data Siswa & Orangtua
        Route::get('/datasiswaortu', [DataSiswaOrtuController::class, 'index'])->name('datasiswaortu');
        Route::post('/storesiswa', [DataSiswaOrtuController::class, 'storesiswa'])->name('storesiswa');
        Route::post('/siswa/update/{id}', [DataSiswaOrtuController::class, 'update'])->name('siswa.update');
        Route::get('/siswa/destroy/{id}', [DataSiswaOrtuController::class, 'destroy']);
        Route::post('/siswa/import', [DataSiswaOrtuController::class, 'importExcel'])->name('siswa.import');
        Route::get('/siswa/export', [DataSiswaOrtuController::class, 'exportExcel'])->name('siswa.export');
        Route::delete('/siswa/destroy-all', [DataSiswaOrtuController::class, 'destroyAll'])->name('siswa.destroyAll');


         // Jadwal Pelajaran
        Route::get('/jadwalpelajaran', [JadwalPelajaranController::class, 'index'])->name('jadwalpelajaran');
        Route::get('/jadwal', [JadwalPelajaranController::class, 'getJadwal'])->name('getJadwal');
        Route::post('/jadwalpelajaran', [JadwalPelajaranController::class, 'store'])->name('jadwal.store');
        Route::post('/update-pembelajaran', [JadwalPelajaranController::class, 'updatePembelajaran'])->name('updatePembelajaran');

        // Pelanggaran
        // Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran');
        // Route::post('/pelanggaran', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
        // Route::get('/listpelanggaran',[PelanggaranController::class, 'listPelanggaran'])->name('Pelanggaran.list');

        // Informasi
        Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
        Route::get('/informasi/{id}/edit', [InformasiController::class, 'edit'])->name('informasi.edit');
        Route::post('/informasi', [InformasiController::class, 'store'])->name('informasi.store');
        Route::put('/informasi/{id}', [InformasiController::class, 'update'])->name('informasi.update');
        Route::delete('/informasi/{id}', [InformasiController::class, 'destroy'])->name('informasi.destroy');

        // Tugas
        Route::get('/tugas', [TugasController::class, 'index'])->name('tugas');
        Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
        Route::get('/lihat-pdf', [TugasController::class, 'lihatPdf'])->name('lihat.pdf');
        Route::delete('/tugas/{id}', [TugasController::class, 'destroy'])->name('tugas.destroy');
        Route::put('/tugas/update/{id}', [TugasController::class, 'update'])->name('tugas.update');
        Route::delete('/tugas/delete/{id_tugas}', [TugasController::class, 'destroy'])->name('tugas.destroy');

        // Ujian
        Route::get('/ujian',[UjianController::class, 'index'])->name('ujian');
        Route::post('/ujian/store', [UjianController::class, 'store'])->name('ujian.store');
        Route::get('/ujian/edit/{id}', [UjianController::class, 'edit'])->name('ujian.edit');
        Route::put('/ujian/update/{id}', [UjianController::class, 'update'])->name('ujian.update');
        Route::delete('/ujian/destroy/{id}', [UjianController::class, 'destroy'])->name('ujian.destroy');

        // Nilai Akhir
        Route::get('/nilaiakhir', [NilaiAkhirController::class, 'index'])->name('nilaiakhir');
        Route::post('/nilai-akhir/store', [NilaiAkhirController::class, 'store'])->name('nilaiakhir.store');
        Route::get('/getSiswaByKelas/{id}', [NilaiAkhirController::class, 'getSiswaByKelas']);
        Route::get('/getMapelByKelas/{id_kelas}', [NilaiAkhirController::class, 'getMapelByKelas']);
        Route::delete('/nilaiakhir/{id}', [NilaiAkhirController::class, 'destroy'])->name('nilaiakhir.destroy');
        Route::put('/nilaiakhir/{id}', [NilaiAkhirController::class, 'update'])->name('nilaiakhir.update');


        // Lokasi
        Route::get('/lokasi', [LocationController::class, 'index'])->name('lokasi');
        Route::post('/locations.store', [LocationController::class, 'store'])->name('locations.store');
        Route::put('/locations/{id}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');



        // Absensi
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
        Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi');
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::put('/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
        Route::get('/getSiswaByKelas/{idKelas}', [AbsensiController::class, 'getSiswaByKelas']);
        Route::get('/getMapelByKelas/{idKelas}', [AbsensiController::class, 'getMapelByKelas']);

        // Route::put('/absensi/{id}/keterangan', [AbsensiController::class, 'updateketerangan'])->name('absensi.keterangan.update');
        Route::post('/absensi/keterangan/update/{id}', [AbsensiController::class, 'updateketerangan'])->name('absensi.keterangan.update');
        Route::put('/absensi/validasi/massal', [AbsensiController::class, 'validasiMassal'])->name('absensi.validasi.massal');
        Route::post('/absensi/selesai', [AbsensiController::class, 'selesaikanValidasi'])->name('absensi.selesai');
        Route::get('/get-pelajaran-by-kelas', [LaporanAbsensiController::class, 'getPelajaranByKelas'])->name('get.pelajaran.by.kelas');
        Route::delete('absensi/{id}', [App\Http\Controllers\AbsensiController::class, 'destroy'])->name('absensi.destroy');

        // Laporan Absensi
        Route::get('/laporanabsensi', [LaporanAbsensiController::class, 'index'])->name('absensi.laporan');
        Route::get('/laporan-absensi', [LaporanAbsensiController::class, 'index'])->name('laporan.absensi');
        Route::get('/absensi/cetak', [LaporanAbsensiController::class, 'cetakLaporan'])->name('absensi.cetak');


        //tabah jurusan
        Route::post('/jurusan/store', [DataGuruController::class, 'storeJurusan'])->name('jurusan.store');
        Route::put('/jurusan/update/{id}', [DataGuruController::class, 'updateJurusan'])->name('jurusan.update');
        Route::get('/jurusan/delete/{id}', [DataGuruController::class, 'deleteJurusan'])->name('jurusan.delete');

    });
});
