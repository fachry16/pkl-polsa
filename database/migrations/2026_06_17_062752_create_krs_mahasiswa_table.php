<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['krs_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs_mahasiswa');
    }
};
