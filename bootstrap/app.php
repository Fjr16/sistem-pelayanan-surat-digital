<?php

use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isPenduduk;
use App\Http\Middleware\isPetugas;
use App\Http\Middleware\isWaliNagari;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');    //untuk mengarahkan pengguna yang tidak terautentikasi
        $middleware->redirectUsersTo('/dashboard');    //untuk mengarahkan pengguna yang terautentikasi
        $middleware->alias([
            'admin' => isAdmin::class,
            'petugas' => isPetugas::class,
            'wali-nagari' => isWaliNagari::class,
            'warga' => isPenduduk::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(NotFoundHttpException $e, $request){
            if ($request->is('/')) {
                return redirect()->route('dashboard');
            }

            return response()->view('pages.misc-page.error');
        });
    })->create();
