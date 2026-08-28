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
        Schema::create('rps_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained()->cascadeOnDelete();
            $table->string('minggu_topik');
            $table->string('nama_tugas');
            $table->string('sub_cpmk')->nullable();
            $table->string('penugasan')->nullable();
            $table->text('ruang_lingkup')->nullable();
            $table->text('cara_pengerjaan')->nullable();
            $table->string('batas_waktu')->nullable();
            $table->text('luaran_tugas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps_tugas');
    }
};
