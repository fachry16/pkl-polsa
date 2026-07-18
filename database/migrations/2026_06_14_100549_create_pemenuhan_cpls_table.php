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
        Schema::create('pemenuhan_cpls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('cpl_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->timestamps();
            $table->unique([
                'kurikulum_id',
                'cpl_id',
                'semester'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemenuhan_cpls');
    }
};
