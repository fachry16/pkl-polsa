<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\LmsMateri;
use App\Models\Pengampu;
use App\Models\Rps;
use App\Models\RpsPertemuan;
use App\Rules\LmsFileMime;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\HeaderUtils;

class RpsPertemuanController extends Controller
{
    use AuthorizesRps;

    /**
     * Menampilkan daftar pertemuan.
     */
    public function index(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $pertemuans = $rps->pertemuans()
            ->orderBy('minggu')
            ->paginate(16);

        return view(
            'rps-pertemuan.index',
            compact('rps', 'pertemuans')
        );
    }

    /**
     * Form tambah pertemuan.
     */
    public function create(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $cpmks = $rps->mataKuliah->cpmks()
            ->orderBy('kode_cpmk')
            ->get();

        return view(
            'rps-pertemuan.create',
            compact('rps', 'cpmks')
        );
    }

    /**
     * Simpan pertemuan.
     */
    public function store(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu' => [
                'required',
                'integer',
                'between:1,'.Rps::JUMLAH_PERTEMUAN,
                Rule::unique('rps_pertemuans')
                    ->where(function ($query) use ($rps) {
                        return $query->where('rps_id', $rps->id);
                    }),
            ],

            'sub_cpmk' => 'required',
            'materi' => 'required',
            'metode' => 'nullable|max:255',
            'pengalaman_belajar' => 'nullable',
            'indikator' => 'nullable',
            'bobot' => 'nullable|numeric|min:0|max:100',
            'cpmk_induk' => 'nullable|string|max:255',
            'teknik_kriteria' => 'nullable|string',
            'metode_daring' => 'nullable|string',
            'metode_luring' => 'nullable|string',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'rps_id' => $rps->id,
            'minggu' => $request->minggu,
            'sub_cpmk' => $request->sub_cpmk,
            'materi' => $request->materi,
            'metode' => $request->metode ?? '',
            'pengalaman_belajar' => $request->pengalaman_belajar ?? '',
            'indikator' => $request->indikator ?? '',
            'bobot' => $request->bobot,
            'cpmk_induk' => $request->cpmk_induk,
            'teknik_kriteria' => $request->teknik_kriteria,
            'metode_daring' => $request->metode_daring,
            'metode_luring' => $request->metode_luring,
        ];

        if ($request->hasFile('file')) {
            $data = array_merge($data, $this->simpanFileMateri($request->file('file'), $rps, (int) $request->minggu));
        }

        $pertemuan = RpsPertemuan::create($data);

        $this->syncToPengampu($rps, $pertemuan);

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'Pertemuan berhasil ditambahkan.'
            );
    }

    /**
     * Form edit.
     */
    public function edit(Rps $rps, RpsPertemuan $pertemuan)
    {
        $this->authorizeRpsModel($rps);

        $cpmks = $rps->mataKuliah->cpmks()
            ->orderBy('kode_cpmk')
            ->get();

        return view(
            'rps-pertemuan.edit',
            compact('rps', 'pertemuan', 'cpmks')
        );
    }

    /**
     * Update pertemuan.
     */
    public function update(
        Request $request,
        Rps $rps,
        RpsPertemuan $pertemuan
    ) {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu' => [
                'required',
                'integer',
                'between:1,'.Rps::JUMLAH_PERTEMUAN,
                Rule::unique('rps_pertemuans')
                    ->ignore($pertemuan->id)
                    ->where(function ($query) use ($rps) {
                        return $query->where('rps_id', $rps->id);
                    }),
            ],

            'sub_cpmk' => 'required',
            'materi' => 'required',
            'metode' => 'nullable|max:255',
            'pengalaman_belajar' => 'nullable',
            'indikator' => 'nullable',
            'bobot' => 'nullable|numeric|min:0|max:100',
            'cpmk_induk' => 'nullable|string|max:255',
            'teknik_kriteria' => 'nullable|string',
            'metode_daring' => 'nullable|string',
            'metode_luring' => 'nullable|string',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'minggu' => $request->minggu,
            'sub_cpmk' => $request->sub_cpmk,
            'materi' => $request->materi,
            'metode' => $request->metode ?? '',
            'pengalaman_belajar' => $request->pengalaman_belajar ?? '',
            'indikator' => $request->indikator ?? '',
            'bobot' => $request->bobot,
            'cpmk_induk' => $request->cpmk_induk,
            'teknik_kriteria' => $request->teknik_kriteria,
            'metode_daring' => $request->metode_daring,
            'metode_luring' => $request->metode_luring,
        ];

        if ($request->hasFile('file')) {
            if ($pertemuan->file_materi) {
                app(GoogleDriveService::class)->deleteFile($pertemuan->file_materi);
            }
            $data = array_merge($data, $this->simpanFileMateri($request->file('file'), $rps, (int) $request->minggu));
        }

        $pertemuan->update($data);

        $this->syncToPengampu($rps, $pertemuan);

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'Pertemuan berhasil diperbarui.'
            );
    }

    /**
     * Hapus pertemuan.
     */
    public function destroy(
        Rps $rps,
        RpsPertemuan $pertemuan
    ) {
        $this->authorizeRpsModel($rps);

        if ($pertemuan->file_materi) {
            app(GoogleDriveService::class)->deleteFile($pertemuan->file_materi);
        }

        LmsMateri::where('rps_pertemuan_id', $pertemuan->id)->delete();

        $pertemuan->delete();

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'Pertemuan berhasil dihapus.'
            );
    }

    /**
     * Upload materi file ke pertemuan.
     */
    public function uploadMateri(
        Request $request,
        Rps $rps,
        RpsPertemuan $pertemuan
    ) {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
            'link_materi' => 'nullable|string|max:2000',
        ]);

        $data = ['link_materi' => $request->link_materi];
        $adaFile = $request->hasFile('file');

        if ($adaFile) {
            if ($pertemuan->file_materi) {
                app(GoogleDriveService::class)->deleteFile($pertemuan->file_materi);
            }

            $data = array_merge($data, $this->simpanFileMateri($request->file('file'), $rps, $pertemuan->minggu));
        }

        $pertemuan->update($data);

        $this->syncToPengampu($rps, $pertemuan);

        $pesan = $adaFile
            ? 'File materi untuk pertemuan minggu '.$pertemuan->minggu.' berhasil diunggah & tersinkron ke kelas LMS.'
            : 'Catatan pertemuan minggu '.$pertemuan->minggu.' berhasil disimpan.';

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with('success', $pesan);
    }

    /**
     * Stream file materi pertemuan.
     */
    public function file(Rps $rps, RpsPertemuan $pertemuan)
    {
        $this->authorizeRpsModel($rps);

        abort_unless($pertemuan->file_materi, 404);

        $namaFile = $pertemuan->file_materi_nama ?: basename($pertemuan->file_materi);

        if (str_starts_with($pertemuan->file_materi, 'gdrive/')) {
            $parts = explode('/', $pertemuan->file_materi);
            $driveFileId = $parts[1] ?? null;
            $fileName = $pertemuan->file_materi_nama ?? ($parts[2] ?? basename($pertemuan->file_materi));

            abort_unless($driveFileId, 404);

            return app(GoogleDriveService::class)->streamFileResponse($driveFileId, $fileName);
        }

        $disk = Storage::disk('public');
        $path = $disk->path($pertemuan->file_materi);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => $disk->mimeType($pertemuan->file_materi),
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                $namaFile
            ),
        ]);
    }

    /**
     * Simpan file materi ke Google Drive (atau fallback lokal) dengan hirarki RPS terstruktur.
     */
    private function simpanFileMateri(UploadedFile $file, Rps $rps, ?int $minggu = null): array
    {
        $original = $file->getClientOriginalName();
        $driveService = app(GoogleDriveService::class);
        $mingguLabel = $minggu ? "Pertemuan_{$minggu}_" : '';
        $customName = $mingguLabel.$original;

        $hierarchy = $driveService->buildRpsHierarchy($rps, 'Materi_Mingguan');
        $storedPath = $driveService->storeFile($file, 'lms/materi', $hierarchy, $customName);

        return [
            'file_materi' => $storedPath,
            'file_materi_nama' => $original,
        ];
    }

    private function syncToPengampu(Rps $rps, RpsPertemuan $pertemuan): void
    {
        if (! $pertemuan->file_materi) {
            return;
        }

        $judul = $pertemuan->materi
            ?: 'Materi Minggu '.$pertemuan->minggu;

        $pengampus = Pengampu::where('mata_kuliah_id', $rps->mata_kuliah_id)->get();

        foreach ($pengampus as $pengampu) {
            $sudahAda = LmsMateri::where('pengampu_id', $pengampu->id)
                ->where('rps_pertemuan_id', $pertemuan->id)
                ->exists();

            if ($sudahAda) {
                LmsMateri::where('pengampu_id', $pengampu->id)
                    ->where('rps_pertemuan_id', $pertemuan->id)
                    ->update([
                        'judul' => $judul,
                        'file_path' => $pertemuan->file_materi,
                    ]);

                continue;
            }

            LmsMateri::create([
                'pengampu_id' => $pengampu->id,
                'rps_pertemuan_id' => $pertemuan->id,
                'judul' => $judul,
                'deskripsi' => $pertemuan->sub_cpmk,
                'file_path' => $pertemuan->file_materi,
            ]);
        }
    }
}
