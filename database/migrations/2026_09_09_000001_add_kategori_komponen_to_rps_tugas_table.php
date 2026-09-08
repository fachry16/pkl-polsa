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
        Schema::table('rps_tugas', function (Blueprint $table) {
            $table->string('kategori_komponen')->default('tugas')->after('nama_tugas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps_tugas', function (Blueprint $table) {
            $table->dropColumn('kategori_komponen');
        });
    }
};
