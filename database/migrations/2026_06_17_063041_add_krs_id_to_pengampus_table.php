<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengampus', function (Blueprint $table) {
            $table->foreignId('krs_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengampus', function (Blueprint $table) {
            $table->dropForeign(['krs_id']);
            $table->dropColumn('krs_id');
        });
    }
};
