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
        Schema::table('rps_penilaians', function (Blueprint $table) {
            $table->decimal('etika', 5, 2)->default(0)->after('keaktifan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps_penilaians', function (Blueprint $table) {
            $table->dropColumn('etika');
        });
    }
};
