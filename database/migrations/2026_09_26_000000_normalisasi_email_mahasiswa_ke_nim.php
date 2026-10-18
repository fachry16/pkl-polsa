<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'like', '%@polsa.ac.id')
            ->orderBy('id')
            ->each(function ($user) {
                if (! preg_match('/^(\d+)@polsa\.ac\.id$/i', $user->email, $m)) {
                    return;
                }

                if (DB::table('users')->where('email', $m[1])->where('id', '!=', $user->id)->exists()) {
                    return;
                }

                DB::table('users')->where('id', $user->id)->update(['email' => $m[1]]);
            });
    }

    public function down(): void
    {
        DB::table('users')
            ->whereNotNull('email')
            ->orderBy('id')
            ->each(function ($user) {
                if (! preg_match('/^\d+$/', $user->email)) {
                    return;
                }

                if (DB::table('users')->where('email', $user->email.'@polsa.ac.id')->where('id', '!=', $user->id)->exists()) {
                    return;
                }

                DB::table('users')->where('id', $user->id)->update(['email' => $user->email.'@polsa.ac.id']);
            });
    }
};
