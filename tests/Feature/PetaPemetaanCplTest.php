<?php

namespace Tests\Feature;

use App\Models\BahanKajian;
use App\Models\Cpl;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PetaPemetaanCplTest extends TestCase
{
    use RefreshDatabase;

    private function buatData(): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => '11',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum TI 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '110 SKS',
            'deskripsi' => 'Kurikulum Teknik Informatika',
            'status' => 'Aktif',
        ]);

        $cpl = Cpl::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_cpl' => 'CPL01',
            'deskripsi' => 'Menguasai algoritma pemrograman',
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'MK01',
            'nama' => 'Pemrograman Dasar',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $bk = BahanKajian::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_bk' => 'BK01',
            'nama_bk' => 'Struktur Data',
        ]);

        DB::table('cpl_bahan_kajian_mata_kuliah')->insert([
            'mata_kuliah_id' => $mk->id,
            'cpl_id' => $cpl->id,
            'bahan_kajian_id' => $bk->id,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        return compact('kurikulum', 'cpl', 'mk', 'admin');
    }

    public function test_pemenuhan_cpl_menampilkan_kode_mk_per_semester_tanpa_checkbox()
    {
        ['kurikulum' => $kurikulum, 'cpl' => $cpl, 'mk' => $mk, 'admin' => $admin] = $this->buatData();

        $this->actingAs($admin)
            ->get(route('kurikulum.pemenuhan-cpl.index', $kurikulum->id))
            ->assertOk()
            ->assertSee($cpl->kode_cpl)
            ->assertSee($mk->kode)
            ->assertSee('Smt 3')
            ->assertDontSee('name="mapping');
    }

    public function test_cpl_bk_mk_menampilkan_matriks_tanpa_filter_mata_kuliah()
    {
        ['kurikulum' => $kurikulum, 'cpl' => $cpl, 'mk' => $mk, 'admin' => $admin] = $this->buatData();

        $this->actingAs($admin)
            ->get(route('kurikulum.cpl-bk-mk.index', $kurikulum->id))
            ->assertOk()
            ->assertSee($cpl->kode_cpl)
            ->assertSee($mk->kode)
            ->assertSee('Jumlah BK per CPL')
            ->assertSee('Jumlah MK per CPL')
            ->assertDontSee('Pilih Mata Kuliah');
    }
}
