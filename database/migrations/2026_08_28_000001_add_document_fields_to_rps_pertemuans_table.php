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
            if (! Schema::hasColumn('rps_pertemuans', 'cpmk_induk')) {
                $table->string('cpmk_induk')->nullable();
            }
            if (! Schema::hasColumn('rps_pertemuans', 'teknik_kriteria')) {
                $table->text('teknik_kriteria')->nullable();
            }
            if (! Schema::hasColumn('rps_pertemuans', 'metode_daring')) {
                $table->text('metode_daring')->nullable();
            }
            if (! Schema::hasColumn('rps_pertemuans', 'metode_luring')) {
                $table->text('metode_luring')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps_pertemuans', function (Blueprint $table) {
            $table->dropColumn([
                'cpmk_induk',
                'teknik_kriteria',
                'metode_daring',
                'metode_luring',
            ]);
        });
    }
};
