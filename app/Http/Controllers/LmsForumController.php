<?php

namespace App\Http\Controllers;

use App\Models\LmsForumDiskusi;
use App\Models\Pengampu;
use App\Notifications\ForumDiskusiBaru;
use App\Rules\LmsFileMime;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmsForumController extends Controller
{
    public function index(Pengampu $pengampu)
    {
        $this->authorizePengampu($pengampu);

        if (Auth::check()) {
            Auth::user()->unreadNotifications()
                ->where('type', ForumDiskusiBaru::class)
                ->where('data->pengampu_id', $pengampu->id)
                ->update(['read_at' => now()]);
        }

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $diskusi = $pengampu->lmsForumDiskusis()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(10);

        return view('lms.forum.index', compact('pengampu', 'diskusi'));
    }

    public function store(Request $request, Pengampu $pengampu)
    {
        $this->authorizePengampu($pengampu);

        $data = $this->validated($request, $pengampu->id);

        if ($request->hasFile('file')) {
            $driveService = app(GoogleDriveService::class);
            $mkLabel = ($pengampu->mataKuliah->kode ?? 'MK').' - '.($pengampu->kelas ?? 'Kelas');
            $data['file_path'] = $driveService->storeFile($request->file('file'), 'lms/forum', [$mkLabel, 'Forum']);
        }

        $forum = LmsForumDiskusi::create($data);

        $sender = Auth::user();
        if ($pengampu->dosen?->user && $pengampu->dosen->user->id !== $sender->id) {
            $pengampu->dosen->user->notify(new ForumDiskusiBaru($pengampu, $forum, $sender->name));
        }
        foreach ($pengampu->mahasiswas as $mhs) {
            if ($mhs->user && $mhs->user->id !== $sender->id) {
                $mhs->user->notify(new ForumDiskusiBaru($pengampu, $forum, $sender->name));
            }
        }

        return back()->with('toast_success', 'Pesan berhasil dikirim.');
    }

    public function edit(Pengampu $pengampu, LmsForumDiskusi $diskusi)
    {
        $this->authorizePost($pengampu, $diskusi);

        $pengampu->load('mataKuliah', 'tahunAkademik');

        return view('lms.forum.edit', compact('pengampu', 'diskusi'));
    }

    public function update(Request $request, Pengampu $pengampu, LmsForumDiskusi $diskusi)
    {
        $this->authorizePost($pengampu, $diskusi);

        $data = $this->validated($request, $pengampu->id);
        $driveService = app(GoogleDriveService::class);
        $mkLabel = ($pengampu->mataKuliah->kode ?? 'MK').' - '.($pengampu->kelas ?? 'Kelas');

        if ($request->hasFile('file')) {
            if ($diskusi->file_path) {
                $driveService->deleteFile($diskusi->file_path);
            }
            $data['file_path'] = $driveService->storeFile($request->file('file'), 'lms/forum', [$mkLabel, 'Forum']);
        } elseif ($request->boolean('remove_file') && $diskusi->file_path) {
            $driveService->deleteFile($diskusi->file_path);
            $data['file_path'] = null;
        }

        $diskusi->update($data);

        return redirect()->route('lms.forum.index', $pengampu->id)->with('toast_success', 'Pesan berhasil diperbarui.');
    }

    public function destroy(Pengampu $pengampu, LmsForumDiskusi $diskusi)
    {
        $this->authorizePengampu($pengampu);
        abort_if($diskusi->pengampu_id !== $pengampu->id, 404);

        if (! Auth::user()->isAdmin()) {
            if ($diskusi->user?->isMahasiswa()) {
                abort_unless($diskusi->isWithinTimeLimit(30), 403, 'Batas waktu 30 menit untuk menghapus pesan mahasiswa telah berakhir.');
            } else {
                abort_unless(Auth::id() === $diskusi->user_id, 403);
                abort_unless($diskusi->isWithinTimeLimit(30), 403, 'Batas waktu 30 menit untuk menghapus pesan telah berakhir.');
            }
        }

        $driveService = app(GoogleDriveService::class);

        foreach ($diskusi->replies as $reply) {
            if ($reply->file_path) {
                $driveService->deleteFile($reply->file_path);
            }
        }
        $diskusi->replies()->delete();

        if ($diskusi->file_path) {
            $driveService->deleteFile($diskusi->file_path);
        }

        $diskusi->delete();

        return redirect()->back(fallback: route('lms.forum.index', $pengampu->id))->with('toast_success', 'Pesan berhasil dihapus.');
    }

    private function validated(Request $request, int $pengampuId): array
    {
        $validated = $request->validate([
            'pesan' => 'required|string',
            'parent_id' => [
                'nullable',
                'exists:lms_forum_diskusis,id',
                function ($attribute, $value, $fail) use ($pengampuId) {
                    if ($value === null) {
                        return;
                    }

                    $parent = LmsForumDiskusi::find($value);

                    if (! $parent || $parent->pengampu_id !== $pengampuId || $parent->parent_id !== null) {
                        $fail('Balasan hanya dapat dibuat pada diskusi utama di kelas ini.');
                    }
                },
            ],
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        return [
            'pengampu_id' => $pengampuId,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'pesan' => $validated['pesan'],
        ];
    }

    private function authorizePengampu(Pengampu $pengampu): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }

        $dosen = $user->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
    }

    private function authorizePost(Pengampu $pengampu, LmsForumDiskusi $diskusi): void
    {
        $this->authorizePengampu($pengampu);

        abort_if($diskusi->pengampu_id !== $pengampu->id, 404);

        if (Auth::user()->isAdmin()) {
            return;
        }

        abort_unless(Auth::id() === $diskusi->user_id, 403);

        abort_unless($diskusi->isWithinTimeLimit(30), 403, 'Batas waktu 30 menit untuk mengubah pesan telah berakhir.');
    }
}
