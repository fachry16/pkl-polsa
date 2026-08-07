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
        Schema::create('pengampus', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dosen_id')
                    ->constrained()
                    ->cascadeOnDelete();

            $table->foreignId('mata_kuliah_id')
                    ->constrained()
                    ->cascadeOnDelete();

            $table->foreignId('tahun_akademik_id')
                    ->constrained()
                    ->cascadeOnDelete();

            $table->enum('semester_akademik', [
                    'Ganjil',
                    'Genap'
                ]);

            $table->string('kelas')->nullable();

            $table->timestamps();

            $table->unique([
                    'dosen_id',
                    'mata_kuliah_id',
                    'tahun_akademik_id',
                    'semester_akademik',
                    'kelas'
            ]);        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengampus');
    }
};
