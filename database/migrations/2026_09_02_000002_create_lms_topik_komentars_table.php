<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_topik_komentars', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_topik'); // 'tugas' atau 'materi'
            $table->unsignedBigInteger('topik_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_private')->default(false);
            $table->text('pesan');
            $table->timestamps();

            $table->index(['tipe_topik', 'topik_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_topik_komentars');
    }
};
