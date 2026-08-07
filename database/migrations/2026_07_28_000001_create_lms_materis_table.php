<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_materis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('rps_pertemuan_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_path')->nullable();
            $table->string('link_external')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_materis');
    }
};
