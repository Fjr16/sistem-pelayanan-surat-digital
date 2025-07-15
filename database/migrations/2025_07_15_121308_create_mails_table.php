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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citizen_id')->nullable(false);
            $table->foreignId('mail_category_id')->nullable(false);
            $table->string('file_path')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->text('denied_note')->nullable();
            $table->enum('status', ['pending', 'denied', 'approve', 'finished'])->default('pending');
            $table->json('input_user')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
