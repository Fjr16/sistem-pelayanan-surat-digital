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
        Schema::create('incoming_mail_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_mail_id')->nullable(false);
            $table->foreignId('mail_requirement_id')->nullable(false);
            $table->string('value_basic')->nullable();
            $table->text('value_text')->nullable();
            $table->json('value_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_mail_details');
    }
};
