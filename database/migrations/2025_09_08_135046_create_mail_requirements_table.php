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
        Schema::create('mail_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_id')->nullable(false);
            $table->string('field_label')->nullable(false);
            $table->string('field_name')->nullable(false);
            $table->string('field_type', 20)->nullable(false);
            $table->boolean('is_required')->default(true);
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_requirements');
    }
};
