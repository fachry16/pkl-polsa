<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 50)->unique();
            $table->string('deskripsi', 255)->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['nama' => 'Admin', 'kode' => 'admin', 'deskripsi' => 'Administrator Sistem', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Dosen', 'kode' => 'dosen', 'deskripsi' => 'Dosen Pengajar / Tenaga Pendidik', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kaprodi', 'kode' => 'kaprodi', 'deskripsi' => 'Ketua Program Studi', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Direktur', 'kode' => 'direktur', 'deskripsi' => 'Pimpinan Politeknik / Direktur', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Mahasiswa', 'kode' => 'mahasiswa', 'deskripsi' => 'Mahasiswa Aktif', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};