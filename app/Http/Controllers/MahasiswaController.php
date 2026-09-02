<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SemesterMahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Mahasiswa::with(['programStudi', 'semesterMahasiswas.tahunAkademik', 'user']);

        if ($programStudiId = request('program_studi_id')) {
            $query->where('program_studi_id', $programStudiId);
        }

        if ($angkatan = request('angkatan')) {
            $query->where('angkatan', $angkatan);
        }

        if ($tahunAkademikId = request('tahun_akademik_id')) {
            $query->whereHas('semesterMahasiswas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            });
        }

        $mahasiswas = $query->latest()->paginate(10);

        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();
        $angkatans = Mahasiswa::distinct()->orderBy('angkatan')->pluck('angkatan');
        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('mahasiswa.index', compact('mahasiswas', 'programStudis', 'angkatans', 'tahunAkademiks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();
        $tahunAkademiks = TahunAkademik::orderByDesc('is_active')->orderByDesc('tahun')->get();

        return view('mahasiswa.create', compact('programStudis', 'tahunAkademiks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => [
                'required',
                'unique:mahasiswas,nim',
                function ($attribute, $value, $fail) {
                    if (User::where('email', $this->emailUntukNim($value))->exists()) {
                        $fail('NIM ini sudah dipakai untuk akun login lain.');
                    }
                },
            ],
            'nama' => 'required',
            'program_studi_id' => 'required|exists:program_studis,id',
            'angkatan' => 'required|digits:4',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'semester' => 'required|integer|min:1|max:14',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $this->emailUntukNim($request->nim),
            'password' => $request->nim,
            'role' => 'mahasiswa',
            'email_verified_at' => now(),
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'nama' => $request->nama,
            'program_studi_id' => $request->program_studi_id,
            'angkatan' => $request->angkatan,
        ]);
        SemesterMahasiswa::create([
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'semester' => $request->semester,
        ]);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();
        $tahunAkademiks = TahunAkademik::orderByDesc('is_active')->orderByDesc('tahun')->get();
        $semesterAktif = $mahasiswa->semesterMahasiswas()->latest()->first();

        return view('mahasiswa.edit', compact('mahasiswa', 'programStudis', 'tahunAkademiks', 'semesterAktif'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswas,nim,'.$mahasiswa->id,
            'nama' => 'required',
            'program_studi_id' => 'required|exists:program_studis,id',
            'angkatan' => 'required|digits:4',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'semester' => 'required|integer|min:1|max:14',
        ]);
        $mahasiswa->update([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'program_studi_id' => $request->program_studi_id,
            'angkatan' => $request->angkatan,
        ]);

        if ($mahasiswa->user) {
            $mahasiswa->user->update([
                'name' => $request->nama,
                'email' => $this->emailUntukNim($request->nim),
            ]);
        }

        $semesterAktif = $mahasiswa->semesterMahasiswas()->latest()->first();
        if ($semesterAktif) {
            $semesterAktif->update([
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'semester' => $request->semester,
            ]);
        }

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        if ($mahasiswa->user) {
            $mahasiswa->user->delete();
        }

        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    private function emailUntukNim(string $nim): string
    {
        return $nim.'@polsa.ac.id';
    }

    public function downloadTemplate(CsvImportService $csvService)
    {
        $headers = ['nim', 'nama', 'kode_prodi', 'angkatan', 'semester', 'status'];
        $samples = [
            ['32240001', 'Ahmad Fauzi', 'TRPL', '2024', '1', 'Aktif'],
            ['32240002', 'Budi Santoso', 'TI', '2024', '1', 'Aktif'],
        ];

        return $csvService->downloadTemplate('template_import_mahasiswa.csv', $headers, $samples);
    }

    public function import(Request $request, CsvImportService $csvService)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $rows = $csvService->parseCsv($request->file('file')->getRealPath());

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $imported = 0;
        $skipped = [];
        $programStudis = ProgramStudi::all()->keyBy(fn ($p) => strtoupper(trim($p->kode_prodi)));
        $activeTa = TahunAkademik::where('is_active', true)->first();

        foreach ($rows as $row) {
            $rowNum = $row['_row_number'] ?? '?';
            $nim = trim($row['nim'] ?? '');
            $nama = trim($row['nama'] ?? '');
            $kodeProdi = strtoupper(trim($row['kode_prodi'] ?? ''));
            $angkatan = trim($row['angkatan'] ?? '');
            $semester = (int) (trim($row['semester'] ?? '1') ?: 1);
            $status = trim($row['status'] ?? '') ?: 'Aktif';

            if ($nim === '' || $nama === '' || $kodeProdi === '' || $angkatan === '') {
                $skipped[] = "Baris {$rowNum}: Data tidak lengkap (nim, nama, kode_prodi, dan angkatan wajib diisi).";
                continue;
            }

            if (! preg_match('/^\d{4}$/', $angkatan)) {
                $skipped[] = "Baris {$rowNum}: Tahun angkatan ({$angkatan}) harus 4 digit angka.";
                continue;
            }

            if ($semester < 1 || $semester > 14) {
                $semester = 1;
            }

            if (Mahasiswa::where('nim', $nim)->exists()) {
                $skipped[] = "Baris {$rowNum}: NIM {$nim} sudah terdaftar.";
                continue;
            }

            $emailMhs = $this->emailUntukNim($nim);
            if (User::where('email', $emailMhs)->exists()) {
                $skipped[] = "Baris {$rowNum}: Akun email login {$emailMhs} sudah terdaftar.";
                continue;
            }

            $prodi = $programStudis->get($kodeProdi);
            if (! $prodi) {
                $prodi = ProgramStudi::whereRaw('UPPER(nama_prodi) = ?', [$kodeProdi])->first();
                if (! $prodi) {
                    $skipped[] = "Baris {$rowNum}: Kode program studi '{$kodeProdi}' tidak ditemukan.";
                    continue;
                }
            }

            $user = User::create([
                'name' => $nama,
                'email' => $emailMhs,
                'password' => $nim,
                'role' => 'mahasiswa',
                'roles' => ['mahasiswa'],
                'email_verified_at' => now(),
            ]);

            $mahasiswa = Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $nim,
                'nama' => $nama,
                'program_studi_id' => $prodi->id,
                'angkatan' => (int) $angkatan,
                'status' => $status,
            ]);

            if ($activeTa) {
                $mahasiswa->semesterMahasiswas()->create([
                    'tahun_akademik_id' => $activeTa->id,
                    'semester' => $semester,
                ]);
            }

            $imported++;
        }

        $msg = "Berhasil mengimpor {$imported} data mahasiswa.";
        if (! empty($skipped)) {
            $msg .= ' ' . count($skipped) . ' baris dilewati.';
            return back()->with('success', $msg)->with('import_warnings', $skipped);
        }

        return back()->with('success', $msg);
    }
}
