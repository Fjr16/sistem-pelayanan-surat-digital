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
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('nik', 16)->nullable(false);
            $table->string('tempat_lhr')->nullable(false);
            $table->date('tanggal_lhr')->nullable(false);
            $table->text('alamat')->nullable();
            $table->string('hp')->nullable(false)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
