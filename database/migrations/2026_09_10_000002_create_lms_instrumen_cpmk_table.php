<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_instrumen_cpmk', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('cpmk_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('komponen', ['tugas', 'quiz', 'uts', 'uas', 'praktikum', 'project']);
            $table->decimal('bobot_kontribusi', 5, 2)->default(100);

            $table->timestamps();

            $table->unique(['pengampu_id', 'cpmk_id', 'komponen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_instrumen_cpmk');
    }
};
