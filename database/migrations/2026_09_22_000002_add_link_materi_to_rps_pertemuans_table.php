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
        Schema::table('rps_pertemuans', function (Blueprint $table) {
            $table->text('link_materi')->nullable()->after('file_materi_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps_pertemuans', function (Blueprint $table) {
            $table->dropColumn('link_materi');
        });
    }
};
