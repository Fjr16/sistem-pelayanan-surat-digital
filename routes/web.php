<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::delete('/logout', [AuthenticateController::class, 'destroy'])->name('logout');

    // dashboard
    Route::get('/dashboard', [MasterController::class, 'dashboard'])->name('dashboard');

    Route::middleware('admin')->group(function () {
        Route::get('/user/get/data', [UserController::class, 'getData'])->name('user.getData');
        Route::post('/get/detail/user', [UserController::class, 'getDetailUser'])->name('user.getDetail');
        // Pengguna
        Route::get('/master/aktor/pengguna', [UserController::class, 'index'])->name('master/aktor/pengguna.index');
        Route::post('/master/aktor/pengguna/store', [UserController::class, 'store'])->name('master/aktor/pengguna.store');
        Route::put('/master/aktor/pengguna/update/{id}', [UserController::class, 'update'])->name('master/aktor/pengguna.update')->withoutMiddleware('admin');
        Route::delete('/master/aktor/pengguna/destroy/{id}', [UserController::class, 'destroy'])->name('master/aktor/pengguna.destroy');

        Route::delete('/pengguna/restore/forceDelete/{id}', [UserController::class, 'forceDelete'])->name('pengguna/restore.forceDelete');
        Route::put('/pengguna/restore/{id}', [UserController::class, 'restore'])->name('pengguna.restore');

        Route::get('/profile', [UserController::class, 'profile'])->name('profile')->withoutMiddleware('admin');
    });
    // Route::middleware('admin')->prefix('master/')->group(function () {
        Route::get('penduduk', [MasterController::class, 'indexPenduduk'])->name('penduduk.index');
        Route::get('penduduk/create', [MasterController::class, 'createPenduduk'])->name('penduduk.create');
        Route::post('penduduk/store', [MasterController::class, 'storePenduduk'])->name('penduduk.store');
        Route::delete('penduduk/destroy', [MasterController::class, 'destroyPenduduk'])->name('penduduk.destroy');

        // Route::get('surat', [MasterController::class, 'indexSurat'])->name('surat.index');
        // Route::get('surat/create', [MasterController::class, 'createSurat'])->name('surat.create');
        // Route::post('surat/store', [MasterController::class, 'storeSurat'])->name('surat.store');
        // Route::delete('surat/destroy', [MasterController::class, 'destroySurat'])->name('surat.destroy');

        Route::get('jenis/surat', [MasterController::class, 'indexMail'])->name('jenis/surat.index');
        Route::get('jenis/surat/create', [MasterController::class, 'createMail'])->name('jenis/surat.create');
        Route::post('jenis/surat/store', [MasterController::class, 'storeMail'])->name('jenis/surat.store');
        Route::delete('jenis/surat/destroy/{id}', [MasterController::class, 'destroyMail'])->name('jenis/surat.destroy');

        // Route::get('template/surat', [MasterController::class, 'indexTemplateSurat'])->name('surat/template.index');
        // Route::get('template/surat/create', [MasterController::class, 'createTemplateSurat'])->name('surat/template.create');
        // Route::get('template/surat/store', [MasterController::class, 'storeTemplateSurat'])->name('surat/template.store');
        // Route::delete('template/surat/destroy', [MasterController::class, 'destroyTemplateSurat'])->name('surat/template.destroy');
    // });

    // Route::middleware('kepsek')->group(function(){
        // rekapitulasi
        // Route::get('rekapitulasi/presensi/siswa', [RekapitulasiController::class, 'indexSiswa'])->name('rekap/presensi/siswa.index');
        // Route::post('rekapitulasi/presensi/siswa', [RekapitulasiController::class, 'presensiSiswaExcel'])->name('rekap/presensi/siswa.excel')->withoutMiddleware('kepsek');
        // Route::get('rekapitulasi/presensi/akhir', [RekapitulasiController::class, 'indexAkhir'])->name('rekap/presensi/akhir.index');
        // Route::post('rekapitulasi/presensi/akhir', [RekapitulasiController::class, 'presensiAkhirExcel'])->name('rekap/presensi/akhir.excel');
    // });

    Route::get('/misc/forbidden', function () {
        return view('pages.misc-page.forbidden');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticateController::class, 'index'])->name('login');
    Route::post('/login', [AuthenticateController::class, 'store'])->name('login');
});
Route::get('/misc/error', function () {
    return view('pages.misc-page.error');
});



