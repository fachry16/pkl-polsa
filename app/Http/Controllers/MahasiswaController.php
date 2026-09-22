<?php

namespace App\Http\Controllers;

use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SemesterMahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\CsvImportService;
use App\Services\PenilaianService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $query = Mahasiswa::with(['programStudi', 'semesterMahasiswas.tahunAkademik', 'user']);

        if ($user->isKaprodi() && ! $user->isAdmin()) {
            $query->where('program_studi_id', $user->dosen->program_studi_id);
        }

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

        if ($jenisKelas = request('jenis_kelas')) {
            $query->where('jenis_kelas', $jenisKelas);
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $mahasiswas = $query->latest()->paginate(10);

        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();
        $angkatans = Mahasiswa::distinct()->orderBy('angkatan')->pluck('angkatan');
        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('mahasiswa.index', compact('mahasiswas', 'programStudis', 'angkatans', 'tahunAkademiks'));
    }

    /**
     * Histori nilai per semester: nilai tiap komponen + nilai akhir per MK mahasiswa.
     */
    public function historiNilai(Mahasiswa $mahasiswa)
    {
        $this->prodiGuard($mahasiswa);

        return view('mahasiswa.nilai', $this->dataKhsMahasiswa($mahasiswa));
    }

    public function khsExport(Mahasiswa $mahasiswa)
    {
        $this->prodiGuard($mahasiswa);

        $data = $this->dataKhsMahasiswa($mahasiswa);

        $pdf = Pdf::loadView('mahasiswa.khs-pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download('KHS-'.$mahasiswa->nim.'-'.str_replace(' ', '-', $mahasiswa->nama).'.pdf');
    }

    protected function dataKhsMahasiswa(Mahasiswa $mahasiswa): array
    {
        $semesters = [];

        foreach ($this->pengampusTerurut($mahasiswa) as $p) {
            $nilaiRows = LmsNilaiMahasiswa::where('pengampu_id', $p->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->get()
                ->keyBy('komponen');

            $komponen = [];
            foreach (PenilaianService::KOMPONEN as $k) {
                $nilai = $nilaiRows->get($k)?->nilai;
                if ($nilai !== null) {
                    $komponen[$k] = (float) $nilai;
                }
            }

            $akhir = isset($nilaiRows['akhir']) ? (float) $nilaiRows['akhir']->nilai : null;

            $semesters[$this->kunciSemester($p)][] = [
                'pengampu' => $p,
                'komponen' => $komponen,
                'akhir' => $akhir,
                'huruf' => PenilaianService::konversiHuruf($akhir),
            ];
        }

        $this->sortSemester($semesters);

        return compact('mahasiswa', 'semesters');
    }

    /**
     * Identitas mahasiswa (self-service): data diri + KHS + transkrip informatif.
     */
    public function identitas()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        abort_unless($mahasiswa, 404);

        $khs = $this->dataKhsMahasiswa($mahasiswa);
        $transkrip = $this->dataTranskripMahasiswa($mahasiswa);

        return view('mahasiswa.identitas', $khs + $transkrip);
    }

    /**
     * Status mahasiswa: status utama dari data mahasiswa.
     */
    public function status(Mahasiswa $mahasiswa)
    {
        $this->prodiGuard($mahasiswa);

        return view('mahasiswa.status', compact('mahasiswa'));
    }

    /**
     * Transkrip: gabungan seluruh MK (semester 1 s.d. semester berjalan) + IPS per semester + IPK kumulatif.
     */
    public function transkrip(Mahasiswa $mahasiswa)
    {
        $this->prodiGuard($mahasiswa);

        return view('mahasiswa.transkrip', $this->dataTranskripMahasiswa($mahasiswa));
    }

    public function transkripExport(Mahasiswa $mahasiswa)
    {
        $this->prodiGuard($mahasiswa);

        $data = $this->dataTranskripMahasiswa($mahasiswa);

        $pdf = Pdf::loadView('mahasiswa.transkrip-pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download('Transkrip-'.$mahasiswa->nim.'-'.str_replace(' ', '-', $mahasiswa->nama).'.pdf');
    }

    protected function dataTranskripMahasiswa(Mahasiswa $mahasiswa): array
    {
        $perSemester = [];

        $akhirRows = LmsNilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('komponen', 'akhir')
            ->with(['pengampu.mataKuliah', 'pengampu.tahunAkademik'])
            ->get();

        foreach ($akhirRows as $row) {
            $p = $row->pengampu;
            $mk = $p?->mataKuliah;
            if (! $mk) {
                continue;
            }

            $perSemester[$this->kunciSemester($p)][] = [
                'mata_kuliah' => $mk,
                'sks' => $p->total_sks,
                'nilai' => (float) $row->nilai,
                'huruf' => PenilaianService::konversiHuruf((float) $row->nilai),
                'bobot_mutu' => PenilaianService::konversiBobotMutu((float) $row->nilai),
            ];
        }

        $this->sortSemester($perSemester);

        $totalSks = 0;
        $totalSksMutu = 0;

        foreach ($perSemester as $semester => $rows) {
            $sksSem = array_sum(array_column($rows, 'sks'));
            $mutuSem = array_sum(array_map(fn ($r) => $r['sks'] * $r['bobot_mutu'], $rows));
            $totalSks += $sksSem;
            $totalSksMutu += $mutuSem;

            $perSemester[$semester] = [
                'rows' => $rows,
                'total_sks' => $sksSem,
                'ips' => $sksSem > 0 ? round($mutuSem / $sksSem, 2) : null,
            ];
        }

        $ipk = $totalSks > 0 ? round($totalSksMutu / $totalSks, 2) : null;

        return compact('mahasiswa', 'perSemester', 'ipk', 'totalSks');
    }

    protected function pengampusTerurut(Mahasiswa $mahasiswa): Collection
    {
        return $mahasiswa->pengampus()
            ->with(['mataKuliah', 'tahunAkademik', 'dosen.user'])
            ->get()
            ->sortBy(fn ($p) => ($p->tahunAkademik?->tahun ?? '0').'-'.($p->semester_akademik === 'Genap' ? 2 : 1))
            ->values();
    }

    protected function prodiGuard(Mahasiswa $mahasiswa): void
    {
        $user = auth()->user();

        if ($user->isKaprodi() && ! $user->isAdmin()) {
            abort_unless($mahasiswa->program_studi_id === $user->dosen->program_studi_id, 403);
        }
    }

    protected function kunciSemester($pengampu): string
    {
        return ($pengampu->tahunAkademik?->tahun ?? '-').' '.ucfirst($pengampu->semester_akademik ?? '');
    }

    protected function sortSemester(array &$rows): void
    {
        $urutan = ['Ganjil' => 1, 'Genap' => 2, 'Pendek' => 3];

        uksort($rows, function ($a, $b) use ($urutan) {
            [$taA, $smA] = array_pad(explode(' ', $a), 2, '');
            [$taB, $smB] = array_pad(explode(' ', $b), 2, '');

            return [$taA, $urutan[$smA] ?? 9] <=> [$taB, $urutan[$smB] ?? 9];
        });
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
            'jenis_kelas' => 'nullable|in:Reguler,Karyawan',
            'status' => 'required|in:Aktif,DO,Cuti,Lulus,Non Aktif',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $this->emailUntukNim($request->nim),
            'password' => $request->nim,
            'role' => 'mahasiswa',
            'harus_ganti_password' => true,
            'email_verified_at' => now(),
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'nama' => $request->nama,
            'program_studi_id' => $request->program_studi_id,
            'angkatan' => $request->angkatan,
            'jenis_kelas' => $request->jenis_kelas ?: 'Reguler',
            'status' => $request->input('status', 'Aktif'),
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
            'jenis_kelas' => 'nullable|in:Reguler,Karyawan',
            'status' => 'required|in:Aktif,DO,Cuti,Lulus,Non Aktif',
        ]);
        $mahasiswa->update([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'program_studi_id' => $request->program_studi_id,
            'angkatan' => $request->angkatan,
            'jenis_kelas' => $request->jenis_kelas ?: $mahasiswa->jenis_kelas ?: 'Reguler',
            'status' => $request->input('status', $mahasiswa->status),
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
        $headers = ['nim', 'nama', 'kode_prodi', 'angkatan', 'semester', 'status', 'jenis_kelas'];
        $samples = [
            ['32240001', 'Ahmad Fauzi', '14', '2024', '1', 'Aktif', 'Reguler'],
            ['32240002', 'Budi Santoso', '11', '2024', '1', 'Aktif', 'Karyawan'],
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
            $jenisKelasRaw = trim($row['jenis_kelas'] ?? '');
            $jenisKelas = (preg_match('/karyawan|sore|malam|B/i', $jenisKelasRaw)) ? 'Karyawan' : 'Reguler';

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
                'harus_ganti_password' => true,
                'email_verified_at' => now(),
            ]);

            $mahasiswa = Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $nim,
                'nama' => $nama,
                'program_studi_id' => $prodi->id,
                'angkatan' => (int) $angkatan,
                'status' => $status,
                'jenis_kelas' => $jenisKelas,
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
            $msg .= ' '.count($skipped).' baris dilewati.';

            return back()->with('success', $msg)->with('import_warnings', $skipped);
        }

        return back()->with('success', $msg);
    }
}
