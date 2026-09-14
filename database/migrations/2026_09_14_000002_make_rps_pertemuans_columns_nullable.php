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
            $table->text('metode')->nullable()->change();
            $table->text('pengalaman_belajar')->nullable()->change();
            $table->text('indikator')->nullable()->change();
            $table->string('bobot')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps_pertemuans', function (Blueprint $table) {
            $table->text('metode')->nullable(false)->change();
            $table->text('pengalaman_belajar')->nullable(false)->change();
            $table->text('indikator')->nullable(false)->change();
            $table->string('bobot')->nullable(false)->change();
        });
    }
};
