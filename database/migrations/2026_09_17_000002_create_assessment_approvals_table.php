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
        Schema::create('assessment_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status')->default('menunggu'); // menunggu | disetujui | direvisi
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diajukan_at')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_at')->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->foreignId('direvisi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('direvisi_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_approvals');
    }
};
