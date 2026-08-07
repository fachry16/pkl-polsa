<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_forum_diskusis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengampu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('lms_forum_diskusis')->cascadeOnDelete();
            $table->text('pesan');
            $table->string('file_path')->nullable();
            $table->string('link_external')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_forum_diskusis');
    }
};
