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
        Schema::create('rps_bentuk_evaluasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained()->cascadeOnDelete();
            $table->string('bentuk_evaluasi');
            $table->string('sub_cpmk')->nullable();
            $table->text('instrumen')->nullable();
            $table->string('frekuensi')->nullable();
            $table->text('tagihan')->nullable();
            $table->decimal('bobot', 5, 2)->default(0);
            $table->boolean('formatif')->default(false);
            $table->boolean('sumatif')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps_bentuk_evaluasis');
    }
};
