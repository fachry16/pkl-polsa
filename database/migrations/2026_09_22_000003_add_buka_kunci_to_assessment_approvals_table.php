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
        Schema::table('assessment_approvals', function (Blueprint $table) {
            $table->foreignId('buka_kunci_oleh')->nullable()->after('direvisi_at')->constrained('users')->nullOnDelete();
            $table->timestamp('buka_kunci_at')->nullable()->after('buka_kunci_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_approvals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('buka_kunci_oleh');
            $table->dropColumn('buka_kunci_at');
        });
    }
};
