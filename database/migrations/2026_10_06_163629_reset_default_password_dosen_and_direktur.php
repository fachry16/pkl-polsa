<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $userIds = DB::table('dosens')->pluck('user_id')
            ->merge(DB::table('users')->whereIn('role', ['direktur'])->pluck('id'))
            ->unique();

        DB::table('users')
            ->whereIn('id', $userIds)
            ->update([
                'password' => Hash::make('polsa123'),
                'harus_ganti_password' => true,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Password lama tidak dapat dikembalikan.
    }
};
