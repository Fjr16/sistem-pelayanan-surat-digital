<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id');
            $table->string('username', 20)->unique()->nullable(false);
            $table->string('password')->nullable(false);
            $table->string('email', 30)->unique()->nullable(false);
            $table->string('no_wa', 20)->unique()->nullable(false);
            $table->string('nik', 16)->unique()->nullable(false);
            $table->string('no_kk', 20)->nullable(false);
            $table->string('name')->nullable(false);
            $table->enum('gender', ['Pria', 'Wanita'])->nullable(false);
            $table->date('tanggal_lhr')->nullable(false);
            $table->string('tempat_lhr', 50)->nullable(false);
            $table->text('alamat_ktp')->nullable();
            $table->text('alamat_dom')->nullable();
            $table->string('agama', 20)->nullable(false);
            $table->string('status_kawin', 20)->nullable(false);
            $table->string('pekerjaan', 50)->nullable();
            $table->string('jabatan', 50)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
