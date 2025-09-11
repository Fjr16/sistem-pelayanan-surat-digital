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
        Schema::create('incoming_mails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_id')->nullable();
            $table->foreignId('penduduk_id')->nullable(false);
            $table->foreignId('mail_id')->nullable(false);
            $table->string('file_path')->nullable();
            $table->datetime('send_at')->nullable(false);
            $table->string('status')->default('PENDING')->nullable(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_mails');
    }
};
