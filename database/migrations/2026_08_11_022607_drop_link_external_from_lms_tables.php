<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('lms_materis', 'link_external')) {
            Schema::table('lms_materis', function (Blueprint $table) {
                $table->dropColumn('link_external');
            });
        }

        if (Schema::hasColumn('lms_forum_diskusis', 'link_external')) {
            Schema::table('lms_forum_diskusis', function (Blueprint $table) {
                $table->dropColumn('link_external');
            });
        }
    }

    public function down(): void
    {
        Schema::table('lms_materis', function (Blueprint $table) {
            $table->string('link_external')->nullable();
        });

        Schema::table('lms_forum_diskusis', function (Blueprint $table) {
            $table->string('link_external')->nullable();
        });
    }
};
