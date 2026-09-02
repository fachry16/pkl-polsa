<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DataImportTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin POLSA',
            'email' => 'admin@polsa.ac.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);
    }

    private function createProdi(): ProgramStudi
    {
        return ProgramStudi::create([
            'kode_prodi' => 'TRPL',
            'nama_prodi' => 'Teknik Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);
    }

    public function test_admin_dapat_download_template_import_dosen(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('dosen.template-import'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('nama,nidn,email,kode_prodi,jabatan', $response->streamedContent());
    }

    public function test_admin_dapat_download_template_import_mahasiswa(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('mahasiswa.template-import'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('nim,nama,kode_prodi,angkatan,semester,status', $response->streamedContent());
    }

    public function test_admin_dapat_mengimpor_dosen_melalui_csv(): void
    {
        $admin = $this->createAdmin();
        $prodi = $this->createProdi();

        $csvContent = "nama,nidn,email,kode_prodi,jabatan\n"
            . "Dosen Satu,11223344,dosen1@polsa.ac.id,TRPL,Dosen\n"
            . "Dosen Dua,22334455,dosen2@polsa.ac.id,TRPL,Kaprodi\n";

        $file = UploadedFile::fake()->createWithContent('import_dosen.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('dosen.import'), [
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('dosens', [
            'nidn' => '11223344',
            'program_studi_id' => $prodi->id,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'dosen1@polsa.ac.id',
            'role' => 'dosen',
        ]);
        $this->assertDatabaseHas('dosens', [
            'nidn' => '22334455',
            'program_studi_id' => $prodi->id,
        ]);
    }

    public function test_import_dosen_dengan_delimiter_titik_koma_dan_baris_duplikat(): void
    {
        $admin = $this->createAdmin();
        $prodi = $this->createProdi();

        // Already existing dosen
        $existingUser = User::create([
            'name' => 'Existing Dosen',
            'email' => 'exist@polsa.ac.id',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);
        Dosen::create([
            'user_id' => $existingUser->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99999999',
            'jabatan' => 'Dosen',
        ]);

        // Semicolon delimited CSV (Excel Indonesia format)
        $csvContent = "nama;nidn;email;kode_prodi;jabatan\n"
            . "Dosen Baru;33445566;baru@polsa.ac.id;TRPL;Dosen\n"
            . "Dosen Duplikat;99999999;exist@polsa.ac.id;TRPL;Dosen\n"
            . "Dosen Invalid Prodi;44556677;invalid@polsa.ac.id;XYZ;Dosen\n";

        $file = UploadedFile::fake()->createWithContent('import_dosen_semicolon.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('dosen.import'), [
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $response->assertSessionHas('import_warnings');

        $this->assertDatabaseHas('dosens', [
            'nidn' => '33445566',
        ]);
        $this->assertDatabaseMissing('dosens', [
            'nidn' => '44556677',
        ]);
    }

    public function test_admin_dapat_mengimpor_mahasiswa_melalui_csv(): void
    {
        $admin = $this->createAdmin();
        $prodi = $this->createProdi();
        TahunAkademik::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $csvContent = "nim,nama,kode_prodi,angkatan,semester,status\n"
            . "32240001,Ahmad Fauzi,TRPL,2024,1,Aktif\n"
            . "32240002,Budi Santoso,TRPL,2024,1,Aktif\n";

        $file = UploadedFile::fake()->createWithContent('import_mahasiswa.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('mahasiswa.import'), [
            'file' => $file,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mahasiswas', [
            'nim' => '32240001',
            'nama' => 'Ahmad Fauzi',
            'program_studi_id' => $prodi->id,
            'angkatan' => 2024,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => '32240001@polsa.ac.id',
            'role' => 'mahasiswa',
        ]);

        $this->assertDatabaseHas('mahasiswas', [
            'nim' => '32240002',
            'nama' => 'Budi Santoso',
        ]);
    }

    public function test_import_mahasiswa_melewati_nim_duplikat(): void
    {
        $admin = $this->createAdmin();
        $prodi = $this->createProdi();

        $existingUser = User::create([
            'name' => 'Existing Mhs',
            'email' => '32240099@polsa.ac.id',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);
        Mahasiswa::create([
            'user_id' => $existingUser->id,
            'nim' => '32240099',
            'nama' => 'Existing Mhs',
            'program_studi_id' => $prodi->id,
            'angkatan' => 2024,
            'status' => 'Aktif',
        ]);

        $csvContent = "nim;nama;kode_prodi;angkatan;semester;status\n"
            . "32240010;Mahasiswa Baru;TRPL;2024;1;Aktif\n"
            . "32240099;Mahasiswa Duplikat;TRPL;2024;1;Aktif\n";

        $file = UploadedFile::fake()->createWithContent('import_mahasiswa_semicolon.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('mahasiswa.import'), [
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $response->assertSessionHas('import_warnings');

        $this->assertDatabaseHas('mahasiswas', [
            'nim' => '32240010',
        ]);
    }
}
