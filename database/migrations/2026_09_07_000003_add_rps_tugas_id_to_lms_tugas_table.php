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
        Schema::table('lms_tugas', function (Blueprint $table) {
            $table->foreignId('rps_tugas_id')
                ->nullable()
                ->after('rps_pertemuan_id')
                ->constrained('rps_tugas')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_tugas', function (Blueprint $table) {
            $table->dropForeign(['rps_tugas_id']);
            $table->dropColumn('rps_tugas_id');
        });
    }
};
