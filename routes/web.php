<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\LessonScheduleController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MyClassController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::delete('/logout', [AuthenticateController::class, 'destroy'])->name('logout');

    // dashboard
    Route::get('/dashboard', [MasterController::class, 'dashboard'])->name('dashboard');

    // hari
    Route::get('/master/hari', [MasterController::class, 'indexDays'])->name('master/hari.index');
    Route::post('/master/hari/store', [MasterController::class, 'storeDays'])->name('master/hari.store');
    Route::get('/master/hari/edit/{id}', [MasterController::class, 'editDays'])->name('master/hari.edit');
    Route::put('/master/hari/update/{id}', [MasterController::class, 'updateDays'])->name('master/hari.update');
    Route::delete('/master/hari/destroy/{id}', [MasterController::class, 'destroyDays'])->name('master/hari.destroy');
        

    Route::middleware('admin')->group(function () {
        // Pengguna
        Route::get('/master/aktor/pengguna', [UserController::class, 'index'])->name('master/aktor/pengguna.index');
        Route::post('/master/aktor/pengguna/store', [UserController::class, 'store'])->name('master/aktor/pengguna.store');
        Route::put('/master/aktor/pengguna/update/{id}', [UserController::class, 'update'])->name('master/aktor/pengguna.update')->withoutMiddleware('admin');
        Route::delete('/master/aktor/pengguna/destroy/{id}', [UserController::class, 'destroy'])->name('master/aktor/pengguna.destroy');
        
        Route::delete('/pengguna/restore/forceDelete/{id}', [UserController::class, 'forceDelete'])->name('pengguna/restore.forceDelete');
        Route::put('/pengguna/restore/{id}', [UserController::class, 'restore'])->name('pengguna.restore');
        
        Route::get('/profile', [UserController::class, 'profile'])->name('profile')->withoutMiddleware('admin');
    });    
        
    // Route::middleware('kepsek')->group(function(){
        // rekapitulasi
        Route::get('rekapitulasi/presensi/siswa', [RekapitulasiController::class, 'indexSiswa'])->name('rekap/presensi/siswa.index');
        Route::post('rekapitulasi/presensi/siswa', [RekapitulasiController::class, 'presensiSiswaExcel'])->name('rekap/presensi/siswa.excel')->withoutMiddleware('kepsek');
        Route::get('rekapitulasi/presensi/akhir', [RekapitulasiController::class, 'indexAkhir'])->name('rekap/presensi/akhir.index');
        Route::post('rekapitulasi/presensi/akhir', [RekapitulasiController::class, 'presensiAkhirExcel'])->name('rekap/presensi/akhir.excel');
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



