<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('role');
        });

        Schema::table('dosens', function (Blueprint $table) {
            $table->string('jabatan', 50)->default('Dosen')->change();
        });

        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $roles = [];
            $dosen = DB::table('dosens')->where('user_id', $user->id)->first();

            if ($dosen) {
                $roles[] = 'dosen';
                if (strtolower($dosen->jabatan ?? '') === 'kaprodi') {
                    $roles[] = 'kaprodi';
                } elseif (strtolower($dosen->jabatan ?? '') === 'direktur') {
                    $roles[] = 'direktur';
                }
            } elseif ($user->role) {
                $roles[] = $user->role;
            } else {
                $roles[] = 'dosen';
            }

            DB::table('users')->where('id', $user->id)->update([
                'roles' => json_encode(array_values(array_unique($roles))),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            $table->enum('jabatan', ['Dosen', 'Kaprodi'])->default('Dosen')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
