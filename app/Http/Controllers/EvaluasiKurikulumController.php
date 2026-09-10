<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\EvaluasiKurikulum;
use App\Models\Kurikulum;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvaluasiKurikulumController extends Controller
{
    use AuthorizesKurikulum;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $evaluasis = $kurikulum->evaluasiKurikulums()
            ->with(['tahunAkademik', 'creator'])
            ->orderByDesc('created_at')
            ->get();

        return view('evaluasi-kurikulum.index', compact('kurikulum', 'evaluasis'));
    }

    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('evaluasi-kurikulum.create', compact('kurikulum', 'tahunAkademiks'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $data = $this->validated($request);

        EvaluasiKurikulum::create($data + [
            'kurikulum_id' => $kurikulum->id,
            'created_by' => auth()->id(),
        ]);

        return $this->redirectAfterSave($kurikulum, $request, 'Evaluasi kurikulum berhasil ditambahkan.');
    }

    public function edit(Kurikulum $kurikulum, EvaluasiKurikulum $evaluasiKurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('evaluasi-kurikulum.edit', compact('kurikulum', 'evaluasiKurikulum', 'tahunAkademiks'));
    }

    public function update(Request $request, Kurikulum $kurikulum, EvaluasiKurikulum $evaluasiKurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $data = $this->validated($request);

        $evaluasiKurikulum->update($data);

        return $this->redirectAfterSave($kurikulum, $request, 'Evaluasi kurikulum berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, EvaluasiKurikulum $evaluasiKurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $evaluasiKurikulum->delete();

        return redirect()
            ->route('kurikulum.evaluasi-kurikulum.index', $kurikulum->id)
            ->with('success', 'Evaluasi kurikulum berhasil dihapus.');
    }

    protected function redirectAfterSave(Kurikulum $kurikulum, Request $request, string $message): RedirectResponse
    {
        if ($request->query('redirect_to') === 'assessment') {
            return redirect()
                ->route('assessment.evaluasi', ['kurikulum_id' => $kurikulum->id])
                ->with('success', $message);
        }

        return redirect()
            ->route('kurikulum.evaluasi-kurikulum.index', $kurikulum->id)
            ->with('success', $message);
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'tahun_akademik_id' => 'nullable|exists:tahun_akademiks,id',
            'catatan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ]);
    }
}
