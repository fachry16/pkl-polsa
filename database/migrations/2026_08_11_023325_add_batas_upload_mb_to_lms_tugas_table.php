<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_tugas', function (Blueprint $table) {
            $table->integer('batas_upload_mb')
                ->nullable()
                ->comment('Batas maksimal ukuran file jawaban (MB), diatur dosen. Null = default 50 MB.');
        });
    }

    public function down(): void
    {
        Schema::table('lms_tugas', function (Blueprint $table) {
            $table->dropColumn('batas_upload_mb');
        });
    }
};
