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
        $headers = ['kode_bk', 'nama_bk', 'deskripsi', 'referensi'];
        $samples = [
            ['BK01', 'Algoritma dan Pemrograman', 'Struktur data, algoritma rekursif, dan kompleksitas.', 'Tanenbaum, A.S. (2015). Structured Computer Organization.'],
            ['BK02', 'Rekayasa Perangkat Lunak', 'Siklus pengembangan, pengujian, dan pemeliharaan perangkat lunak.', 'Sommerville, I. (2016). Software Engineering.'],
        ];

        return $csvService->downloadTemplate('template_import_bahan_kajian.csv', $headers, $samples);
    }

    public function import(Request $request, Kurikulum $kurikulum, CsvImportService $csvService)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'file' => 'required|file|max:5120|mimes:csv,txt',
        ]);

        $rows = $csvService->parseCsv($request->file('file')->getRealPath());

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $missing = array_diff(['kode_bk', 'nama_bk'], array_keys($rows[0]));
        if ($missing) {
            return back()->with('error', 'Header file tidak sesuai. Kolom wajib tidak ditemukan: '.implode(', ', $missing).'. Gunakan template resmi.');
        }

        $imported = 0;
        $skipped = [];
        $existing = $kurikulum->bahanKajians()->pluck('kode_bk')->map(fn ($k) => strtoupper(trim($k)))->all();

        foreach ($rows as $row) {
            $rowNum = $row['_row_number'] ?? '?';
            $kode = strtoupper(trim($row['kode_bk'] ?? ''));
            $nama = trim($row['nama_bk'] ?? '');
            $deskripsi = trim($row['deskripsi'] ?? '');
            $referensi = trim($row['referensi'] ?? '');

            if ($kode === '' || $nama === '') {
                $skipped[] = "Baris {$rowNum}: kode_bk dan nama_bk wajib diisi.";

                continue;
            }

            if (in_array($kode, $existing)) {
                $skipped[] = "Baris {$rowNum}: Kode BK {$kode} sudah terdaftar pada kurikulum ini.";

                continue;
            }

            try {
                BahanKajian::create([
                    'kurikulum_id' => $kurikulum->id,
                    'kode_bk' => $kode,
                    'nama_bk' => $nama,
                    'deskripsi' => $deskripsi,
                    'referensi' => $referensi,
                ]);
            } catch (\Throwable $e) {
                report($e);
                $skipped[] = "Baris {$rowNum}: gagal disimpan (".substr($e->getMessage(), 0, 120).').';

                continue;
            }
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
        $q = trim((string) request('cari'));
        $bahanKajians = $kurikulum->bahanKajians()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('kode_bk', 'like', "%{$q}%")
                ->orWhere('nama_bk', 'like', "%{$q}%")
                ->orWhere('deskripsi', 'like', "%{$q}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

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
            'deskripsi' => $request->deskripsi,
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
            'deskripsi' => $request->deskripsi,
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
