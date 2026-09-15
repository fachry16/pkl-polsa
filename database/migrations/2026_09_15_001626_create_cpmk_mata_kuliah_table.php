<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpmk_mata_kuliah', function (Blueprint $table) {
            $table->foreignId('mata_kuliah_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cpmk_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['mata_kuliah_id', 'cpmk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpmk_mata_kuliah');
    }
};
