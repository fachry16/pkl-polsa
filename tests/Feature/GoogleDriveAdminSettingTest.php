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

    private ?string $originalConfig = null;

    private ?string $originalServiceAccount = null;

    private ?string $originalEnv = null;

    protected function setUp(): void
    {
        parent::setUp();

        $configPath = storage_path('app/google-drive/config.json');
        $jsonPath = storage_path('app/google-drive/service-account.json');
        $envPath = base_path('.env');

        if (File::exists($configPath)) {
            $this->originalConfig = File::get($configPath);
        }
        if (File::exists($jsonPath)) {
            $this->originalServiceAccount = File::get($jsonPath);
        }
        if (File::exists($envPath)) {
            $this->originalEnv = File::get($envPath);
        }
    }

    protected function tearDown(): void
    {
        $configPath = storage_path('app/google-drive/config.json');
        $jsonPath = storage_path('app/google-drive/service-account.json');
        $envPath = base_path('.env');

        if ($this->originalConfig !== null) {
            File::put($configPath, $this->originalConfig);
        } else {
            File::delete($configPath);
        }

        if ($this->originalServiceAccount !== null) {
            File::put($jsonPath, $this->originalServiceAccount);
        } else {
            File::delete($jsonPath);
        }

        if ($this->originalEnv !== null) {
            File::put($envPath, $this->originalEnv);
        }

        parent::tearDown();
    }

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
        $this->assertTrue(File::exists(storage_path('app/google-drive/config.json')));

        $config = json_decode(File::get(storage_path('app/google-drive/config.json')), true);
        $this->assertEquals('folder-12345', $config['folder_id']);
        $this->assertEquals('bot@project.iam.gserviceaccount.com', $config['client_email']);
        $this->assertTrue($config['enabled']);
    }

    public function test_admin_dapat_menjalankan_tes_koneksi_gdrive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.test'));

        $response->assertRedirect();
        $this->assertTrue(session()->has('toast_success') || session()->has('toast_error'));
    }
}
