<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Console\Command;

class MahasiswaBuatAkun extends Command
{
    protected $signature = 'mahasiswa:buat-akun';

    protected $description = 'Buat akun login untuk mahasiswa yang belum memiliki user_id';

    public function handle(): int
    {
        $tanpaAkun = Mahasiswa::whereNull('user_id')->get();

        if ($tanpaAkun->isEmpty()) {
            $this->info('Semua mahasiswa sudah memiliki akun login.');
        }

        $dibuat = 0;
        $gagal = 0;

        foreach ($tanpaAkun as $mahasiswa) {
            $nim = $mahasiswa->nim;

            if (User::where('email', $nim)->exists()) {
                $this->warn("{$nim} dilewati: NIM sudah dipakai akun lain.");
                $gagal++;

                continue;
            }

            $user = User::create([
                'name' => $mahasiswa->nama,
                'email' => $nim,
                'password' => $nim,
                'role' => 'mahasiswa',
                'email_verified_at' => now(),
            ]);

            $mahasiswa->update(['user_id' => $user->id]);
            $this->info("Akun dibuat untuk {$mahasiswa->nama} -> NIM {$nim} / password: {$nim}");
            $dibuat++;
        }

        $this->newLine();
        $this->info("Selesai. Akun dibuat: {$dibuat}, gagal/dilewati: {$gagal}.");

        $diverifikasi = User::where('role', 'mahasiswa')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
        $this->info("Email akun mahasiswa diverifikasi: {$diverifikasi}.");

        return self::SUCCESS;
    }
}
