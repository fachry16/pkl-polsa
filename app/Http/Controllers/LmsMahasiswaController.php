<?php

namespace App\Http\Controllers;

use App\Models\LmsForumDiskusi;
use App\Models\LmsMateri;
use App\Models\LmsMateriMahasiswa;
use App\Models\LmsNilaiMahasiswa;
use App\Models\LmsSesiAbsensi;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Pengampu;
use App\Models\TahunAkademik;
use App\Notifications\SubmissionBaru;
use App\Rules\LmsFileMime;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsMahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $tahunAkademik = TahunAkademik::where('is_active', true)->first();

        abort_if(! $mahasiswa, 403);

        $pengampus = collect();

        if ($tahunAkademik) {
            $pengampus = $mahasiswa->pengampus()
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->with(['mataKuliah', 'dosen.user'])
                ->withCount(['lmsMateris', 'lmsTugas'])
                ->get();
        }

        return view('lms.mahasiswa.index', compact('pengampus', 'tahunAkademik'));
    }

    public function show(Pengampu $pengampu)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        Auth::user()->unreadNotifications()
            ->where('data->pengampu_id', $pengampu->id)
            ->update(['read_at' => now()]);

        $pengampu->load([
            'mataKuliah',
            'dosen.user',
            'tahunAkademik',
            'mahasiswas.user',
            'lmsMateris.rpsPertemuan',
            'lmsTugas.rpsPertemuan',
            'lmsMateris' => function ($q) {
                $q->latest();
            },
            'lmsTugas' => function ($q) {
                $q->withCount('submissions')->latest();
            },
            'lmsForumDiskusis' => function ($q) {
                $q->whereNull('parent_id')->with('user', 'replies.user')->latest();
            },
        ]);

        $pengumumans = $pengampu->lmsPengumumans()->latest()->get();

        $submissions = LmsSubmission::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('lms_tugas_id', $pengampu->lmsTugas->pluck('id'))
            ->get()
            ->keyBy('lms_tugas_id');

        $materiSelesai = LmsMateriMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('materi_id', $pengampu->lmsMateris->pluck('id'))
            ->whereNotNull('dibaca_pada')
            ->pluck('materi_id')
            ->flip();

        $materiCount = $pengampu->lmsMateris->count();
        $tugasCount = $pengampu->lmsTugas->count();

        $nilaiByKomponen = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->get()
            ->keyBy('komponen');

        $bobot = app(PenilaianService::class)->bobotKomponen($pengampu);

        $absensiSesi = LmsSesiAbsensi::where('pengampu_id', $pengampu->id)
            ->with(['rpsPertemuan', 'absensis' => function ($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id);
            }])
            ->orderBy('tanggal_aktual')
            ->get();

        $absensiSesi->each(function ($sesi) {
            $sesi->status_mahasiswa = $sesi->absensis->first()?->status ?? null;
        });

        $hadirCount = $absensiSesi->filter(fn ($sesi) => $sesi->status_mahasiswa === 'hadir')->count();
        $totalSesi = $absensiSesi->count();

        return view('lms.mahasiswa.show', compact(
            'pengampu', 'materiCount', 'tugasCount', 'submissions', 'materiSelesai', 'pengumumans',
            'nilaiByKomponen', 'bobot', 'absensiSesi', 'hadirCount', 'totalSesi'
        ));
    }

    public function showTugas(Pengampu $pengampu, LmsTugas $tugas)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        $pengampu->load('mataKuliah', 'tahunAkademik', 'dosen.user');
        $submission = LmsSubmission::where('lms_tugas_id', $tugas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        $komentarsKelas = \App\Models\LmsTopikKomentar::where('tipe_topik', 'tugas')
            ->where('topik_id', $tugas->id)
            ->where('is_private', false)
            ->with('user')
            ->oldest()
            ->get();

        $komentarsPribadi = \App\Models\LmsTopikKomentar::where('tipe_topik', 'tugas')
            ->where('topik_id', $tugas->id)
            ->where('is_private', true)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with('user')
            ->oldest()
            ->get();

        return view('lms.mahasiswa.tugas.show', compact('pengampu', 'tugas', 'submission', 'komentarsKelas', 'komentarsPribadi'));
    }

    public function showMateri(Pengampu $pengampu, LmsMateri $materi)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);
        abort_if($materi->pengampu_id !== $pengampu->id, 404);

        $pengampu->load('mataKuliah', 'tahunAkademik', 'dosen.user');
        $isSelesai = $materi->dibacaOleh($mahasiswa);

        $komentarsKelas = \App\Models\LmsTopikKomentar::where('tipe_topik', 'materi')
            ->where('topik_id', $materi->id)
            ->where('is_private', false)
            ->with('user')
            ->oldest()
            ->get();

        return view('lms.mahasiswa.materi.show', compact('pengampu', 'materi', 'isSelesai', 'komentarsKelas'));
    }

    public function storeForum(Request $request, Pengampu $pengampu)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        $validated = $request->validate([
            'pesan' => 'required|string',
            'parent_id' => [
                'nullable',
                'exists:lms_forum_diskusis,id',
                function ($attribute, $value, $fail) use ($pengampu) {
                    if ($value === null) {
                        return;
                    }

                    $parent = LmsForumDiskusi::find($value);

                    if (! $parent || $parent->pengampu_id !== $pengampu->id || $parent->parent_id !== null) {
                        $fail('Balasan hanya dapat dibuat pada diskusi utama di kelas ini.');
                    }
                },
            ],
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'pengampu_id' => $pengampu->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'pesan' => $validated['pesan'],
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('lms/forum', 'public');
        }

        LmsForumDiskusi::create($data);

        return back()->with('toast_success', 'Pesan berhasil dikirim.');
    }

    public function destroyForum(Pengampu $pengampu, LmsForumDiskusi $diskusi)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);
        abort_if($diskusi->pengampu_id !== $pengampu->id, 404);
        abort_unless(Auth::id() === $diskusi->user_id, 403);
        abort_unless($diskusi->isWithinTimeLimit(30), 403, 'Batas waktu 30 menit untuk menghapus pesan telah berakhir.');

        foreach ($diskusi->replies as $reply) {
            if ($reply->file_path) {
                Storage::disk('public')->delete($reply->file_path);
            }
        }

        $diskusi->replies()->delete();

        if ($diskusi->file_path) {
            Storage::disk('public')->delete($diskusi->file_path);
        }

        $diskusi->delete();

        return back()->with('toast_success', 'Pesan berhasil dihapus.');
    }

    public function updateForum(Request $request, Pengampu $pengampu, LmsForumDiskusi $diskusi)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);
        abort_if($diskusi->pengampu_id !== $pengampu->id, 404);
        abort_unless(Auth::id() === $diskusi->user_id, 403);
        abort_unless($diskusi->isWithinTimeLimit(30), 403, 'Batas waktu 30 menit untuk mengubah pesan telah berakhir.');

        $validated = $request->validate([
            'pesan' => 'required|string',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = ['pesan' => $validated['pesan']];

        if ($request->hasFile('file')) {
            if ($diskusi->file_path) {
                Storage::disk('public')->delete($diskusi->file_path);
            }

            $data['file_path'] = $request->file('file')->store('lms/forum', 'public');
        } elseif ($request->boolean('remove_file') && $diskusi->file_path) {
            Storage::disk('public')->delete($diskusi->file_path);
            $data['file_path'] = null;
        }

        $diskusi->update($data);

        return back()->with('toast_success', 'Pesan berhasil diperbarui.');
    }

    public function storeSubmission(Request $request, LmsTugas $tugas)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);

        $pengampu = $tugas->pengampu;

        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        $existing = LmsSubmission::where('lms_tugas_id', $tugas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        abort_if($existing?->nilai !== null, 403, 'Tidak dapat mengumpulkan: tugas sudah dinilai.');
        abort_if($tugas->deadline->isPast(), 403, 'Tidak dapat mengumpulkan: melewati deadline.');

        $maxKb = ($tugas->batas_upload_mb ?: 50) * 1024;

        $request->validate([
            'file_jawaban' => ['nullable', 'file', "max:{$maxKb}", new LmsFileMime],
            'link_jawaban' => ['nullable', 'string', 'max:500'],
            'catatan_mahasiswa' => ['nullable', 'string'],
        ]);

        $data = [
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $mahasiswa->id,
            'link_jawaban' => $request->link_jawaban,
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
            'dikumpulkan_pada' => now(),
        ];

        if ($request->hasFile('file_jawaban')) {
            if ($existing?->file_jawaban) {
                Storage::disk('public')->delete($existing->file_jawaban);
            }

            $data['file_jawaban'] = $request->file('file_jawaban')->store('lms/submissions', 'public');
        }

        LmsSubmission::updateOrCreate(
            ['lms_tugas_id' => $tugas->id, 'mahasiswa_id' => $mahasiswa->id],
            $data
        );

        if ($pengampu->dosen?->user) {
            $pengampu->dosen->user->notify(new SubmissionBaru($pengampu, $tugas, $mahasiswa));
        }

        return back()->with('toast_success', 'Tugas berhasil dikumpulkan / ditandai selesai.');
    }

    public function updateSubmission(Request $request, LmsSubmission $submission)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_unless($submission->mahasiswa_id === $mahasiswa->id, 403);

        $tugas = $submission->lmsTugas;
        $pengampu = $tugas->pengampu;

        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);
        abort_if($tugas->deadline->isPast(), 403, 'Tidak dapat memperbarui: melewati deadline.');
        abort_if($submission->nilai !== null, 403, 'Tidak dapat memperbarui: tugas sudah dinilai.');

        $maxKb = ($tugas->batas_upload_mb ?: 50) * 1024;

        $request->validate([
            'file_jawaban' => ['nullable', 'file', "max:{$maxKb}", new LmsFileMime],
            'link_jawaban' => ['nullable', 'string', 'max:500'],
            'catatan_mahasiswa' => ['nullable', 'string'],
        ]);

        $data = [
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
            'dikumpulkan_pada' => now(),
        ];

        if ($request->has('link_jawaban')) {
            $data['link_jawaban'] = $request->link_jawaban;
        }

        if ($request->hasFile('file_jawaban')) {
            if ($submission->file_jawaban) {
                Storage::disk('public')->delete($submission->file_jawaban);
            }

            $data['file_jawaban'] = $request->file('file_jawaban')->store('lms/submissions', 'public');
        } elseif ($request->boolean('hapus_file_jawaban') && $submission->file_jawaban) {
            Storage::disk('public')->delete($submission->file_jawaban);
            $data['file_jawaban'] = null;
        }

        $submission->update($data);

        return back()->with('toast_success', 'Kiriman tugas berhasil diperbarui.');
    }

    public function toggleMateriSelesai(LmsMateri $materi)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);

        $pengampu = $materi->pengampu;

        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        $progress = LmsMateriMahasiswa::firstOrNew([
            'materi_id' => $materi->id,
            'mahasiswa_id' => $mahasiswa->id,
        ]);

        if ($progress->exists) {
            $progress->delete();

            return back()->with('toast_success', 'Materi ditandai belum selesai.');
        }

        $progress->dibaca_pada = now();
        $progress->save();

        return back()->with('toast_success', 'Materi ditandai selesai dibaca.');
    }
}
