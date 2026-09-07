<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_tugas', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('batas_upload_mb');
        });
    }

    public function down(): void
    {
        Schema::table('lms_tugas', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
