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
        Schema::create('kurikulums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained()->cascadeOnDelete();
            $table->string('nama_kurikulum');
            $table->year('tahun_berlaku');
            $table->string('beban_studi');
            $table->text('deskripsi');
            $table->enum('status', [
                'Draft',
                'Aktif',
                'Arsip',
            ])->default('Draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurikulums');
    }
};
