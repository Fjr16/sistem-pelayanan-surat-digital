<?php

use App\Enums\UserRole;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\LetterProcessController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\QrVerifyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubmissionLetterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::delete('/logout', [AuthenticateController::class, 'destroy'])->name('logout');

    // dashboard
    Route::get('/dashboard', [MasterController::class, 'dashboard'])->name('dashboard');

    Route::middleware('sekretaris')->group(function () {
        Route::get('/user/get/data', [UserController::class, 'getData'])->name('user.getData');
        Route::post('/get/detail/user', [UserController::class, 'getDetailUser'])->name('user.getDetail');
        // Pengguna
        Route::get('/master/aktor/pengguna', [UserController::class, 'index'])->name('master/aktor/pengguna.index');
        Route::post('/master/aktor/pengguna/store', [UserController::class, 'store'])->name('master/aktor/pengguna.store');
        Route::put('/master/aktor/pengguna/update/{id}', [UserController::class, 'update'])->name('master/aktor/pengguna.update')->withoutMiddleware('sekretaris');
        Route::delete('/master/aktor/pengguna/destroy/{id}', [UserController::class, 'destroy'])->name('master/aktor/pengguna.destroy');

        // Route::delete('/pengguna/restore/forceDelete/{id}', [UserController::class, 'forceDelete'])->name('pengguna/restore.forceDelete');
        // Route::put('/pengguna/restore/{id}', [UserController::class, 'restore'])->name('pengguna.restore');

        Route::get('/profile', [UserController::class, 'profile'])->name('profile')->withoutMiddleware('sekretaris');

        // master data surat
        Route::get('jenis/surat', [MasterController::class, 'indexMail'])->name('jenis/surat.index');
        Route::get('jenis/surat/create', [MasterController::class, 'createMail'])->name('jenis/surat.create');
        Route::post('jenis/surat/store', [MasterController::class, 'storeMail'])->name('jenis/surat.store');
        Route::delete('jenis/surat/destroy/{id}', [MasterController::class, 'destroyMail'])->name('jenis/surat.destroy');
    });

    Route::middleware('petugasOrWarga:' . UserRole::PENDUDUK->value . ',' . UserRole::PETUGAS->value)->group(function(){
        // pengajuan surat
        Route::get('pengajuan/surat', [SubmissionLetterController::class, 'create'])->name('pengajuan/surat.create');
        Route::post('pengajuan/surat/store', [SubmissionLetterController::class, 'store'])->name('pengajuan/surat.store');
        Route::get('pengajuan/surat/get/schema/{id}', [SubmissionLetterController::class, 'getSchema'])->name('pengajuan/surat.getSchema');
    });
    Route::middleware('warga')->group(function(){
        // Surat Saya
        Route::get('surat/saya', [SubmissionLetterController::class, 'index'])->name('surat/saya.index');
        Route::get('surat/saya/show', [SubmissionLetterController::class, 'getSuratSaya'])->name('surat/saya.show');
        Route::post('surat/saya/update/status', [SubmissionLetterController::class, 'updateStatusPengajuan'])->name('surat/saya/update.status');
    });


    Route::middleware('petugas')->group(function () {
        // Proses Surat verifikasi
        Route::get('proses/surat/verifikasi', [LetterProcessController::class, 'indexVerifikasi'])->name('proses/surat/verifikasi.index');
        Route::get('proses/surat/verifikasi/show', [LetterProcessController::class, 'getSuratVerifikasi'])->name('proses/surat/verifikasi.show');
        Route::get('proses/surat/verifikasi/getDetail/{id}', [LetterProcessController::class, 'getDetailPengajuan'])->name('proses/surat/verifikasi.getDetail');
        Route::post('proses/surat/verifikasi/store', [LetterProcessController::class, 'store'])->name('proses/surat/verifikasi.store');
        Route::post('proses/surat/verifikasi/update', [LetterProcessController::class, 'updateStatusPengajuan'])->name('proses/surat/verifikasi.update');
        // upload
        Route::get('proses/surat/upload', [LetterProcessController::class, 'indexUpload'])->name('proses/surat/upload.index');
        Route::get('proses/surat/upload/show', [LetterProcessController::class, 'showUpload'])->name('proses/surat/upload.show');
        Route::post('proses/surat/upload/store', [LetterProcessController::class, 'storeUpload'])->name('proses/surat/upload.store');
    });

    Route::middleware('wali-nagari')->group(function() {
        Route::get('proses/surat/pengesahan', [LetterProcessController::class, 'indexSent'])->name('proses/surat/pengesahan.index');
        Route::get('proses/surat/pengesahan/show', [LetterProcessController::class, 'showSent'])->name('proses/surat/pengesahan.show');
        Route::post('proses/surat/pengesahan/store', [LetterProcessController::class, 'storeSent'])->name('proses/surat/pengesahan.store');
        Route::post('proses/surat/insertQrToPdf', [LetterProcessController::class, 'insertQrToPdf'])->name('proses/surat.insertQrToPdf');
    });

    Route::middleware('petugasOrWarga:'. UserRole::SEKRETARIS->value . ',' . UserRole::WALINAGARI->value)->group(function(){
        Route::get('export/report', [ReportController::class, 'index'])->name('export.index');
        Route::get('export/report/show', [ReportController::class, 'show'])->name('export.show');
        Route::get('export/report/download', [ReportController::class, 'exportExcel'])->name('export.download');
    });

    Route::get('/misc/forbidden', function () {
        return view('pages.misc-page.forbidden');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticateController::class, 'index'])->name('login');
    Route::post('/login', [AuthenticateController::class, 'store'])->name('login');
    Route::get('/registration', [AuthenticateController::class, 'indexRegister'])->name('register');
    Route::post('/registration', [AuthenticateController::class, 'storeRegister'])->name('register');

    // verifikasi qr code
    Route::get('letter/verify/code', [QrVerifyController::class, 'verify'])->name('Qr.verify');

    // unduh Surat
});
Route::get('unduh/surat/{encryptIdSurat}', [LetterProcessController::class, 'download'])->name('unduh.surat');
Route::get('/misc/error', function () {
    return view('pages.misc-page.error');
});



