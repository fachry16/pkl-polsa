<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_nilai_mahasiswas', function (Blueprint $table) {
            $table->decimal('nilai', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lms_nilai_mahasiswas', function (Blueprint $table) {
            $table->decimal('nilai', 5, 2)->default(0)->nullable(false)->change();
        });
    }
};
