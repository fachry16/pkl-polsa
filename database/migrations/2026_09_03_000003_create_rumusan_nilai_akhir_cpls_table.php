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
        Schema::create('rumusan_nilai_akhir_cpls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('cpmk_id')->constrained('cpmks')->cascadeOnDelete();
            $table->decimal('skor_maks', 7, 2)->default(0);
            $table->decimal('total', 7, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rumusan_nilai_akhir_cpls');
    }
};
