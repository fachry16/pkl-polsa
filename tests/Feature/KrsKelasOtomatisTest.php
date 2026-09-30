<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class KrsKelasOtomatisTest extends TestCase
{
    use RefreshDatabase;

    private function buatData(): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => '11',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'akreditasi' => 'Baik',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TI',
            'status' => 'Aktif',
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI101',
            'nama' => 'Pemrograman Web',
            'sks_teori' => 3,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $tahun = TahunAkademik::create(['tahun' => 2024, 'semester' => 'Ganjil', 'is_active' => true]);

        $userKaprodi = User::create([
            'name' => 'Kaprodi',
            'email' => 'kaprodi@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $kaprodi = Dosen::create([
            'user_id' => $userKaprodi->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1100',
            'jabatan' => 'Kaprodi',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen Pengampu',
            'email' => 'dosen@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1200',
            'jabatan' => 'Dosen',
        ]);

        $buatMahasiswa = function (string $nim, string $nama) use ($prodi): Mahasiswa {
            $user = User::create([
                'name' => $nama,
                'email' => $nim.'@test.dev',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
            ]);

            return Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $nim,
                'nama' => $nama,
                'email' => $nim.'@test.dev',
                'program_studi_id' => $prodi->id,
                'angkatan' => 2024,
            ]);
        };

        $mhsA = $buatMahasiswa('32241001', 'Mahasiswa A');
        $mhsB = $buatMahasiswa('32242001', 'Mahasiswa B');

        return compact('prodi', 'mk', 'tahun', 'kaprodi', 'userKaprodi', 'dosen', 'mhsA', 'mhsB');
    }

    public function test_store_membuat_kelas_otomatis_per_prodi_dan_semester(): void
    {
        $d = $this->buatData();

        $this->actingAs($d['userKaprodi'])
            ->post(route('krs.store'), [
                'program_studi_id' => $d['prodi']->id,
                'mata_kuliah_id' => $d['mk']->id,
                'dosen_id' => $d['dosen']->id,
                'tahun_akademik_id' => $d['tahun']->id,
                'kelas' => 'Zebra',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $krs = Krs::latest()->first();
        $this->assertSame('11 3', $krs->kelas);
        $this->assertSame('11 3', $krs->pengampu->kelas);
    }

    public function test_split_classes_memecah_base_menjadi_ab_berdasarkan_digit_kelima_nim(): void
    {
        $d = $this->buatData();

        $this->actingAs($d['userKaprodi'])
            ->post(route('krs.store'), [
                'program_studi_id' => $d['prodi']->id,
                'mata_kuliah_id' => $d['mk']->id,
                'dosen_id' => $d['dosen']->id,
                'tahun_akademik_id' => $d['tahun']->id,
            ]);

        $krs = Krs::latest()->first();

        $this->actingAs($d['userKaprodi'])
            ->post(route('krs.mahasiswa.store', $krs->id), [
                'mahasiswa_id' => [$d['mhsA']->id, $d['mhsB']->id],
            ])
            ->assertSessionHas('success');

        Artisan::call('krs:split-classes');

        $this->assertSame('11 3A', Krs::findOrFail($krs->id)->kelas);

        $krsA = Krs::where('kelas', '11 3A')->firstOrFail();
        $krsB = Krs::where('kelas', '11 3B')->firstOrFail();

        $this->assertTrue($krsA->mahasiswas->contains('nim', '32241001'));
        $this->assertFalse($krsA->mahasiswas->contains('nim', '32242001'));
        $this->assertTrue($krsB->mahasiswas->contains('nim', '32242001'));
        $this->assertFalse($krsB->mahasiswas->contains('nim', '32241001'));

        $this->assertSame('11 3A', $krsA->pengampu->kelas);
        $this->assertSame('11 3B', $krsB->pengampu->kelas);
    }

    public function test_klasifikasi_a_b_tidak_terpengaruh_huruf_nama_prodi(): void
    {
        $this->assertTrue(Krs::isKelasA('TI 3A'));
        $this->assertFalse(Krs::isKelasA('TI 3B'));
        $this->assertTrue(Krs::isKelasB('TI 3B'));
        $this->assertFalse(Krs::isKelasB('TI 3A'));

        $this->assertTrue(Krs::isKelasA('AK 4A'));
        $this->assertFalse(Krs::isKelasA('AK 4B'));
        $this->assertTrue(Krs::isKelasB('AK 4B'));
        $this->assertFalse(Krs::isKelasB('AK 4A'));

        $this->assertFalse(Krs::isKelasA('TI 3'));
        $this->assertFalse(Krs::isKelasB('TI 3'));

        $this->assertTrue(Krs::isKelasA('Reguler'));
        $this->assertTrue(Krs::isKelasB('Karyawan'));
    }
}
