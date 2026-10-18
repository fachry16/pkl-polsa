<?php

namespace Tests\Feature;

use App\Models\Cpmk;
use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KurikulumMkCpmkTest extends TestCase
{
    use RefreshDatabase;

    private function buatProdi(string $kode = '11'): ProgramStudi
    {
        return ProgramStudi::create([
            'kode_prodi' => $kode,
            'nama_prodi' => 'Teknik Informatika '.$kode,
            'jenjang' => 'D3',
        ]);
    }

    private function buatKurikulum(ProgramStudi $prodi): Kurikulum
    {
        return Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum TI 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '110 SKS',
            'deskripsi' => 'Kurikulum Teknik Informatika',
            'status' => 'Aktif',
        ]);
    }

    private function buatKaprodi(ProgramStudi $prodi): User
    {
        $user = User::create([
            'name' => 'Kaprodi MK CPMK',
            'email' => 'kaprodi_mkcpmk_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1122'.random_int(10000000, 99999999),
            'jabatan' => 'Kaprodi',
        ]);

        return $user;
    }

    public function test_kaprodi_dapat_melihat_halaman_matriks()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);
        $cpmk = Cpmk::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_cpmk' => 'CPMK011',
            'deskripsi' => 'Menguasai konsep dasar',
        ]);
        MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'MK01',
            'nama' => 'Pemrograman Dasar',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $this->actingAs($this->buatKaprodi($prodi))
            ->get(route('kurikulum.mk-cpmk.index', $kurikulum->id))
            ->assertOk()
            ->assertSee('CPMK011')
            ->assertSee('MK01');
    }

    public function test_kaprodi_dapat_menyimpan_mapping_ke_pivot()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);
        $cpmk1 = Cpmk::create(['kurikulum_id' => $kurikulum->id, 'kode_cpmk' => 'CPMK011', 'deskripsi' => 'Dasar']);
        $cpmk2 = Cpmk::create(['kurikulum_id' => $kurikulum->id, 'kode_cpmk' => 'CPMK012', 'deskripsi' => 'Lanjut']);
        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'MK01',
            'nama' => 'Pemrograman Dasar',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $this->actingAs($this->buatKaprodi($prodi))
            ->put(route('kurikulum.mk-cpmk.update', $kurikulum->id), [
                'mataKuliah' => [$mk->id => [$cpmk1->id, $cpmk2->id]],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('cpmk_mata_kuliah', ['mata_kuliah_id' => $mk->id, 'cpmk_id' => $cpmk1->id]);
        $this->assertDatabaseHas('cpmk_mata_kuliah', ['mata_kuliah_id' => $mk->id, 'cpmk_id' => $cpmk2->id]);

        $this->actingAs($this->buatKaprodi($prodi))
            ->put(route('kurikulum.mk-cpmk.update', $kurikulum->id), [
                'mataKuliah' => [$mk->id => []],
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('cpmk_mata_kuliah', ['mata_kuliah_id' => $mk->id]);
    }

    public function test_direktur_bisa_melihat_tapi_tidak_bisa_mengubah()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);
        Cpmk::create(['kurikulum_id' => $kurikulum->id, 'kode_cpmk' => 'CPMK011', 'deskripsi' => 'Dasar']);

        $direktur = User::create([
            'name' => 'Direktur MK CPMK',
            'email' => 'direktur_mkcpmk_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'direktur',
            'roles' => ['direktur'],
        ]);

        $this->actingAs($direktur)
            ->get(route('kurikulum.mk-cpmk.index', $kurikulum->id))
            ->assertOk();

        $this->actingAs($direktur)
            ->put(route('kurikulum.mk-cpmk.update', $kurikulum->id), [
                'mataKuliah' => [],
            ])
            ->assertForbidden();
    }
}
