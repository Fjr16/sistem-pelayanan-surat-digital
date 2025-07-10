<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\New_;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // DB::table('students')->truncate();
        DB::table('users')->truncate();
        // DB::table('teachers')->truncate();
        // DB::table('subjects')->truncate();
        // DB::table('students')->truncate();
        // DB::table('grades')->truncate();
        // User::factory(10)->create();
        User::factory(1)->create();
        


        // Subject::factory(10)->create();
        // Student::factory(100)->create();
        // Teacher::factory(5)->create();
        // Grade::factory(10)->create();
    }
}
