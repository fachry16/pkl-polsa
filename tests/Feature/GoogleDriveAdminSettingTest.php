<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
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
        File::delete(storage_path('app/google-drive/test_config.json'));
    }

    protected function tearDown(): void
    {
        File::delete(storage_path('app/google-drive/test_config.json'));
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
        $this->assertTrue(File::exists(storage_path('app/google-drive/test_config.json')));

        $config = json_decode(File::get(storage_path('app/google-drive/test_config.json')), true);
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

    public function test_admin_dapat_mengupdate_pengaturan_oauth_gdrive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.update'), [
            'enabled' => 1,
            'folder_id' => 'folder-oauth-123',
            'auth_mode' => 'oauth',
            'oauth_client_id' => 'client-id-test.apps.googleusercontent.com',
            'oauth_client_secret' => 'GOCSPX-secret-test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast_success');

        $config = json_decode(File::get(storage_path('app/google-drive/test_config.json')), true);
        $this->assertEquals('folder-oauth-123', $config['folder_id']);
        $this->assertEquals('oauth', $config['auth_mode']);
        $this->assertEquals('client-id-test.apps.googleusercontent.com', $config['oauth_client_id']);
        $this->assertEquals('GOCSPX-secret-test', $config['oauth_client_secret']);
    }

    public function test_oauth_connect_redirects_to_google(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Save client id first
        $dir = storage_path('app/google-drive');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
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

        $dir = storage_path('app/google-drive');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
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

        $config = json_decode(File::get(storage_path('app/google-drive/test_config.json')), true);
        $this->assertEquals('mock-refresh-token', $config['oauth_refresh_token']);
        $this->assertEquals('eduva@polsa.ac.id', $config['oauth_connected_email']);
        $this->assertEquals('oauth', $config['auth_mode']);
        $this->assertTrue($config['enabled']);
    }

    public function test_oauth_disconnect_clears_token(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $dir = storage_path('app/google-drive');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
        File::put(storage_path('app/google-drive/test_config.json'), json_encode([
            'oauth_refresh_token' => 'mock-refresh-token',
            'oauth_connected_email' => 'eduva@polsa.ac.id',
        ]));

        $response = $this->actingAs($admin)->post(route('admin.setting.gdrive.oauth.disconnect'));

        $response->assertRedirect(route('admin.setting.gdrive'));
        $response->assertSessionHas('toast_success');

        $config = json_decode(File::get(storage_path('app/google-drive/test_config.json')), true);
        $this->assertArrayNotHasKey('oauth_refresh_token', $config);
        $this->assertArrayNotHasKey('oauth_connected_email', $config);
    }
}
