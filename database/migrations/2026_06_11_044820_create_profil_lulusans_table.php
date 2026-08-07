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
        Schema::create('profil_lulusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained()->cascadeOnDelete();
            $table->string('kode_pl');
            $table->string('nama_pl');
            $table->string('profesi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_lulusans');
    }
};
