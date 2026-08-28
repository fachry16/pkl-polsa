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
        Schema::create('mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained()->cascadeOnDelete();
            $table->string('kode');
            $table->string('nama');
            $table->unsignedTinyInteger('sks_teori')->default(0);
            $table->unsignedTinyInteger('sks_praktikum')->default(0);
            $table->unsignedTinyInteger('semester');
            $table->enum('jenis', [
                'Wajib',
                'Pilihan',
            ])->default('Wajib');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliahs');
    }
};
