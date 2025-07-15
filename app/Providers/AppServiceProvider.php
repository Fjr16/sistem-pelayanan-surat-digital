<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', function ($user) {
            return $user->role === 'Administrator';
        });

        Gate::define('petugas', function ($user){
            return $user->role === 'Petugas Wali Nagari';
        });
        Gate::define('wali-nagari', function ($user){
            return $user->role === 'Wali Nagari';
        });
        Gate::define('warga', function ($user) {
            return $user->role === 'Penduduk';
        });
    }
}
