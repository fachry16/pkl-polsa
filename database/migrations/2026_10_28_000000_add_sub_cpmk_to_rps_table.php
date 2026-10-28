<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            $table->text('sub_cpmk')->nullable()->after('deskripsi_mata_kuliah');
        });
    }

    public function down(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            $table->dropColumn('sub_cpmk');
        });
    }
};
