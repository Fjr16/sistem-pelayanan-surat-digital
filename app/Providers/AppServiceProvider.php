<?php

namespace App\Providers;

use App\Enums\UserRole;
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
        Gate::define('sekretaris', function ($user) {
            return $user->role === UserRole::SEKRETARIS->value;
        });

        Gate::define('petugas', function ($user){
            return $user->role === UserRole::PETUGAS->value;
        });
        Gate::define('wali-nagari', function ($user){
            return $user->role === UserRole::WALINAGARI->value;
        });
        Gate::define('warga', function ($user) {
            return $user->role === UserRole::PENDUDUK->value;
        });
    }
}
