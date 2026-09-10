<?php

namespace Tests\Feature;

use App\Models\Kurikulum;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirekturMonitorKurikulumTest extends TestCase
{
    use RefreshDatabase;

    private function createDirektur(): User
    {
        return User::create([
            'name' => 'Direktur',
            'email' => 'direktur_monitor@test.dev',
            'password' => bcrypt('password'),
            'role' => 'direktur',
            'roles' => ['direktur'],
        ]);
    }

    private function createKurikulum(string $status): Kurikulum
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        return Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum TI 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '110 SKS',
            'deskripsi' => 'Kurikulum Teknik Informatika',
            'status' => $status,
        ]);
    }

    public function test_direktur_dapat_akses_halaman_monitoring_kurikulum(): void
    {
        $du = $this->createDirektur();

        $this->actingAs($du)->get(route('monitoring.kurikulum'))->assertOk();
    }

    public function test_direktur_dapat_lihat_detail_kurikulum(): void
    {
        $du = $this->createDirektur();
        $kurikulum = $this->createKurikulum('Aktif');

        $this->actingAs($du)->get(route('kurikulum.detail', $kurikulum->id))->assertOk();
    }

    public function test_direktur_dapat_lihat_detail_kurikulum_arsip(): void
    {
        $du = $this->createDirektur();
        $kurikulum = $this->createKurikulum('Arsip');

        $this->actingAs($du)->get(route('kurikulum.detail', $kurikulum->id))->assertOk();
    }

    public function test_direktur_dapat_lihat_sub_halaman_kurikulum_read_only(): void
    {
        $du = $this->createDirektur();
        $kurikulum = $this->createKurikulum('Aktif');

        $this->actingAs($du)->get(route('kurikulum.cpl.index', $kurikulum->id))->assertOk();
        $this->actingAs($du)->get(route('kurikulum.cpmk.index', $kurikulum->id))->assertOk();
        $this->actingAs($du)->get(route('kurikulum.bahan-kajian.index', $kurikulum->id))->assertOk();
        $this->actingAs($du)->get(route('kurikulum.profil-lulusan.index', $kurikulum->id))->assertOk();
        $this->actingAs($du)->get(route('kurikulum.mata-kuliah.index', $kurikulum->id))->assertOk();
        $this->actingAs($du)->get(route('kurikulum.struktur', $kurikulum->id))->assertOk();
    }

    public function test_url_lama_liat_kurikulum_dialihkan_ke_detail(): void
    {
        $du = $this->createDirektur();
        $kurikulum = $this->createKurikulum('Aktif');

        $this->actingAs($du)
            ->get("monitoring/kurikulum/{$kurikulum->id}")
            ->assertRedirect(route('kurikulum.detail', $kurikulum->id));
    }

    public function test_direktur_ditolak_akses_crud_kurikulum(): void
    {
        $du = $this->createDirektur();
        $kurikulum = $this->createKurikulum('Aktif');

        $this->actingAs($du)->get(route('kurikulum.create'))->assertForbidden();
        $this->actingAs($du)->get(route('kurikulum.cpl.create', $kurikulum->id))->assertForbidden();
        $this->actingAs($du)->post(route('kurikulum.cpl.store', $kurikulum->id))->assertForbidden();
        $this->actingAs($du)->post(route('kurikulum.cpl-pl.update', $kurikulum->id))->assertForbidden();
    }
}
