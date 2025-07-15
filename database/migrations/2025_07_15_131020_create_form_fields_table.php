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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_category_id')->nullable(false);
            $table->string('input_name')->nullable(false);
            $table->string('input_label')->nullable(false);
            $table->enum('input_type', ['text', 'textarea', 'date', 'number', 'email', 'select']);
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
        Schema::dropIfExists('form_fields');
    }
};
