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
        // Gate::define('admin', function ($user) {
        //     return $user->role === 'Administrator';
        // });

        // Gate::define('guru', function ($user){
        //     return $user->role === 'Guru';
        // });
        // Gate::define('wali-kelas', function ($user){
        //     // return  && $user->teacher?->grade !== null;
        //     return Gate::allows('guru', $user) && $user->teacher?->grade;
        // });
        // Gate::define('kepsek', function ($user) {
        //     return $user->role === 'Kepala Sekolah';
        // });
    }
}
