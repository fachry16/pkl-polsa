<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Dosen;
use App\Models\LmsAbsensi;
use App\Models\LmsInstrumenCpmk;
use App\Models\LmsNilaiMahasiswa;
use App\Models\LmsSesiAbsensi;
use App\Models\LmsSubmission;
use App\Models\LmsTopikKomentar;
use App\Models\LmsTugas;
use App\Models\Pengampu;
use App\Models\RpsPertemuan;
use App\Models\User;
use App\Notifications\NilaiDiberikan;
use App\Notifications\TugasBaru;
use App\Rules\LmsFileMime;
use App\Services\AssessmentCalculationService;
use App\Services\GoogleDriveService;
use App\Services\PenilaianService;
use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmsTugasController extends Controller
{
    private function authorizeRead(Pengampu $pengampu): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }

        $dosen = $user->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
    }

    private function authorizeWrite(Pengampu $pengampu): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }

        $dosen = $user->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
    }

    private function abortIfNilaiTerkunci(Pengampu $pengampu): void
    {
        abort_if($pengampu->assessment?->isNilaiTerkunci(), 403, 'Nilai telah disetujui Kaprodi dan terkunci. Buka kunci oleh admin terlebih dahulu.');
    }

    public function index(Pengampu $pengampu)
    {
        $this->authorizeRead($pengampu);

        $pengampu->load(['mataKuliah.rps.tugas', 'mataKuliah.rps.pertemuans', 'tahunAkademik']);
        $tugas = $pengampu->lmsTugas()->withCount('submissions')->latest()->paginate(10);
        $pertemuans = $pengampu->rpsPertemuans();
        $rpsTugasList = $pengampu->mataKuliah?->rps?->tugas ?? collect();

        return view('lms.tugas.index', compact('pengampu', 'tugas', 'pertemuans', 'rpsTugasList'));
    }

    public function store(Request $request, Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $request->validate([
            'judul' => 'required|string|max:255',
            'instruksi' => 'required|string',
            'rps_pertemuan_id' => ['nullable', 'exists:rps_pertemuans,id', $this->pertemuanMilikKelas($pengampu)],
            'deadline' => 'required|date',
            'bobot_nilai' => 'required|integer|min:0|max:100',
            'batas_upload_mb' => 'nullable|integer|min:1|max:50',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'pengampu_id' => $pengampu->id,
            'judul' => $request->judul,
            'instruksi' => $request->instruksi,
            'rps_pertemuan_id' => $request->rps_pertemuan_id,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai,
            'batas_upload_mb' => $request->batas_upload_mb,
        ];

        if ($request->hasFile('file')) {
            $driveService = app(GoogleDriveService::class);
            $mkLabel = ($pengampu->mataKuliah->kode ?? 'MK').' - '.($pengampu->kelas ?? 'Kelas');
            $customName = 'Lampiran_'.$request->file('file')->getClientOriginalName();
            $data['file_lampiran'] = $driveService->storeFile($request->file('file'), 'lms/tugas', [$mkLabel, 'Tugas', $request->judul], $customName);
        }

        $tugas = LmsTugas::create($data);

        $this->notifyTugasPublikasi($pengampu, $tugas);

        return back()->with('toast_success', 'Tugas berhasil ditambahkan.');
    }

    public function tugaskan(Request $request, Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        $request->validate([
            'batas_upload_mb' => 'nullable|integer|min:1|max:50',
            'deadline' => 'nullable|date',
        ]);

        $updateData = ['is_active' => true];
        if ($request->filled('batas_upload_mb')) {
            $updateData['batas_upload_mb'] = (int) $request->batas_upload_mb;
        }
        if ($request->filled('deadline')) {
            $updateData['deadline'] = $request->deadline;
        }

        $tugas->update($updateData);

        $this->notifyTugasPublikasi($pengampu, $tugas);

        return back()->with('toast_success', 'Tugas berhasil dipublikasikan ke kelas.');
    }

    public function show(Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeRead($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        Auth::user()->unreadNotifications()
            ->where('data->pengampu_id', $pengampu->id)
            ->where('data->url', route('lms.tugas.show', [$pengampu->id, $tugas->id]))
            ->update(['read_at' => now()]);

        $pengampu->load('mataKuliah', 'tahunAkademik', 'dosen.user');

        $mahasiswas = $pengampu->mahasiswas()->orderBy('nim')->paginate(20);
        $submissions = $tugas->submissions()->with('mahasiswa')->get()->keyBy('mahasiswa_id');

        $komentarsKelas = LmsTopikKomentar::where('tipe_topik', 'tugas')
            ->where('topik_id', $tugas->id)
            ->where('is_private', false)
            ->with('user')
            ->oldest()
            ->get();

        $komentarsPribadi = LmsTopikKomentar::where('tipe_topik', 'tugas')
            ->where('topik_id', $tugas->id)
            ->where('is_private', true)
            ->with(['user', 'mahasiswa.user'])
            ->oldest()
            ->get()
            ->groupBy('mahasiswa_id');

        return view('lms.tugas.show', compact('pengampu', 'tugas', 'mahasiswas', 'submissions', 'komentarsKelas', 'komentarsPribadi'));
    }

    public function edit(Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        if (! $tugas->canBeModified()) {
            return redirect()->route('lms.tugas.index', $pengampu->id)
                ->with('toast_error', 'Batas waktu 1x24 jam untuk mengedit tugas telah berakhir.');
        }

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $pertemuans = $pengampu->rpsPertemuans();

        return view('lms.tugas.edit', compact('pengampu', 'tugas', 'pertemuans'));
    }

    public function update(Request $request, Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        if (! $tugas->canBeModified()) {
            return redirect()->route('lms.tugas.index', $pengampu->id)
                ->with('toast_error', 'Batas waktu 1x24 jam untuk mengedit tugas telah berakhir.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'instruksi' => 'required|string',
            'rps_pertemuan_id' => ['nullable', 'exists:rps_pertemuans,id', $this->pertemuanMilikKelas($pengampu)],
            'deadline' => 'required|date',
            'bobot_nilai' => 'required|integer|min:0|max:100',
            'batas_upload_mb' => 'nullable|integer|min:1|max:50',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'judul' => $request->judul,
            'instruksi' => $request->instruksi,
            'rps_pertemuan_id' => $request->rps_pertemuan_id,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai,
            'batas_upload_mb' => $request->batas_upload_mb,
        ];

        if ($request->hasFile('file')) {
            $driveService = app(GoogleDriveService::class);
            if ($tugas->file_lampiran) {
                $driveService->deleteFile($tugas->file_lampiran);
            }

            $mkLabel = ($pengampu->mataKuliah->kode ?? 'MK').' - '.($pengampu->kelas ?? 'Kelas');
            $customName = 'Lampiran_'.$request->file('file')->getClientOriginalName();
            $data['file_lampiran'] = $driveService->storeFile($request->file('file'), 'lms/tugas', [$mkLabel, 'Tugas', $request->judul ?: $tugas->judul], $customName);
        }

        $tugas->update($data);

        return redirect()
            ->route('lms.tugas.index', $pengampu->id)
            ->with('toast_success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        if (! $tugas->canBeModified()) {
            return back()->with('toast_error', 'Batas waktu 1x24 jam untuk menghapus tugas telah berakhir.');
        }

        $driveService = app(GoogleDriveService::class);

        if ($tugas->file_lampiran) {
            $driveService->deleteFile($tugas->file_lampiran);
        }

        foreach ($tugas->submissions as $submission) {
            if ($submission->file_jawaban) {
                $driveService->deleteFile($submission->file_jawaban);
            }

            $submission->delete();
        }

        $tugas->delete();

        return back()->with('toast_success', 'Tugas berhasil dihapus.');
    }

    public function nilai(Request $request, LmsSubmission $submission)
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        if (! $user->isAdmin()) {
            abort_if(! $dosen || $submission->lmsTugas->pengampu->dosen_id !== $dosen->id, 403);
        }

        $this->abortIfNilaiTerkunci($submission->lmsTugas->pengampu);

        $request->validate([
            'nilai' => 'nullable|numeric|min:0|max:100',
            'catatan_dosen' => 'nullable|string',
        ]);

        $submission->update([
            'nilai' => $request->nilai,
            'catatan_dosen' => $request->catatan_dosen,
        ]);

        $pengampu = $submission->lmsTugas->pengampu;

        app(PenilaianService::class)->simpanNilaiMahasiswa($pengampu, $submission->mahasiswa);

        if ($submission->nilai !== null && $submission->mahasiswa?->user) {
            $submission->mahasiswa->user->notify(new NilaiDiberikan($pengampu, $submission));
        }

        return back()->with('toast_success', 'Nilai berhasil disimpan.');
    }

    public function rekap(Pengampu $pengampu)
    {
        $this->authorizeRead($pengampu);

        [
            'mahasiswas' => $mahasiswas,
            'tugasList' => $tugasList,
            'nilaiByMhs' => $nilaiByMhs,
            'bobot' => $bobot,
        ] = $this->dataRekap($pengampu);

        $instrumenCpmk = $pengampu->instrumenCpmk()->with('cpmk')->get();

        $calc = app(AssessmentCalculationService::class);
        $cpmkConfig = $calc->cpmkConfigForMataKuliah($pengampu->mataKuliah);

        $assessment = Assessment::where('pengampu_id', $pengampu->id)->first();
        $pengampu->setRelation('assessment', $assessment);

        $terkunci = $assessment?->isNilaiTerkunci() ?? false;

        $belumDinilai = $mahasiswas
            ->filter(fn ($m) => $nilaiByMhs->get($m->id)?->firstWhere('komponen', 'akhir')?->nilai === null)
            ->count();

        return view('lms.tugas.rekap', compact('pengampu', 'mahasiswas', 'tugasList', 'nilaiByMhs', 'bobot', 'instrumenCpmk', 'cpmkConfig', 'belumDinilai', 'terkunci'));
    }

    /**
     * Export rekap nilai kelas (print / pdf / excel).
     */
    public function export(Pengampu $pengampu)
    {
        $this->authorizeRead($pengampu);

        $data = $this->dataRekap($pengampu);
        $data['info'] = $this->rekapInfo($pengampu, $data['mahasiswas'], $data['tugasList']);
        $data['rows'] = $this->nilaiBaris($pengampu, $data);

        $format = request()->query('format', 'print');

        if ($format === 'excel') {
            return $this->exportRekapExcel($pengampu, $data);
        }

        $view = view('lms.tugas.rekap-export', ['pengampu' => $pengampu] + $data);

        if ($format === 'pdf') {
            return Pdf::loadHTML($view->render())
                ->setPaper('a4', 'landscape')
                ->download('rekap-nilai-'.($pengampu->mataKuliah?->kode ?? 'mk').'-kelas-'.($pengampu->kelas ?? 'x').now()->format('YmdHis').'.pdf');
        }

        return $view;
    }

    private function dataRekap(Pengampu $pengampu): array
    {
        $pengampu->load('mataKuliah', 'tahunAkademik');

        $mahasiswas = $pengampu->mahasiswas()->orderBy('nim')->get();
        $tugasList = $pengampu->lmsTugas()->with('submissions')->get();

        $nilaiByMhs = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
            ->whereIn('mahasiswa_id', $mahasiswas->pluck('id'))
            ->get()
            ->groupBy('mahasiswa_id');

        $bobot = app(PenilaianService::class)->bobotKomponen($pengampu);

        return compact('mahasiswas', 'tugasList', 'nilaiByMhs', 'bobot');
    }

    /**
     * Data pelengkap untuk kop export (identitas kelas, dosen, kehadiran, kaprodi).
     */
    private function rekapInfo(Pengampu $pengampu, $mahasiswas, $tugasList): array
    {
        $pengampu->loadMissing([
            'mataKuliah.kurikulum.programStudi',
            'tahunAkademik',
            'dosen.user',
        ]);

        $sesiIds = LmsSesiAbsensi::where('pengampu_id', $pengampu->id)->pluck('id');

        $hadirCounts = collect();
        if ($sesiIds->isNotEmpty()) {
            $hadirCounts = LmsAbsensi::whereIn('sesi_id', $sesiIds)
                ->whereIn('mahasiswa_id', $mahasiswas->pluck('id'))
                ->get()
                ->where('status', 'hadir')
                ->groupBy('mahasiswa_id')
                ->map->count();
        }

        $prodiId = $pengampu->mataKuliah?->kurikulum?->program_studi_id
            ?? $pengampu->dosen?->program_studi_id;

        $kaprodi = Dosen::where('program_studi_id', $prodiId)
            ->where('jabatan', 'Kaprodi')
            ->with('user')
            ->first();

        $approval = $pengampu->assessment?->approval;

        $romanLabel = static function (int $n): string {
            $map = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII', 'XVIII', 'XIX', 'XX'];

            return $map[$n - 1] ?? (string) $n;
        };

        $romawi = [];
        foreach ($tugasList as $i => $tugas) {
            $romawi[] = $romanLabel($i + 1);
        }

        return [
            'institusi' => 'Politeknik Sawnggalih Aji',
            'semester' => ucfirst((string) ($pengampu->tahunAkademik?->semester ?? '')),
            'tahun' => (string) ($pengampu->tahunAkademik?->tahun ?? ''),
            'dosen' => $pengampu->dosen?->user?->name ?? '-',
            'nidn' => $pengampu->dosen?->nidn ?? '-',
            'prodi' => $pengampu->mataKuliah?->kurikulum?->programStudi?->nama_prodi
                ?? $pengampu->dosen?->programStudi?->nama_prodi
                ?? '-',
            'matakuliah' => $pengampu->mataKuliah?->nama ?? '-',
            'kodeKelas' => trim(($pengampu->mataKuliah?->kode ?? '').' '.($pengampu->kelas ?? '')),
            'semesterTingkat' => $pengampu->mataKuliah?->semester ?? '-',
            'jumlahTM' => $pengampu->rpsPertemuans()->count(),
            'totalSesi' => $sesiIds->count(),
            'hadirCount' => $hadirCounts,
            'rataKehadiran' => $sesiIds->isEmpty() ? null : round($hadirCounts->avg() ?? 0),
            'romawi' => $romawi,
            'kaprodiNama' => $approval?->penyetuju?->name ?? $kaprodi?->user?->name ?? null,
            'tanggalDisetujui' => $approval?->disetujui_at,
        ];
    }

    /**
     * Nilai terhitung per mahasiswa untuk tabel export rekap.
     */
    private function nilaiBaris(Pengampu $pengampu, array $data): array
    {
        $service = app(PenilaianService::class);
        $rows = [];

        foreach ($data['mahasiswas'] as $mahasiswa) {
            $map = $data['nilaiByMhs']->get($mahasiswa->id)?->keyBy('komponen') ?? collect();

            $nilaiTugas = $map->get('tugas')?->nilai ?? $service->hitungTugas($pengampu, $mahasiswa);
            $nilaiAbsensi = $service->hitungAbsensi($pengampu, $mahasiswa);
            $nilaiAkhir = $map->get('akhir')?->nilai ?? $service->hitungNilaiAkhir($pengampu, $mahasiswa);

            $tugasPerKolom = [];
            foreach ($data['tugasList'] as $tugas) {
                $submission = $tugas->submissions->where('mahasiswa_id', $mahasiswa->id)->first();
                $tugasPerKolom[] = $submission?->nilai;
            }

            $rows[$mahasiswa->id] = [
                'mahasiswa' => $mahasiswa,
                'tugas' => $tugasPerKolom,
                'komponen' => [
                    'tugas' => $nilaiTugas,
                    'absensi' => $nilaiAbsensi,
                    'keaktifan' => $map->get('keaktifan')?->nilai,
                    'etika' => $map->get('etika')?->nilai,
                    'uts' => $map->get('uts')?->nilai,
                    'uas' => $map->get('uas')?->nilai,
                ],
                'hadir' => $data['info']['hadirCount']->get($mahasiswa->id),
                'akhir' => $nilaiAkhir,
                'huruf' => konversiNilaiHurufPolsa($nilaiAkhir),
            ];
        }

        return $rows;
    }

    protected function exportRekapExcel(Pengampu $pengampu, array $d)
    {
        $e = fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $fmt = static function ($v): string {
            if ($v === null || $v === '') {
                return '';
            }

            return rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
        };
        $kontribusi = static function ($nilai, $persen) use ($fmt) {
            return $nilai === null ? '' : $fmt((float) $nilai * (float) $persen / 100);
        };

        $cell = fn (int $index, string $value, ?int $mergeAcross = null, ?int $mergeDown = null): string => '<Cell ss:Index="'.$index.'"'
            .($mergeAcross !== null ? ' ss:MergeAcross="'.$mergeAcross.'"' : '')
            .($mergeDown !== null ? ' ss:MergeDown="'.$mergeDown.'"' : '')
            .'><Data ss:Type="String">'.$e($value).'</Data></Cell>';

        $rows = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $rows[] = '<?mso-application progid="Excel.Sheet"?>';
        $rows[] = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $rows[] = '<Worksheet ss:Name="Daftar Nilai"><Table>';

        $info = $d['info'];
        $bobot = $d['bobot'];
        $tugasCount = $d['tugasList']->count();

        // Indeks kolom mengikuti referensi format nilai DM
        $aktifCol = 5 + $tugasCount;
        $etikaCol = 7 + $tugasCount;
        $presensiCol = 9 + $tugasCount;
        $ujianCol = 11 + $tugasCount;
        $jumlahCol = 15 + $tugasCount;
        $naCol = 16 + $tugasCount;

        // Kop dokumen
        $rows[] = '<Row>'.$cell(1, 'DAFTAR NILAI', 18).'</Row>';
        $rows[] = '<Row>'.$cell(1, 'Semester : '.$info['semester'].' '.$info['tahun'], 18).'</Row>';
        $rows[] = '<Row>'.$cell(1, $info['institusi'], 18).'</Row>';
        $rows[] = '<Row/>';
        $rows[] = '<Row>'.$cell(1, 'Dosen', 1).$cell(3, $info['dosen'])
            .$cell(13, 'Kelas').$cell(15, ':').$cell(16, (string) $pengampu->kelas).'</Row>';
        $rows[] = '<Row>'.$cell(1, 'NIDN/NUPN', 1).$cell(3, $info['nidn'])
            .$cell(13, 'Semester').$cell(15, ':').$cell(16, (string) $info['semesterTingkat']).'</Row>';
        $rows[] = '<Row>'.$cell(1, 'Program Studi', 1).$cell(3, $info['prodi'])
            .$cell(13, 'Kode Kelas').$cell(15, ':').$cell(16, $info['kodeKelas']).'</Row>';
        $rows[] = '<Row>'.$cell(1, 'Nama Matakuliah', 1).$cell(3, $info['matakuliah'])
            .$cell(13, 'Jumlah TM').$cell(15, ':').$cell(16, (string) $info['jumlahTM']).'</Row>';
        $rows[] = '<Row>'.$cell(1, '', 1).$cell(3, '')
            .$cell(13, 'Rata-rata Kehadiran       :').$cell(16, $info['rataKehadiran'] === null ? '' : (string) $info['rataKehadiran']).'</Row>';
        $rows[] = '<Row/>';

        // Header dua tingkat
        $rows[] = '<Row>'
            .$cell(1, 'No', 0, 1)
            .$cell(2, 'NIM', 0, 1)
            .$cell(3, 'Nama Mahasiswa', 0, 1)
            .$cell(4, 'Tugas', $tugasCount)
            .$cell($aktifCol, 'Aktif', 1)
            .$cell($etikaCol, 'Etika', 1)
            .$cell($presensiCol, '', 1)
            .$cell($ujianCol, 'UJIAN', 3)
            .$cell($jumlahCol, 'Jumlah', 0, 1)
            .$cell($naCol, 'NA', 0, 1)
            .'</Row>';

        $subs = '';
        foreach ($info['romawi'] as $i => $label) {
            $subs .= $cell(4 + $i, $label);
        }
        $subs .= $cell(4 + $tugasCount, '% tugas')
            .$cell($aktifCol, 'K').$cell($aktifCol + 1, '% K')
            .$cell($etikaCol, 'E').$cell($etikaCol + 1, '% E')
            .$cell($presensiCol, 'TOT').$cell($presensiCol + 1, '%P')
            .$cell($ujianCol, 'UTS').$cell($ujianCol + 1, '% MID').$cell($ujianCol + 2, 'UAS').$cell($ujianCol + 3, '% UAS');
        $rows[] = '<Row>'.$subs.'</Row>';

        // Baris nilai mahasiswa
        $no = 0;
        foreach ($d['rows'] as $row) {
            $no++;
            $nilai = $row['komponen'];
            $cols = [];
            $cols[] = (string) $no;
            $cols[] = $row['mahasiswa']->nim;
            $cols[] = $row['mahasiswa']->nama;
            foreach ($row['tugas'] as $v) {
                $cols[] = $fmt($v);
            }
            $cols[] = $kontribusi($nilai['tugas'], $bobot['tugas']);
            $cols[] = $fmt($nilai['keaktifan']);
            $cols[] = $kontribusi($nilai['keaktifan'], $bobot['keaktifan']);
            $cols[] = $fmt($nilai['etika']);
            $cols[] = $kontribusi($nilai['etika'], $bobot['etika']);
            $cols[] = $row['hadir'] === null ? '' : (string) $row['hadir'];
            $cols[] = $kontribusi($nilai['absensi'], $bobot['absensi']);
            $cols[] = $fmt($nilai['uts']);
            $cols[] = $kontribusi($nilai['uts'], $bobot['uts']);
            $cols[] = $fmt($nilai['uas']);
            $cols[] = $kontribusi($nilai['uas'], $bobot['uas']);
            $cols[] = $fmt($row['akhir']);
            $cols[] = $row['huruf'] ?? '';

            $rows[] = '<Row>'.implode('', array_map(fn ($c) => '<Cell><Data ss:Type="String">'.$e((string) $c).'</Data></Cell>', $cols)).'</Row>';
        }

        // Blok tanda tangan (Pengesahan)
        $tanggal = $info['tanggalDisetujui']?->format('d/m/Y') ?? now()->format('d/m/Y');
        $rows[] = '<Row/>';
        $rows[] = '<Row>'.$cell(15, 'Purworejo, '.$tanggal).$cell(19, 'Purworejo, '.$tanggal).'</Row>';
        $rows[] = '<Row>'.$cell(15, 'Dosen Pengampu').$cell(19, 'Kaprodi').'</Row>';
        $rows[] = '<Row>'.$cell(15, '✓ Disetujui').$cell(19, '✓ Disetujui').'</Row>';
        $rows[] = '<Row>'.$cell(15, $info['dosen']).$cell(19, $info['kaprodiNama'] ?? '-').'</Row>';
        $rows[] = '<Row>'.$cell(15, '(Tanda tangan)').$cell(19, '(Tanda tangan)').'</Row>';

        $rows[] = '</Table></Worksheet></Workbook>';

        return response(implode("\n", $rows), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="rekap-nilai-'.($pengampu->mataKuliah?->kode ?? 'mk').'-kelas-'.($pengampu->kelas ?? 'x').now()->format('YmdHis').'.xls"',
        ]);
    }

    public function simpanKomponen(Request $request, Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $this->abortIfNilaiTerkunci($pengampu);

        $request->validate([
            'nilai' => 'required|array',
            'nilai.*.quiz' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.praktikum' => 'nullable|numeric|min:0|max:100',
            'nilai.*.project' => 'nullable|numeric|min:0|max:100',
            'nilai.*.absensi' => 'nullable|numeric|min:0|max:100',
            'nilai.*.keaktifan' => 'nullable|numeric|min:0|max:100',
            'nilai.*.etika' => 'nullable|numeric|min:0|max:100',
        ]);

        $service = app(PenilaianService::class);
        $komponenLain = ['quiz', 'uts', 'uas', 'praktikum', 'project', 'absensi', 'keaktifan', 'etika'];

        foreach ($request->input('nilai', []) as $mahasiswaId => $nilaiKomponen) {
            $mahasiswa = $pengampu->mahasiswas()->find($mahasiswaId);

            if (! $mahasiswa) {
                continue;
            }

            foreach ($komponenLain as $komponen) {
                $nilai = $nilaiKomponen[$komponen] ?? null;

                $nilai = ($nilai === '' || $nilai === null) ? null : $nilai;

                if ($nilai === null) {
                    LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                        ->where('mahasiswa_id', $mahasiswa->id)
                        ->where('komponen', $komponen)
                        ->delete();

                    continue;
                }

                LmsNilaiMahasiswa::updateOrCreate(
                    ['pengampu_id' => $pengampu->id, 'mahasiswa_id' => $mahasiswa->id, 'komponen' => $komponen],
                    ['nilai' => $nilai]
                );
            }

            $service->simpanNilaiMahasiswa($pengampu, $mahasiswa);
        }

        $synced = $this->syncToAssessment($pengampu, $service);

        if (! $synced) {
            return back()->with('toast_success', 'Nilai LMS berhasil disimpan. Catatan: Bobot CPMK di RPS belum dikonfigurasi, silakan lengkapi RPS agar nilai terkirim ke Asesmen OBE.');
        }

        return back()->with('toast_success', 'Penilaian kelas berhasil disimpan & dikirim ke Modul Asesmen OBE.');
    }

    public function hitungUlangNilai(Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $this->abortIfNilaiTerkunci($pengampu);

        $service = app(PenilaianService::class);
        $service->simpanNilaiKelas($pengampu);

        $synced = $this->syncToAssessment($pengampu, $service);

        $message = $synced
            ? 'Penilaian kelas berhasil disimpan & dikirim ke Modul Asesmen OBE.'
            : 'Nilai LMS berhasil dihitung ulang. Catatan: Bobot CPMK di RPS belum dikonfigurasi, silakan lengkapi RPS agar nilai terkirim ke Asesmen OBE.';

        $referer = request()->headers->get('referer');
        if ($referer && ! str_contains($referer, 'hitung-ulang-nilai')) {
            return back()->with('toast_success', $message);
        }

        return redirect()->route('lms.show', ['pengampu' => $pengampu->id, 'tab' => 'rekap_nilai'])->with('toast_success', $message);
    }

    public function simpanInstrumenCpmk(Request $request, Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $this->abortIfNilaiTerkunci($pengampu);

        $request->validate([
            'cpmk_id' => 'required|exists:cpmks,id',
            'komponen' => 'required|in:tugas,quiz,uts,uas,praktikum,project,absensi,keaktifan,etika',
            'bobot_kontribusi' => 'required|numeric|min:0|max:100',
        ]);

        LmsInstrumenCpmk::updateOrCreate(
            [
                'pengampu_id' => $pengampu->id,
                'cpmk_id' => $request->cpmk_id,
                'komponen' => $request->komponen,
            ],
            ['bobot_kontribusi' => $request->bobot_kontribusi]
        );

        return back()->with('toast_success', 'Pemetaan instrumen → CPMK berhasil disimpan.');
    }

    public function hapusInstrumenCpmk(Pengampu $pengampu, LmsInstrumenCpmk $instrumen)
    {
        $this->authorizeWrite($pengampu);

        $this->abortIfNilaiTerkunci($pengampu);

        abort_if($instrumen->pengampu_id !== $pengampu->id, 404);

        $instrumen->delete();

        return back()->with('toast_success', 'Pemetaan instrumen → CPMK berhasil dihapus.');
    }

    private function syncToAssessment(Pengampu $pengampu, PenilaianService $service): bool
    {
        $assessment = Assessment::firstOrCreate(
            ['pengampu_id' => $pengampu->id],
            ['status' => Assessment::STATUS_DRAFT, 'created_by' => Auth::id()]
        );

        $calc = app(AssessmentCalculationService::class);
        $config = $calc->cpmkConfigForMataKuliah($pengampu->mataKuliah);

        $instrumenMap = LmsInstrumenCpmk::where('pengampu_id', $pengampu->id)
            ->get()
            ->groupBy('cpmk_id');

        $scoredCount = 0;
        if ($config->isNotEmpty()) {
            foreach ($pengampu->mahasiswas as $mahasiswa) {
                $nilaiByKomponen = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->pluck('nilai', 'komponen');

                foreach ($config as $cpmkId => $meta) {
                    $instrumens = $instrumenMap->get($cpmkId);

                    if ($instrumens && $instrumens->isNotEmpty()) {
                        $totalBobot = $instrumens->sum('bobot_kontribusi');
                        $weightedSum = 0;
                        $hasValue = false;

                        foreach ($instrumens as $instrumen) {
                            $nilai = $nilaiByKomponen->get($instrumen->komponen);
                            if ($nilai !== null) {
                                $weightedSum += ($nilai / 100) * $instrumen->bobot_kontribusi;
                                $hasValue = true;
                            }
                        }

                        $skorCpmk = $hasValue && $totalBobot > 0
                            ? round(($weightedSum / $totalBobot) * $meta['bobot'], 2)
                            : null;
                    } else {
                        $nilaiAkhir = $service->hitungNilaiAkhir($pengampu, $mahasiswa);
                        $skorCpmk = $nilaiAkhir !== null
                            ? round(($nilaiAkhir / 100) * $meta['bobot'], 2)
                            : null;
                    }

                    if ($skorCpmk !== null) {
                        AssessmentScore::updateOrCreate(
                            [
                                'assessment_id' => $assessment->id,
                                'mahasiswa_id' => $mahasiswa->id,
                                'cpmk_id' => $cpmkId,
                            ],
                            ['nilai' => $skorCpmk]
                        );
                        $scoredCount++;
                    }
                }
            }
        }

        $status = $scoredCount > 0 ? Assessment::STATUS_DINILAI : Assessment::STATUS_DRAFT;
        $assessment->update(['status' => $status]);

        return $scoredCount > 0;
    }

    private function pertemuanMilikKelas(Pengampu $pengampu): Closure
    {
        return function ($attribute, $value, $fail) use ($pengampu) {
            if ($value === null) {
                return;
            }

            $rpsId = $pengampu->mataKuliah?->rps?->id;

            if (! $rpsId || ! RpsPertemuan::where('id', $value)->where('rps_id', $rpsId)->exists()) {
                $fail('Pertemuan tidak valid untuk mata kuliah ini.');
            }
        };
    }

    private function notifyTugasPublikasi(Pengampu $pengampu, LmsTugas $tugas): void
    {
        // 1. Mahasiswa di kelas tersebut
        foreach ($pengampu->mahasiswas as $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->notify(new TugasBaru($pengampu, $tugas, 'mahasiswa'));
            }
        }

        // 2. Dosen pengampu kelas tersebut
        if ($pengampu->dosen?->user) {
            $pengampu->dosen->user->notify(new TugasBaru($pengampu, $tugas, 'dosen'));
        }

        // 3. Kaprodi dari Program Studi mata kuliah tersebut
        $prodiId = $pengampu->mataKuliah?->kurikulum?->program_studi_id ?? $pengampu->dosen?->program_studi_id;
        if ($prodiId) {
            $kaprodis = Dosen::where('program_studi_id', $prodiId)
                ->where('jabatan', 'Kaprodi')
                ->with('user')
                ->get();

            foreach ($kaprodis as $kaprodi) {
                if ($kaprodi->user && $kaprodi->user_id !== $pengampu->dosen?->user_id) {
                    $kaprodi->user->notify(new TugasBaru($pengampu, $tugas, 'kaprodi'));
                }
            }
        }

        // 4. Direktur
        $direkturs = User::where('role', 'direktur')->get();
        foreach ($direkturs as $direktur) {
            $direktur->notify(new TugasBaru($pengampu, $tugas, 'direktur'));
        }
    }
}
