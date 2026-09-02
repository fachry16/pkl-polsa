<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Krs;
use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KhsController extends Controller
{
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

        return view('khs.cetak', compact('mahasiswa', 'kelas', 'tahunAkademik', 'khsData'));
    }

    public function cetakPdf(Mahasiswa $mahasiswa)
    {
        $this->authorizeCetak($mahasiswa);

        $tahunAkademikId = request('tahun_akademik_id');

        $kelas = $this->kelasMahasiswa($mahasiswa, $tahunAkademikId);

        $tahunAkademik = $tahunAkademikId
            ? TahunAkademik::find($tahunAkademikId)
            : ($kelas->first()?->tahunAkademik ?? TahunAkademik::latest()->first());

        $khsData = $this->formatKhsItems($kelas, $mahasiswa);

        $pdf = Pdf::loadView('khs.pdf', compact('mahasiswa', 'kelas', 'tahunAkademik', 'khsData'));

        $filename = 'KHS-'.$mahasiswa->nim.'-'.$mahasiswa->nama.'.pdf';

        return $pdf->download($filename);
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
}
