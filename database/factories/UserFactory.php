<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = UserRole::cases()[array_rand(UserRole::cases())]->value;
        return [
            'role' => $role,
            'username' => fake()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'email' => fake()->unique()->safeEmail(),
            'no_wa' => '081378580551',
            'nik' => '1309041608010001',
            'no_kk' => '1309041608010001',
            'name' => fake()->name(),
            'gender' => "Pria",
            'tanggal_lhr' => "2001-08-16",
            'tempat_lhr' => "Gadur",
            'alamat_ktp' => "Gadur",
            'alamat_dom' => "Padang",
            'agama' => "Islam",
            'status_kawin' => "belum menikah",
            'pekerjaan' => "Programmer",
            'jabatan' => "Sekretaris wali nagari",
            'tanggal_masuk' => "2025-01-01",
            'is_active' => true,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
