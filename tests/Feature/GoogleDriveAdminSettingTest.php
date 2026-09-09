<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GoogleDriveAdminSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_mengakses_halaman_pengaturan_gdrive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.setting.gdrive'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Google Drive');
        $response->assertSee('Panduan & Cara Menautkan GDrive', false);
    }

    public function test_non_admin_ditolak_mengakses_halaman_gdrive(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $response = $this->actingAs($dosen)->get(route('admin.setting.gdrive'));

        $response->assertStatus(403);
    }

    public function test_admin_dapat_mengupdate_pengaturan_gdrive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $jsonFile = UploadedFile::fake()->createWithContent('service-account.json', '{"type": "service_account"}');

        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.update'), [
            'enabled' => 1,
            'folder_id' => 'folder-12345',
            'client_email' => 'bot@project.iam.gserviceaccount.com',
            'credentials_json' => $jsonFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast_success');

        $this->assertTrue(File::exists(storage_path('app/google-drive/service-account.json')));
    }
}
