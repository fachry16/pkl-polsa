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
        Schema::create('rps_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained()->cascadeOnDelete();
            $table->decimal('tugas', 5, 2)->default(0);
            $table->decimal('quiz', 5, 2)->default(0);
            $table->decimal('uts', 5, 2)->default(0);
            $table->decimal('uas', 5, 2)->default(0);
            $table->decimal('praktikum', 5, 2)->default(0);
            $table->decimal('project', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps_penilaians');
    }
};
