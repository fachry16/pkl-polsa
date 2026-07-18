<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            if (!Schema::hasColumn('rps', 'disetujui_oleh')) {
                $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('rps', 'tanggal_disetujui')) {
                $table->timestamp('tanggal_disetujui')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn(['disetujui_oleh', 'tanggal_disetujui']);
        });
    }
};
