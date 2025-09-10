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
            $table->string('field_placeholder')->nullable();
            $table->boolean('is_required')->default(true);
            $table->json('options')->nullable(); // untuk select, radio, checkbox
            $table->string('min', 10)->nullable(); //untuk number dan date
            $table->string('max', 10)->nullable(); //untuk number, dan date, dan sebagai max length jika textarea atau text field
            $table->integer('step')->nullable(); //untuk number
            $table->boolean('inline')->default(false); //untuk radio dan checkbox
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
