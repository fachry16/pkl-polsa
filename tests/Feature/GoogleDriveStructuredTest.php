<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsMateri;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GoogleDriveStructuredTest extends TestCase
{
    use RefreshDatabase;

    private ?string $originalConfig = null;

    private ?string $originalServiceAccount = null;

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

    public function test_store_file_fallback_ke_local_saat_gdrive_nonaktif(): void
    {
        Storage::fake('public');

        $service = new GoogleDriveService;
        $file = UploadedFile::fake()->create('modul_ajar.pdf', 500, 'application/pdf');

        $path = $service->storeFile($file, 'lms/materi', ['TI201 - TI-2A', 'Materi']);

        $this->assertStringStartsWith('lms/materi/', $path);
        $this->assertStringEndsWith('modul_ajar.pdf', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_store_file_berhasil_ke_gdrive_terstruktur_saat_aktif(): void
    {
        Storage::fake('public');

        $dir = storage_path('app/google-drive');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        File::put($dir.'/service-account.json', json_encode([
            'type' => 'service_account',
            'client_email' => 'bot@project.iam.gserviceaccount.com',
            'private_key' => "-----BEGIN RSA PRIVATE KEY-----\nMIIEowIBAAKCAQEA0mockkey...\n-----END RSA PRIVATE KEY-----\n",
        ]));

        File::put($dir.'/test_config.json', json_encode([
            'enabled' => true,
            'folder_id' => 'root-folder-xyz',
            'client_email' => 'bot@project.iam.gserviceaccount.com',
        ]));

        $mockService = $this->getMockBuilder(GoogleDriveService::class)
            ->onlyMethods(['getAccessToken'])
            ->getMock();

        $mockService->method('getAccessToken')->willReturn('mock-access-token');

        Http::fake([
            'https://www.googleapis.com/drive/v3/files?*' => Http::response(['files' => []], 200),
            'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true' => Http::response(['id' => 'mock-subfolder-1'], 200),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response(['id' => 'mock-uploaded-file-888'], 200),
        ]);

        $file = UploadedFile::fake()->create('modul_ajar.pdf', 500, 'application/pdf');
        $path = $mockService->storeFile($file, 'lms/materi', ['TI201 - TI-2A', 'Materi'], 'Pertemuan_1_modul_ajar.pdf');

        $this->assertEquals('gdrive/mock-uploaded-file-888/Pertemuan_1_modul_ajar.pdf', $path);
        // Server disk is NOT burdened when GDrive is enabled
        Storage::disk('public')->assertMissing($path);
    }

    public function test_lms_file_controller_stream_file_gdrive(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $user = User::factory()->create(['role' => 'dosen']);
        $dosen = Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '12345678',
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
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $ta = TahunAkademik::create([
            'tahun' => 2024,
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'TI-2A',
        ]);

        $materi = LmsMateri::create([
            'pengampu_id' => $pengampu->id,
            'judul' => 'Pengenalan OOP',
            'file_path' => 'gdrive/drive-file-abc-999/Pengenalan_OOP.pdf',
        ]);

        $mockDrive = $this->createMock(GoogleDriveService::class);
        $mockDrive->method('streamFileResponse')
            ->with('drive-file-abc-999', 'Pengenalan_OOP.pdf')
            ->willReturn(response('FILE CONTENT FROM GDRIVE', 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Pengenalan_OOP.pdf"',
            ]));

        $this->app->instance(GoogleDriveService::class, $mockDrive);

        $response = $this->actingAs($user)->get(route('lms.file', ['materi', $materi->id]));

        $response->assertStatus(200);
        $this->assertEquals('FILE CONTENT FROM GDRIVE', $response->getContent());
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_delete_file_gdrive_memanggil_api_gdrive(): void
    {
        $dir = storage_path('app/google-drive');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        File::put($dir.'/service-account.json', json_encode(['client_email' => 'bot@test.com']));
        File::put($dir.'/config.json', json_encode(['enabled' => true, 'folder_id' => 'fid']));

        $mockService = $this->getMockBuilder(GoogleDriveService::class)
            ->onlyMethods(['getAccessToken'])
            ->getMock();

        $mockService->method('getAccessToken')->willReturn('mock-token');

        Http::fake([
            'https://www.googleapis.com/drive/v3/files/file-to-delete?supportsAllDrives=true' => Http::response('', 204),
        ]);

        $result = $mockService->deleteFile('gdrive/file-to-delete/test.pdf');
        $this->assertTrue($result);
    }
}
