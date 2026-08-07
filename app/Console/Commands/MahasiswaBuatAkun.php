<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MahasiswaBuatAkun extends Command
{
    protected $signature = 'mahasiswa:buat-akun';

    protected $description = 'Buat akun login untuk mahasiswa yang belum memiliki user_id';

    public function handle(): int
    {
        $tanpaAkun = Mahasiswa::whereNull('user_id')->get();

        if ($tanpaAkun->isEmpty()) {
            $this->info('Semua mahasiswa sudah memiliki akun login.');

            return self::SUCCESS;
        }

        $dibuat = 0;
        $gagal = 0;

        foreach ($tanpaAkun as $mahasiswa) {
            $email = $mahasiswa->nim . '@polsa.ac.id';

            if (User::where('email', $email)->exists()) {
                $this->warn("{$mahasiswa->nim} dilewati: email {$email} sudah dipakai akun lain.");
                $gagal++;

                continue;
            }

            $user = User::create([
                'name' => $mahasiswa->nama,
                'email' => $email,
                'password' => Hash::make($mahasiswa->nim),
                'role' => 'mahasiswa',
            ]);

            $mahasiswa->update(['user_id' => $user->id]);
            $this->info("Akun dibuat untuk {$mahasiswa->nama} ({$mahasiswa->nim}) -> {$email} / password: {$mahasiswa->nim}");
            $dibuat++;
        }

        $this->newLine();
        $this->info("Selesai. Akun dibuat: {$dibuat}, gagal/dilewati: {$gagal}.");

        return self::SUCCESS;
    }
}
