<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\BahanKajian;
use App\Models\Kurikulum;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class BahanKajianController extends Controller
{
    use AuthorizesKurikulum;

    public function downloadTemplate(CsvImportService $csvService)
    {
        $headers = ['kode_bk', 'nama_bk', 'referensi'];
        $samples = [
            ['BK01', 'Algoritma dan Pemrograman', 'Tanenbaum, A.S. (2015). Structured Computer Organization.'],
            ['BK02', 'Rekayasa Perangkat Lunak', 'Sommerville, I. (2016). Software Engineering.'],
        ];

        return $csvService->downloadTemplate('template_import_bahan_kajian.csv', $headers, $samples);
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
        $existing = $kurikulum->bahanKajians()->pluck('kode_bk')->map(fn ($k) => strtoupper(trim($k)))->all();

        foreach ($rows as $row) {
            $rowNum = $row['_row_number'] ?? '?';
            $kode = strtoupper(trim($row['kode_bk'] ?? ''));
            $nama = trim($row['nama_bk'] ?? '');
            $referensi = trim($row['referensi'] ?? '');

            if ($kode === '' || $nama === '') {
                $skipped[] = "Baris {$rowNum}: kode_bk dan nama_bk wajib diisi.";

                continue;
            }

            if (in_array($kode, $existing)) {
                $skipped[] = "Baris {$rowNum}: Kode BK {$kode} sudah terdaftar pada kurikulum ini.";

                continue;
            }

            BahanKajian::create([
                'kurikulum_id' => $kurikulum->id,
                'kode_bk' => $kode,
                'nama_bk' => $nama,
                'referensi' => $referensi,
            ]);
            $existing[] = $kode;
            $imported++;
        }

        $msg = "Berhasil mengimpor {$imported} data Bahan Kajian.";
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
        $bahanKajians = $kurikulum->bahanKajians()->latest()->paginate(10);

        return view('bahan-kajian.index', compact('kurikulum', 'bahanKajians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('bahan-kajian.create', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_bk' => 'required',
            'nama_bk' => 'required',
        ]);
        BahanKajian::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_bk' => strtoupper($request->kode_bk),
            'nama_bk' => $request->nama_bk,
            'referensi' => $request->referensi,
        ]);

        return redirect()->route('kurikulum.bahan-kajian.index', $kurikulum->id)->with('success', 'Bahan Kajian berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kurikulum $kurikulum, BahanKajian $bahanKajian) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum, BahanKajian $bahanKajian)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('bahan-kajian.edit', compact('kurikulum', 'bahanKajian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum, BahanKajian $bahanKajian)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_bk' => 'required',
            'nama_bk' => 'required',
        ]);
        $bahanKajian->update([
            'kode_bk' => strtoupper($request->kode_bk),
            'nama_bk' => $request->nama_bk,
            'referensi' => $request->referensi,
        ]);

        return redirect()->route('kurikulum.bahan-kajian.index', $kurikulum->id)->with('success', 'Bahan Kajian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum, BahanKajian $bahanKajian)
    {
        $this->authorizeKurikulum($kurikulum);
        $bahanKajian->delete();

        return redirect()->route('kurikulum.bahan-kajian.index', $kurikulum->id)->with('success', 'Bahan Kajian berhasil dihapus');
    }
}
