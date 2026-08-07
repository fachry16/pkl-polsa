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
        Schema::create('cpl_bahan_kajian_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->foreignId('bahan_kajian_id')->constrained('bahan_kajians')->cascadeOnDelete();
            $table->unique([
                'mata_kuliah_id',
                'cpl_id',
                'bahan_kajian_id'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpl_bahan_kajian_mata_kuliah');
    }
};
