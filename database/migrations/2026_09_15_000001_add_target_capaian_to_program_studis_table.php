<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_studis', function (Blueprint $table) {
            $table->decimal('target_capaian', 5, 2)->default(75)->after('akreditasi');
        });
    }

    public function down(): void
    {
        Schema::table('program_studis', function (Blueprint $table) {
            $table->dropColumn('target_capaian');
        });
    }
};
