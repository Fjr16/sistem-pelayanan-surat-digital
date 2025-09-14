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
        Schema::create('signature_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_mail_id')->nullable(false);
            $table->integer('page_number')->nullable(false);
            $table->float('signature_x')->nullable(false);
            $table->float('signature_y')->nullable(false);
            $table->float('signature_height')->nullable(false);
            $table->float('signature_width')->nullable(false);
            $table->float('canvas_width')->nullable(false);
            $table->float('canvas_height')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_positions');
    }
};
