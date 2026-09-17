<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Cpl;
use App\Models\Kurikulum;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class CplController extends Controller
{
    use AuthorizesKurikulum;

    public function downloadTemplate(CsvImportService $csvService)
    {
        $headers = ['kode_cpl', 'deskripsi'];
        $samples = [
            ['CPL01', 'Mampu menerapkan ilmu komputer dan matematika untuk menyelesaikan masalah rekayasa perangkat lunak.'],
            ['CPL02', 'Mampu menganalisis, merancang, dan membangun perangkat lunak yang andal dan efisien.'],
        ];

        return $csvService->downloadTemplate('template_import_cpl.csv', $headers, $samples);
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
        $existing = $kurikulum->cpls()->pluck('kode_cpl')->map(fn ($k) => strtoupper(trim($k)))->all();

        foreach ($rows as $row) {
            $rowNum = $row['_row_number'] ?? '?';
            $kode = strtoupper(trim($row['kode_cpl'] ?? ''));
            $deskripsi = trim($row['deskripsi'] ?? '');

            if ($kode === '' || $deskripsi === '') {
                $skipped[] = "Baris {$rowNum}: kode_cpl dan deskripsi wajib diisi.";

                continue;
            }

            if (in_array($kode, $existing)) {
                $skipped[] = "Baris {$rowNum}: Kode CPL {$kode} sudah terdaftar pada kurikulum ini.";

                continue;
            }

            Cpl::create([
                'kurikulum_id' => $kurikulum->id,
                'kode_cpl' => $kode,
                'deskripsi' => $deskripsi,
            ]);
            $existing[] = $kode;
            $imported++;
        }

        $msg = "Berhasil mengimpor {$imported} data CPL.";
        if (! empty($skipped)) {
            return back()->with('success', $msg.' '.count($skipped).' baris dilewati.')->with('import_warnings', $skipped);
        }

        return back()->with('success', $msg);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);
        $cpls = $kurikulum->cpls()->latest()->paginate(10);

        return view('cpl.index', compact('kurikulum', 'cpls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('cpl.create', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_cpl' => 'required',
            'deskripsi' => 'required',
        ]);
        Cpl::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_cpl' => strtoupper($request->kode_cpl),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('kurikulum.cpl.index', $kurikulum->id)->with('success', 'CPL berhasil ditambahkan,');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kurikulum $kurikulum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum, Cpl $cpl)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('cpl.edit', compact('kurikulum', 'cpl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum, Cpl $cpl)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_cpl' => 'required',
            'deskripsi' => 'required',
        ]);
        $cpl->update([
            'kode_cpl' => strtoupper($request->kode_cpl),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('kurikulum.cpl.index', $kurikulum->id)->with('success', 'CPL berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum, Cpl $cpl)
    {
        $this->authorizeKurikulum($kurikulum);
        $cpl->delete();

        return redirect()->route('kurikulum.cpl.index', $kurikulum->id)->with('success', 'CPL berhasil dihapus.');
    }
}
