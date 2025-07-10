<?php

use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isGuru;
use App\Http\Middleware\isKepsek;
use App\Http\Middleware\isWaliKelas;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');    //untuk mengarahkan pengguna yang tidak terautentikasi
        $middleware->redirectUsersTo('/dashboard');    //untuk mengarahkan pengguna yang terautentikasi
        // $middleware->alias([
        //     'admin' => isAdmin::class,
        //     'guru' => isGuru::class,
        //     'kepsek' => isKepsek::class,
        //     'wali-kelas' => isWaliKelas::class,
        // ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
