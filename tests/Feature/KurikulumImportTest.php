<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KurikulumImportTest extends TestCase
{
    use RefreshDatabase;

    private function buatProdi(string $kode = 'TI'): ProgramStudi
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
            'name' => 'Kaprodi Import',
            'email' => 'kaprodi_import_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '8899'.random_int(10000000, 99999999),
            'jabatan' => 'Kaprodi',
        ]);

        return $user;
    }

    private function fileCsv(string $content): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('import.csv', $content);
    }

    public function test_template_import_cpl_tersedia()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);

        $this->actingAs($this->buatKaprodi($prodi))
            ->get(route('kurikulum.cpl.template-import', $kurikulum->id))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_import_cpl_berhasil_dan_duplikat_dilewati()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);

        $this->actingAs($this->buatKaprodi($prodi))
            ->post(route('kurikulum.cpl.import', $kurikulum->id), [
                'file' => $this->fileCsv("kode_cpl,deskripsi\nCPL01,Deskripsi satu\ncpl01,Deskripsi duplikat\nCPL02,Deskripsi dua"),
            ])
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('import_warnings');

        $this->assertDatabaseHas('cpls', ['kurikulum_id' => $kurikulum->id, 'kode_cpl' => 'CPL01']);
        $this->assertDatabaseHas('cpls', ['kurikulum_id' => $kurikulum->id, 'kode_cpl' => 'CPL02']);
        $this->assertSame(2, $kurikulum->cpls()->count());
    }

    public function test_import_cpmk_berhasil_dan_baris_tidak_lengkap_dilewati()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);

        $this->actingAs($this->buatKaprodi($prodi))
            ->post(route('kurikulum.cpmk.import', $kurikulum->id), [
                'file' => $this->fileCsv("kode_cpmk,deskripsi\nCPMK01,Deskripsi cpmk 1\n,Deskripsi tanpa kode\nCPMK02,Deskripsi cpmk 2"),
            ])
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('import_warnings');
        $this->assertSame(2, $kurikulum->cpmks()->count());
    }

    public function test_import_profil_lulusan_berhasil()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);

        $this->actingAs($this->buatKaprodi($prodi))
            ->post(route('kurikulum.profil-lulusan.import', $kurikulum->id), [
                'file' => $this->fileCsv("kode_pl,nama_pl,profesi\nPL01,Software Engineer,Engineer\nPL02,Database Admin,DBA"),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('profil_lulusans', ['kurikulum_id' => $kurikulum->id, 'kode_pl' => 'PL01']);
        $this->assertSame(2, $kurikulum->profilLulusans()->count());
    }

    public function test_import_bahan_kajian_berhasil()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);

        $this->actingAs($this->buatKaprodi($prodi))
            ->post(route('kurikulum.bahan-kajian.import', $kurikulum->id), [
                'file' => $this->fileCsv("kode_bk,nama_bk,referensi\nBK01,Algoritma,Ref A\nBK02,Rekayasa Perangkat Lunak,Ref B"),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(2, $kurikulum->bahanKajians()->count());
    }

    public function test_import_mata_kuliah_berhasil_dan_validasi_nilai()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);

        $this->actingAs($this->buatKaprodi($prodi))
            ->post(route('kurikulum.mata-kuliah.import', $kurikulum->id), [
                'file' => $this->fileCsv("kode,nama,sks_teori,sks_praktikum,semester,jenis\nTI1001,Algoritma,2,1,1,Wajib\nTI1002,Basis Data,2,1,1,Pilihan\nTI1003,Invalid,2,1,99,Wajib"),
            ])
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('import_warnings');

        $this->assertDatabaseHas('mata_kuliahs', ['kurikulum_id' => $kurikulum->id, 'kode' => 'TI1001']);
        $this->assertDatabaseHas('mata_kuliahs', ['kurikulum_id' => $kurikulum->id, 'kode' => 'TI1002']);
        $this->assertSame(2, $kurikulum->mataKuliahs()->count());
    }

    public function test_direktur_tidak_bisa_mengimpor()
    {
        $prodi = $this->buatProdi();
        $kurikulum = $this->buatKurikulum($prodi);

        $direktur = User::create([
            'name' => 'Direktur Import',
            'email' => 'direktur_import_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'direktur',
            'roles' => ['direktur'],
        ]);

        $this->actingAs($direktur)
            ->post(route('kurikulum.cpl.import', $kurikulum->id), [
                'file' => $this->fileCsv("kode_cpl,deskripsi\nCPL01,Deskripsi"),
            ])
            ->assertForbidden();
    }
}
