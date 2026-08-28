<?php

namespace App\Http\Controllers;

use App\Models\LmsForumDiskusi;
use App\Models\LmsMateri;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Pengampu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;

class LmsFileController extends Controller
{
    public function show(string $model, int $id)
    {
        $file = $this->resolveFile($model, $id);

        abort_if(! $file, 404);
        abort_if(! $file['path'], 404);

        $this->authorizeFile($model, $file);

        $disk = Storage::disk('public');

        abort_if(! is_file($disk->path($file['path'])), 404);

        return response()->file($disk->path($file['path']), [
            'Content-Type' => $disk->mimeType($file['path']),
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                basename($file['path'])
            ),
        ]);
    }

    private function resolveFile(string $model, int $id): ?array
    {
        return match ($model) {
            'materi' => $this->fromModel(LmsMateri::find($id), 'file_path'),
            'tugas' => $this->fromModel(LmsTugas::find($id), 'file_lampiran'),
            'forum' => $this->fromModel(LmsForumDiskusi::find($id), 'file_path'),
            'submission' => $this->fromModel(
                LmsSubmission::with('lmsTugas')->find($id),
                'file_jawaban',
                'mahasiswa_id'
            ),
            default => null,
        };
    }

    private function fromModel(?object $model, string $pathColumn, ?string $userIdColumn = null): ?array
    {
        if (! $model) {
            return null;
        }

        return [
            'pengampu_id' => $model->pengampu_id ?? $model->lmsTugas?->pengampu_id,
            'path' => $model->{$pathColumn} ?? null,
            'user_id' => $userIdColumn ? $model->{$userIdColumn} : null,
        ];
    }

    private function authorizeFile(string $model, array $file): void
    {
        $user = Auth::user();

        abort_if(! $user, 403);

        $dosen = $user->dosen;

        if ($dosen && $file['pengampu_id']) {
            $pengampu = Pengampu::find($file['pengampu_id']);

            abort_if(! $pengampu || $pengampu->dosen_id !== $dosen->id, 403);

            return;
        }

        if ($model === 'submission' && $user->mahasiswa) {
            abort_unless($user->mahasiswa->id === $file['user_id'], 403);

            return;
        }

        if ($user->mahasiswa && $file['pengampu_id']) {
            $pengampu = Pengampu::find($file['pengampu_id']);

            abort_if(! $pengampu || ! $pengampu->mahasiswas()->where('mahasiswa_id', $user->mahasiswa->id)->exists(), 403);

            return;
        }

        abort(403);
    }
}
