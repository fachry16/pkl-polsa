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
        Schema::create('mahasiswa_tahun_akademiks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('mahasiswa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('tahun_akademik_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('semester');

            $table->enum('status', [
                'Aktif',
                'Cuti',
                'Lulus',
                'DO',
            ])->default('Aktif');

            $table->timestamps();

            $table->unique([
                'mahasiswa_id',
                'tahun_akademik_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_tahun_akademiks');
    }
};
