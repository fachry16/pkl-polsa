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
        Schema::create('metode_bobot_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('cpmk_id')->constrained('cpmks')->cascadeOnDelete();
            $table->decimal('partisipasi', 5, 2)->default(0);
            $table->decimal('kuis', 5, 2)->default(0);
            $table->decimal('tugas_teori_individu', 5, 2)->default(0);
            $table->decimal('unjuk_kerja_presentasi', 5, 2)->default(0);
            $table->decimal('tes_tulis_uts', 5, 2)->default(0);
            $table->decimal('tes_tulis_uas', 5, 2)->default(0);
            $table->decimal('tugas_teori_kelompok', 5, 2)->default(0);
            $table->decimal('tugas_praktikum', 5, 2)->default(0);
            $table->decimal('responsi', 5, 2)->default(0);
            $table->decimal('total', 7, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metode_bobot_penilaians');
    }
};
