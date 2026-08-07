<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengampu_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengampu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['pengampu_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengampu_mahasiswa');
    }
};
