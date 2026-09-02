<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_submissions', function (Blueprint $table) {
            $table->string('link_jawaban')->nullable()->after('file_jawaban');
        });
    }

    public function down(): void
    {
        Schema::table('lms_submissions', function (Blueprint $table) {
            $table->dropColumn('link_jawaban');
        });
    }
};
