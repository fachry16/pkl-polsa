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

        $pengampu->load([
            'mataKuliah',
            'dosen.user',
            'tahunAkademik',
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

        if ($diskusi->created_at->diffInMinutes(now()) > 30) {
            return back()->with('toast_error', 'Batas waktu hapus pesan sudah lewat (30 menit).');
        }

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

        if ($diskusi->created_at->diffInMinutes(now()) > 30) {
            return back()->with('toast_error', 'Batas waktu edit pesan sudah lewat (30 menit).');
        }

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
            'file_jawaban' => ['nullable', 'file', "max:{$maxKb}", new LmsFileMime, 'required_without:catatan_mahasiswa'],
            'catatan_mahasiswa' => ['nullable', 'string', 'required_without:file_jawaban'],
        ], [
            'file_jawaban.max' => 'Ukuran file maksimal '.($tugas->batas_upload_mb ?: 50).' MB.',
            'file_jawaban.required_without' => 'Kumpulkan tugas dengan mengunggah file atau menulis catatan.',
            'catatan_mahasiswa.required_without' => 'Kumpulkan tugas dengan mengunggah file atau menulis catatan.',
        ]);

        $data = [
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $mahasiswa->id,
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

        return back()->with('toast_success', 'Tugas berhasil dikumpulkan.');
    }

    public function updateSubmission(Request $request, LmsSubmission $submission)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_unless($submission->mahasiswa_id === $mahasiswa->id, 403);

        $tugas = $submission->lmsTugas;
        $pengampu = $tugas->pengampu;

        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        // Batas waktu edit 30 menit sejak pengumpulan pertama
        if ($submission->dikumpulkan_pada && $submission->dikumpulkan_pada->diffInMinutes(now()) > 30) {
            return back()->with('toast_error', 'Batas waktu edit sudah lewat (30 menit). Hubungi dosen jika perlu perubahan.');
        }

        abort_if($tugas->deadline->isPast(), 403, 'Tidak dapat memperbarui: melewati deadline.');
        abort_if($submission->nilai !== null, 403, 'Tidak dapat memperbarui: tugas sudah dinilai.');

        $maxKb = ($tugas->batas_upload_mb ?: 50) * 1024;

        $request->validate([
            'file_jawaban' => ['nullable', 'file', "max:{$maxKb}", new LmsFileMime],
            'catatan_mahasiswa' => ['nullable', 'string'],
        ]);

        $data = [
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
            'dikumpulkan_pada' => now(),
        ];

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
