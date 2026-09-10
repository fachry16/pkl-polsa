<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_sesi_absensis', function (Blueprint $table) {
            $table->timestamp('waktu_buka')->nullable()->after('tanggal_aktual');
        });
    }

    public function down(): void
    {
        Schema::table('lms_sesi_absensis', function (Blueprint $table) {
            $table->dropColumn('waktu_buka');
        });
    }
};
