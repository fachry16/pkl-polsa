<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\KhsApproval;
use App\Models\Krs;
use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Notifications\KhsDisetujuiNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KhsController extends Controller
{
    /**
     * Halaman Dashboard Verifikasi & Persetujuan KHS untuk Kaprodi & Admin
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $programStudis = $kaprodiProdiId
            ? ProgramStudi::where('id', $kaprodiProdiId)->orderBy('nama_prodi')->get()
            : ProgramStudi::orderBy('nama_prodi')->get();

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        $selectedProdiId = $kaprodiProdiId ?: ($request->program_studi_id ?: $programStudis->first()?->id);
        $selectedTahunId = $request->tahun_akademik_id ?: ($tahunAkademiks->firstWhere('is_active', true)?->id ?? $tahunAkademiks->first()?->id);

        $mahasiswas = [];
        $tahunAkademik = TahunAkademik::find($selectedTahunId);

        if ($selectedProdiId && $selectedTahunId) {
            $mahasiswaList = Mahasiswa::where('program_studi_id', $selectedProdiId)
                ->with(['user', 'programStudi'])
                ->orderBy('nim')
                ->get();

            $approvals = KhsApproval::where('tahun_akademik_id', $selectedTahunId)
                ->whereIn('mahasiswa_id', $mahasiswaList->pluck('id'))
                ->with('approver')
                ->get()
                ->keyBy('mahasiswa_id');

            foreach ($mahasiswaList as $mhs) {
                $kelas = $this->kelasMahasiswa($mhs, $selectedTahunId);
                $khsData = $this->formatKhsItems($kelas, $mhs);
                $approval = $approvals->get($mhs->id);

                $mahasiswas[] = [
                    'mahasiswa' => $mhs,
                    'kelas_count' => $kelas->count(),
                    'total_sks' => $khsData['total_sks'],
                    'ips' => $khsData['ips'],
                    'approval' => $approval,
                    'is_disetujui' => $approval?->isDisetujui() ?? false,
                ];
            }
        }

        return view('khs.index', compact(
            'programStudis',
            'tahunAkademiks',
            'selectedProdiId',
            'selectedTahunId',
            'tahunAkademik',
            'mahasiswas'
        ));
    }

    public function cetakPilih()
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $programStudis = $kaprodiProdiId
            ? ProgramStudi::where('id', $kaprodiProdiId)->orderBy('nama_prodi')->get()
            : ProgramStudi::orderBy('nama_prodi')->get();

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('khs.cetak-pilih', compact('programStudis', 'tahunAkademiks'));
    }

    public function pilihMahasiswa(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
        ]);

        return redirect()->route('khs.cetak', [
            $request->mahasiswa_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
        ]);
    }

    public function self(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        return $this->cetak($mahasiswa);
    }

    public function cetak(Mahasiswa $mahasiswa)
    {
        $this->authorizeCetak($mahasiswa);

        $tahunAkademikId = request('tahun_akademik_id');

        $kelas = $this->kelasMahasiswa($mahasiswa, $tahunAkademikId);

        $tahunAkademik = $tahunAkademikId
            ? TahunAkademik::find($tahunAkademikId)
            : ($kelas->first()?->tahunAkademik ?? TahunAkademik::latest()->first());

        $khsData = $this->formatKhsItems($kelas, $mahasiswa);

        $approval = null;
        if ($tahunAkademik) {
            $approval = KhsApproval::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->with('approver')
                ->first();
        }

        return view('khs.cetak', compact('mahasiswa', 'kelas', 'tahunAkademik', 'khsData', 'approval'));
    }

    public function cetakPdf(Mahasiswa $mahasiswa)
    {
        $this->authorizeCetak($mahasiswa);

        $tahunAkademikId = request('tahun_akademik_id');

        $kelas = $this->kelasMahasiswa($mahasiswa, $tahunAkademikId);

        $tahunAkademik = $tahunAkademikId
            ? TahunAkademik::find($tahunAkademikId)
            : ($kelas->first()?->tahunAkademik ?? TahunAkademik::latest()->first());

        $approval = null;
        if ($tahunAkademik) {
            $approval = KhsApproval::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->with('approver')
                ->first();
        }

        // Mahasiswa hanya boleh unduh PDF jika sudah disetujui Kaprodi
        if (auth()->user()->isMahasiswa() && (! $approval || ! $approval->isDisetujui())) {
            return back()->with('error', 'KHS semester ini belum disetujui oleh Kaprodi sehingga belum dapat diunduh.');
        }

        $khsData = $this->formatKhsItems($kelas, $mahasiswa);

        $pdf = Pdf::loadView('khs.pdf', compact('mahasiswa', 'kelas', 'tahunAkademik', 'khsData', 'approval'));

        $filename = 'KHS-'.$mahasiswa->nim.'-'.$mahasiswa->nama.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Kaprodi / Admin menyetujui KHS 1 mahasiswa
     */
    public function approve(Request $request, Mahasiswa $mahasiswa, TahunAkademik $tahunAkademik)
    {
        $this->authorizeApproval($mahasiswa);

        $approval = KhsApproval::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAkademik->id,
            ],
            [
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'catatan' => $request->catatan,
            ]
        );

        if ($mahasiswa->user) {
            $approverName = auth()->user()->name;
            $mahasiswa->user->notify(new KhsDisetujuiNotification($mahasiswa, $tahunAkademik, $approverName));
        }

        return back()->with('success', "KHS mahasiswa {$mahasiswa->nama} ({$mahasiswa->nim}) berhasil disetujui dan dipublikasikan.");
    }

    /**
     * Kaprodi / Admin membatalkan persetujuan KHS mahasiswa
     */
    public function unapprove(Request $request, Mahasiswa $mahasiswa, TahunAkademik $tahunAkademik)
    {
        $this->authorizeApproval($mahasiswa);

        KhsApproval::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAkademik->id,
            ],
            [
                'status' => 'menunggu',
                'approved_by' => null,
                'approved_at' => null,
                'catatan' => $request->catatan,
            ]
        );

        return back()->with('success', "Persetujuan KHS mahasiswa {$mahasiswa->nama} berhasil dibatalkan.");
    }

    /**
     * Kaprodi / Admin menyetujui KHS seluruh mahasiswa prodi pada tahun akademik yang dipilih (Bulk Approval)
     */
    public function approveAll(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        ]);

        $user = auth()->user();
        if ($user->isKaprodi()) {
            abort_unless((int) $user->dosen->program_studi_id === (int) $request->program_studi_id, 403);
        } elseif (! $user->isAdmin()) {
            abort(403);
        }

        $tahunAkademik = TahunAkademik::findOrFail($request->tahun_akademik_id);
        $mahasiswas = Mahasiswa::where('program_studi_id', $request->program_studi_id)->get();
        $approverName = auth()->user()->name;

        foreach ($mahasiswas as $mhs) {
            KhsApproval::updateOrCreate(
                [
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                ],
                [
                    'status' => 'disetujui',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]
            );

            if ($mhs->user) {
                $mhs->user->notify(new KhsDisetujuiNotification($mhs, $tahunAkademik, $approverName));
            }
        }

        return back()->with('success', 'Seluruh KHS mahasiswa prodi berhasil disetujui dan dipublikasikan.');
    }

    private function formatKhsItems($kelas, Mahasiswa $mahasiswa)
    {
        $pengampuIds = $kelas->pluck('id');
        $nilaiAkhirMap = LmsNilaiMahasiswa::whereIn('pengampu_id', $pengampuIds)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('komponen', 'akhir')
            ->pluck('nilai', 'pengampu_id');

        $items = [];
        $totalSks = 0;
        $totalPoin = 0;

        foreach ($kelas as $pengampu) {
            $sks = (int) $pengampu->total_sks;
            $nilaiAngka = $nilaiAkhirMap->get($pengampu->id);
            $nilaiHuruf = $nilaiAngka !== null ? konversiNilaiHuruf((float) $nilaiAngka) : '-';
            $bobotMutu = $nilaiAngka !== null ? konversiBobotMutu((float) $nilaiAngka) : null;
            $nilaiMutu = ($bobotMutu !== null) ? ($sks * $bobotMutu) : 0;
            $predikat = $nilaiAngka !== null ? predikatNilai((float) $nilaiAngka) : 'Belum Selesai';

            $totalSks += $sks;
            if ($bobotMutu !== null) {
                $totalPoin += $nilaiMutu;
            }

            $items[] = [
                'pengampu' => $pengampu,
                'kode' => $pengampu->kode_mata_kuliah,
                'nama' => $pengampu->nama_mata_kuliah,
                'sks' => $sks,
                'sks_teori' => $pengampu->mataKuliah?->sks_teori ?? 0,
                'sks_praktik' => $pengampu->mataKuliah?->sks_praktikum ?? 0,
                'dosen' => $pengampu->nama_dosen,
                'nilai_angka' => $nilaiAngka !== null ? number_format($nilaiAngka, 2) : '-',
                'nilai_huruf' => $nilaiHuruf,
                'bobot_mutu' => $bobotMutu !== null ? number_format($bobotMutu, 2) : '-',
                'nilai_mutu' => $bobotMutu !== null ? number_format($nilaiMutu, 2) : '-',
                'predikat' => $predikat,
                'lulus' => in_array($nilaiHuruf, ['A', 'B+', 'B', 'C+', 'C']),
            ];
        }

        $ips = $totalSks > 0 ? round($totalPoin / $totalSks, 2) : 0.00;

        return [
            'items' => $items,
            'total_sks' => $totalSks,
            'total_poin' => round($totalPoin, 2),
            'ips' => number_format($ips, 2),
        ];
    }

    private function kelasMahasiswa(Mahasiswa $mahasiswa, $tahunAkademikId = null)
    {
        return $mahasiswa->pengampus()
            ->with([
                'mataKuliah',
                'dosen.user',
                'tahunAkademik',
                'krs',
            ])
            ->when($tahunAkademikId, function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->orderBy('mata_kuliah_id')
            ->get();
    }

    private function authorizeCetak(Mahasiswa $mahasiswa)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isDirektur()) {
            return;
        }

        if ($user->isMahasiswa() && $user->mahasiswa?->id === $mahasiswa->id) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $mahasiswa->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }

    private function authorizeApproval(Mahasiswa $mahasiswa)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $mahasiswa->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }
}
