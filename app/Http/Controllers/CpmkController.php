<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Cpmk;
use App\Models\Kurikulum;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class CpmkController extends Controller
{
    use AuthorizesKurikulum;

    public function downloadTemplate(CsvImportService $csvService)
    {
        $headers = ['kode_cpmk', 'deskripsi'];
        $samples = [
            ['CPMK01', 'Mahasiswa mampu menganalisis kebutuhan perangkat lunak secara sistematis.'],
            ['CPMK02', 'Mahasiswa mampu merancang arsitektur perangkat lunak berbasis pola desain.'],
        ];

        return $csvService->downloadTemplate('template_import_cpmk.csv', $headers, $samples);
    }

    public function import(Request $request, Kurikulum $kurikulum, CsvImportService $csvService)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $rows = $csvService->parseCsv($request->file('file')->getRealPath());

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $imported = 0;
        $skipped = [];
        $existing = $kurikulum->cpmks()->pluck('kode_cpmk')->map(fn ($k) => strtoupper(trim($k)))->all();

        foreach ($rows as $row) {
            $rowNum = $row['_row_number'] ?? '?';
            $kode = strtoupper(trim($row['kode_cpmk'] ?? ''));
            $deskripsi = trim($row['deskripsi'] ?? '');

            if ($kode === '' || $deskripsi === '') {
                $skipped[] = "Baris {$rowNum}: kode_cpmk dan deskripsi wajib diisi.";

                continue;
            }

            if (in_array($kode, $existing)) {
                $skipped[] = "Baris {$rowNum}: Kode CPMK {$kode} sudah terdaftar pada kurikulum ini.";

                continue;
            }

            Cpmk::create([
                'kurikulum_id' => $kurikulum->id,
                'kode_cpmk' => $kode,
                'deskripsi' => $deskripsi,
            ]);
            $existing[] = $kode;
            $imported++;
        }

        $msg = "Berhasil mengimpor {$imported} data CPMK.";
        if (! empty($skipped)) {
            return back()->with('success', $msg.' '.count($skipped).' baris dilewati.')->with('import_warnings', $skipped);
        }

        return back()->with('success', $msg);
    }

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);
        $cpmks = $kurikulum->cpmks()->latest()->paginate(10);

        return view('cpmk.index', compact('kurikulum', 'cpmks'));
    }

    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('cpmk.create', compact('kurikulum'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_cpmk' => 'required',
            'deskripsi' => 'required',
        ]);

        Cpmk::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_cpmk' => strtoupper($request->kode_cpmk),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()
            ->route('kurikulum.cpmk.index', $kurikulum->id)
            ->with('success', 'CPMK berhasil ditambahkan.');
    }

    public function show(Kurikulum $kurikulum, Cpmk $cpmk)
    {
        //
    }

    public function edit(Kurikulum $kurikulum, Cpmk $cpmk)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('cpmk.edit', compact('kurikulum', 'cpmk'));
    }

    public function update(Request $request, Kurikulum $kurikulum, Cpmk $cpmk)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_cpmk' => 'required',
            'deskripsi' => 'required',
        ]);

        $cpmk->update([
            'kode_cpmk' => strtoupper($request->kode_cpmk),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()
            ->route('kurikulum.cpmk.index', $kurikulum->id)
            ->with('success', 'CPMK berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, Cpmk $cpmk)
    {
        $this->authorizeKurikulum($kurikulum);
        $cpmk->delete();

        return redirect()
            ->route('kurikulum.cpmk.index', $kurikulum->id)
            ->with('success', 'CPMK berhasil dihapus.');
    }
}
