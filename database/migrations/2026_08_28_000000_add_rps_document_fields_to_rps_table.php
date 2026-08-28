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
        Schema::table('rps', function (Blueprint $table) {
            if (! Schema::hasColumn('rps', 'rumpun_mk')) {
                $table->string('rumpun_mk')->nullable();
            }
            if (! Schema::hasColumn('rps', 'mk_prasyarat')) {
                $table->text('mk_prasyarat')->nullable();
            }
            if (! Schema::hasColumn('rps', 'prasyarat_untuk')) {
                $table->text('prasyarat_untuk')->nullable();
            }
            if (! Schema::hasColumn('rps', 'integrasi_antar_mk')) {
                $table->text('integrasi_antar_mk')->nullable();
            }
            if (! Schema::hasColumn('rps', 'tautan_daring')) {
                $table->string('tautan_daring')->nullable();
            }
            if (! Schema::hasColumn('rps', 'daftar_pustaka')) {
                $table->text('daftar_pustaka')->nullable();
            }
            if (! Schema::hasColumn('rps', 'dosen_pengembang_rps')) {
                $table->string('dosen_pengembang_rps')->nullable();
            }
            if (! Schema::hasColumn('rps', 'koordinator_rmk')) {
                $table->string('koordinator_rmk')->nullable();
            }
            if (! Schema::hasColumn('rps', 'ketua_prodi')) {
                $table->string('ketua_prodi')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            $table->dropColumn([
                'rumpun_mk',
                'mk_prasyarat',
                'prasyarat_untuk',
                'integrasi_antar_mk',
                'tautan_daring',
                'daftar_pustaka',
                'dosen_pengembang_rps',
                'koordinator_rmk',
                'ketua_prodi',
            ]);
        });
    }
};
