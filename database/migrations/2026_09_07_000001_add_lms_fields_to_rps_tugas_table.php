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
            $table->dateTime('deadline')->nullable()->after('batas_waktu');
            $table->integer('bobot_nilai')->default(100)->after('deadline');
            $table->string('file_soal')->nullable()->after('bobot_nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps_tugas', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'bobot_nilai', 'file_soal']);
        });
    }
};
