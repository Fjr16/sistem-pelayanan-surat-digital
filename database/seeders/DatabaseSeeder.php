<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        
        DB::table('roles')->insert([
            [
                'name' => 'Sekretaris', 
            ],
            [
                'name' => 'Petugas', 
            ],
            [
                'name' => 'Wali Nagari', 
            ],
            [
                'name' => 'Penduduk', 
            ],
        ]);
        User::factory(1)->create();
    }
}
