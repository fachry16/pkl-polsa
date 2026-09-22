<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleDriveAdminSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $dir = storage_path('app/google-drive');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
        File::delete(storage_path('app/google-drive/test_config.json'));
        File::delete(storage_path('app/google-drive/.test_config.backup.json'));
    }

    protected function tearDown(): void
    {
        File::delete(storage_path('app/google-drive/test_config.json'));
        File::delete(storage_path('app/google-drive/.test_config.backup.json'));
        parent::tearDown();
    }

    public function test_admin_dapat_mengakses_halaman_pengaturan_gdrive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.setting.gdrive'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Google Drive');
        $response->assertSee('Panduan Menautkan Akun Kampus', false);
    }

    public function test_non_admin_ditolak_mengakses_halaman_gdrive(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $response = $this->actingAs($dosen)->get(route('admin.setting.gdrive'));

        $response->assertStatus(403);
    }

    public function test_admin_dapat_mengupdate_pengaturan_oauth_gdrive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.update'), [
            'enabled' => 1,
            'folder_id' => 'folder-oauth-123',
            'oauth_client_id' => 'client-id-test.apps.googleusercontent.com',
            'oauth_client_secret' => 'GOCSPX-secret-test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast_success');

        $mainPath = storage_path('app/google-drive/test_config.json');
        $backupPath = storage_path('app/google-drive/.test_config.backup.json');

        $this->assertTrue(File::exists($mainPath));
        $this->assertTrue(File::exists($backupPath));

        $config = json_decode(File::get($mainPath), true);
        $backup = json_decode(File::get($backupPath), true);

        $this->assertEquals('folder-oauth-123', $config['folder_id']);
        $this->assertEquals('oauth', $config['auth_mode']);
        $this->assertEquals('client-id-test.apps.googleusercontent.com', $config['oauth_client_id']);
        $this->assertEquals('GOCSPX-secret-test', $config['oauth_client_secret']);
        $this->assertTrue($config['enabled']);

        $this->assertEquals($config, $backup);
    }

    public function test_update_pengaturan_tidak_menghapus_token_oauth_yang_sudah_ada(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $initialConfig = [
            'enabled' => true,
            'folder_id' => 'folder-awal',
            'auth_mode' => 'oauth',
            'oauth_client_id' => 'client-awal',
            'oauth_client_secret' => 'secret-awal',
            'oauth_refresh_token' => 'permanent-refresh-token-123',
            'oauth_connected_email' => 'eduva@polsa.ac.id',
        ];

        $mainPath = storage_path('app/google-drive/test_config.json');
        File::put($mainPath, json_encode($initialConfig, JSON_PRETTY_PRINT));

        // Admin hanya mengubah folder_id dan client_id baru tanpa menyertakan refresh_token di request form
        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.update'), [
            'enabled' => 1,
            'folder_id' => 'folder-baru-456',
            'oauth_client_id' => 'client-baru',
            'oauth_client_secret' => 'secret-baru',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast_success');

        $savedConfig = json_decode(File::get($mainPath), true);
        $this->assertEquals('folder-baru-456', $savedConfig['folder_id']);
        $this->assertEquals('client-baru', $savedConfig['oauth_client_id']);
        // Refresh token & email akun kampus HARUS TETAP ADA (anti-reset)
        $this->assertEquals('permanent-refresh-token-123', $savedConfig['oauth_refresh_token']);
        $this->assertEquals('eduva@polsa.ac.id', $savedConfig['oauth_connected_email']);
    }

    public function test_konfigurasi_pulih_otomatis_dari_backup_jika_file_utama_hilang(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $backupConfig = [
            'enabled' => true,
            'folder_id' => 'folder-backup-789',
            'auth_mode' => 'oauth',
            'oauth_client_id' => 'client-backup',
            'oauth_client_secret' => 'secret-backup',
            'oauth_refresh_token' => 'refresh-token-from-backup',
            'oauth_connected_email' => 'backup@polsa.ac.id',
        ];

        $mainPath = storage_path('app/google-drive/test_config.json');
        $backupPath = storage_path('app/google-drive/.test_config.backup.json');

        // Simpan hanya di file backup (simulasi file utama terhapus/hilang)
        File::put($backupPath, json_encode($backupConfig, JSON_PRETTY_PRINT));
        File::delete($mainPath);

        $this->assertFalse(File::exists($mainPath));
        $this->assertTrue(File::exists($backupPath));

        // Buka halaman pengaturan, auto-recovery harus memulihkan file utama
        $response = $this->actingAs($admin)->get(route('admin.setting.gdrive'));

        $response->assertStatus(200);
        $response->assertSee('backup@polsa.ac.id');
        $response->assertSee('folder-backup-789');

        // File utama harus otomatis dipulihkan persis sama dengan backup
        $this->assertTrue(File::exists($mainPath));
        $recovered = json_decode(File::get($mainPath), true);
        $this->assertEquals('refresh-token-from-backup', $recovered['oauth_refresh_token']);
    }

    public function test_admin_dapat_menjalankan_tes_koneksi_gdrive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.test'));

        $response->assertRedirect();
        $this->assertTrue(session()->has('toast_success') || session()->has('toast_error'));
    }

    public function test_oauth_connect_redirects_to_google(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        File::put(storage_path('app/google-drive/test_config.json'), json_encode([
            'oauth_client_id' => 'test-client-id.apps.googleusercontent.com',
            'oauth_client_secret' => 'test-secret',
        ]));

        $response = $this->actingAs($admin)->get(route('admin.setting.gdrive.oauth.connect'));

        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com/o/oauth2/v2/auth', $redirectUrl);
        $this->assertStringContainsString('test-client-id.apps.googleusercontent.com', $redirectUrl);
        $this->assertNotEmpty(session('gdrive_oauth_state'));
    }

    public function test_oauth_callback_exchanges_code_and_saves_token(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $state = 'valid-test-state';
        session(['gdrive_oauth_state' => $state]);

        File::put(storage_path('app/google-drive/test_config.json'), json_encode([
            'oauth_client_id' => 'test-client-id',
            'oauth_client_secret' => 'test-secret',
        ]));

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-access-token',
                'refresh_token' => 'mock-refresh-token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://www.googleapis.com/oauth2/v2/userinfo' => Http::response([
                'email' => 'eduva@polsa.ac.id',
            ], 200),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.setting.gdrive.oauth.callback', [
            'code' => 'mock-auth-code',
            'state' => $state,
        ]));

        $response->assertRedirect(route('admin.setting.gdrive'));
        $response->assertSessionHas('toast_success');

        $mainPath = storage_path('app/google-drive/test_config.json');
        $backupPath = storage_path('app/google-drive/.test_config.backup.json');

        $this->assertTrue(File::exists($mainPath));
        $this->assertTrue(File::exists($backupPath));

        $config = json_decode(File::get($mainPath), true);
        $this->assertEquals('mock-refresh-token', $config['oauth_refresh_token']);
        $this->assertEquals('eduva@polsa.ac.id', $config['oauth_connected_email']);
        $this->assertEquals('oauth', $config['auth_mode']);
        $this->assertTrue($config['enabled']);

        $backup = json_decode(File::get($backupPath), true);
        $this->assertEquals($config, $backup);
    }

    public function test_oauth_disconnect_clears_token(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        File::put(storage_path('app/google-drive/test_config.json'), json_encode([
            'oauth_refresh_token' => 'mock-refresh-token',
            'oauth_connected_email' => 'eduva@polsa.ac.id',
        ]));

        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.oauth.disconnect'));

        $response->assertRedirect(route('admin.setting.gdrive'));
        $response->assertSessionHas('toast_success');

        $mainPath = storage_path('app/google-drive/test_config.json');
        $backupPath = storage_path('app/google-drive/.test_config.backup.json');

        $config = json_decode(File::get($mainPath), true);
        $this->assertArrayNotHasKey('oauth_refresh_token', $config);
        $this->assertArrayNotHasKey('oauth_connected_email', $config);

        $backup = json_decode(File::get($backupPath), true);
        $this->assertArrayNotHasKey('oauth_refresh_token', $backup);
        $this->assertArrayNotHasKey('oauth_connected_email', $backup);
    }
}
