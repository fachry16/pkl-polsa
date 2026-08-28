<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            if (! Schema::hasColumn('rps', 'catatan_revisi')) {
                $table->text('catatan_revisi')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            if (Schema::hasColumn('rps', 'catatan_revisi')) {
                $table->dropColumn('catatan_revisi');
            }
        });
    }
};
