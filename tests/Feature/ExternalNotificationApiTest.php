<?php

namespace Tests\Feature;

use App\Jobs\SendPerpusNotificationJob;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Services\PerpusNotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ExternalNotificationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.eduva_api.key', 'test-eduva-secret-key');
    }

    public function test_api_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/external/notifications?nim=12345');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized: API Key tidak valid.',
            ]);
    }

    public function test_api_returns_404_if_nim_not_found(): void
    {
        $response = $this->withHeaders([
            'X-API-KEY' => 'test-eduva-secret-key',
        ])->getJson('/api/v1/external/notifications?nim=NIM_TIDAK_ADA');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.',
            ]);
    }

    public function test_api_returns_notifications_list_for_valid_nim(): void
    {
        $prodi = ProgramStudi::first() ?? ProgramStudi::create(['nama_prodi' => 'Teknik Informatika', 'kode_prodi' => 'TI', 'jenjang' => 'S1']);
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nim' => 'TESTNIM999',
            'nama' => 'Test Mahasiswa Notif',
            'angkatan' => 2024,
            'status' => 'Aktif',
        ]);

        $response = $this->withHeaders([
            'X-API-KEY' => 'test-eduva-secret-key',
        ])->getJson('/api/v1/external/notifications?nim=TESTNIM999');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'nim' => 'TESTNIM999',
                    'unread_count' => 0,
                ],
                'data' => [],
            ]);
    }

    public function test_unread_count_endpoint(): void
    {
        $prodi = ProgramStudi::first() ?? ProgramStudi::create(['nama_prodi' => 'Teknik Informatika', 'kode_prodi' => 'TI', 'jenjang' => 'S1']);
        $user = User::factory()->create(['role' => 'mahasiswa']);
        Mahasiswa::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nim' => 'TESTNIM998',
            'nama' => 'Test Mahasiswa Count',
            'angkatan' => 2024,
            'status' => 'Aktif',
        ]);

        $response = $this->withHeaders([
            'X-API-KEY' => 'test-eduva-secret-key',
        ])->getJson('/api/v1/external/notifications/unread-count?nim=TESTNIM998');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'nim' => 'TESTNIM998',
                'unread_count' => 0,
            ]);
    }

    public function test_perpus_notification_dispatcher_dispatches_http_webhook(): void
    {
        Queue::fake();

        Config::set('services.perpus.webhook_url', 'https://perpus.test/api/webhook');
        Config::set('services.perpus.webhook_secret', 'secret-signature');

        $prodi = ProgramStudi::first() ?? ProgramStudi::create(['nama_prodi' => 'Teknik Informatika', 'kode_prodi' => 'TI', 'jenjang' => 'S1']);
        $user = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso']);
        Mahasiswa::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nim' => 'TESTDISPATCH123',
            'nama' => 'Budi Santoso',
            'angkatan' => 2024,
            'status' => 'Aktif',
        ]);

        PerpusNotificationDispatcher::dispatch(
            $user,
            'tugas_baru',
            'Tugas Baru: Test Tugas',
            'Deskripsi tugas',
            ['tugas_id' => 1]
        );

        Queue::assertPushed(SendPerpusNotificationJob::class, function ($job) {
            return $job->payload['nim'] === 'TESTDISPATCH123'
                && $job->payload['type'] === 'tugas_baru';
        });
    }

    public function test_webhook_connection_failure_does_not_throw_on_sync_queue(): void
    {
        Config::set('services.perpus.webhook_url', 'https://perpus.test/api/webhook');
        Config::set('services.perpus.webhook_secret', 'secret-signature');
        Config::set('queue.default', 'sync');

        Http::fake(fn () => throw new ConnectionException('Operation timed out after 5005 milliseconds'));

        $prodi = ProgramStudi::first() ?? ProgramStudi::create(['nama_prodi' => 'Teknik Informatika', 'kode_prodi' => 'TI', 'jenjang' => 'S1']);
        $user = User::factory()->create(['role' => 'mahasiswa']);
        Mahasiswa::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nim' => 'TESTTIMEOUT124',
            'nama' => 'Test Webhook Timeout',
            'angkatan' => 2024,
            'status' => 'Aktif',
        ]);

        PerpusNotificationDispatcher::dispatch(
            $user,
            'pengumuman_baru',
            'Pengumuman Baru: Test',
            'Isi pengumuman'
        );

        $this->assertTrue(true, 'Dispatcher tidak boleh melempar exception saat webhook gagal.');
    }

    public function test_web_unread_count_endpoint_requires_auth(): void
    {
        $response = $this->getJson(route('notifications.unread-count'));
        $response->assertStatus(401);
    }

    public function test_web_unread_count_endpoint_returns_count_for_logged_in_user(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($user)->getJson(route('notifications.unread-count'));
        $response->assertStatus(200)
            ->assertJson([
                'count' => 0,
            ]);
    }
}
