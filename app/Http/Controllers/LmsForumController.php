<?php

namespace App\Http\Controllers;

use App\Models\LmsForumDiskusi;
use App\Models\Pengampu;
use App\Rules\LmsFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsForumController extends Controller
{
    public function index(Pengampu $pengampu)
    {
        $this->authorizePengampu($pengampu);

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
            $data['file_path'] = $request->file('file')->store('lms/forum', 'public');
        }

        LmsForumDiskusi::create($data);

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

        return redirect()->route('lms.forum.index', $pengampu->id)->with('toast_success', 'Pesan berhasil diperbarui.');
    }

    public function destroy(Pengampu $pengampu, LmsForumDiskusi $diskusi)
    {
        $this->authorizePengampu($pengampu);
        abort_if($diskusi->pengampu_id !== $pengampu->id, 404);

        abort_if($diskusi->user?->isMahasiswa(), 403, 'Dosen tidak dapat menghapus pesan forum yang dikirim oleh mahasiswa.');

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
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
    }

    private function authorizePost(Pengampu $pengampu, LmsForumDiskusi $diskusi): void
    {
        $this->authorizePengampu($pengampu);

        abort_if($diskusi->pengampu_id !== $pengampu->id, 404);

        abort_unless(Auth::id() === $diskusi->user_id, 403);

        abort_unless($diskusi->isWithinTimeLimit(15), 403, 'Batas waktu 15 menit untuk mengubah pesan telah berakhir.');
    }
}

