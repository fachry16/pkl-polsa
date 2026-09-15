<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    use AuthorizesKurikulum;

    public function downloadTemplate(CsvImportService $csvService)
    {
        $headers = ['kode', 'nama', 'sks_teori', 'sks_praktikum', 'semester', 'jenis'];
        $samples = [
            ['TI1001', 'Algoritma dan Pemrograman', '2', '1', '1', 'Wajib'],
            ['TI1002', 'Basis Data', '2', '1', '2', 'Wajib'],
        ];

        return $csvService->downloadTemplate('template_import_mata_kuliah.csv', $headers, $samples);
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
        $existing = $kurikulum->mataKuliahs()->pluck('kode')->map(fn ($k) => strtoupper(trim($k)))->all();

        foreach ($rows as $row) {
            $rowNum = $row['_row_number'] ?? '?';
            $kode = strtoupper(trim($row['kode'] ?? ''));
            $nama = trim($row['nama'] ?? '');
            $sksTeori = trim($row['sks_teori'] ?? '');
            $sksPraktikum = trim($row['sks_praktikum'] ?? '');
            $semester = trim($row['semester'] ?? '');
            $jenis = trim($row['jenis'] ?? '');

            if ($kode === '' || $nama === '' || $sksTeori === '' || $sksPraktikum === '' || $semester === '' || $jenis === '') {
                $skipped[] = "Baris {$rowNum}: kode, nama, sks_teori, sks_praktikum, semester, dan jenis wajib diisi.";

                continue;
            }

            if (in_array($kode, $existing)) {
                $skipped[] = "Baris {$rowNum}: Kode MK {$kode} sudah terdaftar pada kurikulum ini.";

                continue;
            }

            if (! ctype_digit($sksTeori) || ! ctype_digit($sksPraktikum) || ! ctype_digit($semester)) {
                $skipped[] = "Baris {$rowNum}: sks_teori, sks_praktikum, dan semester harus berupa angka.";

                continue;
            }

            if ((int) $sksTeori > 6 || (int) $sksPraktikum > 6 || (int) $semester < 1 || (int) $semester > 14) {
                $skipped[] = "Baris {$rowNum}: nilai sks (0-6) atau semester (1-14) tidak valid.";

                continue;
            }

            if (! in_array($jenis, ['Wajib', 'Pilihan'])) {
                $skipped[] = "Baris {$rowNum}: jenis harus 'Wajib' atau 'Pilihan'.";

                continue;
            }

            MataKuliah::create([
                'kurikulum_id' => $kurikulum->id,
                'kode' => $kode,
                'nama' => $nama,
                'sks_teori' => (int) $sksTeori,
                'sks_praktikum' => (int) $sksPraktikum,
                'semester' => (int) $semester,
                'jenis' => $jenis,
            ]);
            $existing[] = $kode;
            $imported++;
        }

        $msg = "Berhasil mengimpor {$imported} data Mata Kuliah.";
        if (! empty($skipped)) {
            return back()->with('success', $msg.' '.count($skipped).' baris dilewati.')->with('import_warnings', $skipped);
        }

        return back()->with('success', $msg);
    }

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $mataKuliahs = $kurikulum->mataKuliahs()
            ->latest()
            ->paginate(10);

        return view('mata-kuliah.index', compact('kurikulum', 'mataKuliahs'));
    }

    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('mata-kuliah.create', compact('kurikulum'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode' => 'required|max:20',
            'nama' => 'required',
            'sks_teori' => 'required|integer|min:0|max:6',
            'sks_praktikum' => 'required|integer|min:0|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
        ]);

        $cek = MataKuliah::where('kurikulum_id', $kurikulum->id)
            ->where('kode', $request->kode)
            ->exists();

        if ($cek) {
            return back()
                ->withInput()
                ->withErrors(['kode' => 'Kode mata kuliah sudah digunakan pada kurikulum ini.']);
        }

        MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'sks_teori' => $request->sks_teori,
            'sks_praktikum' => $request->sks_praktikum,
            'semester' => $request->semester,
            'jenis' => $request->jenis,
        ]);

        return redirect()
            ->route('kurikulum.mata-kuliah.index', $kurikulum->id)
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function show(MataKuliah $mataKuliah)
    {
        //
    }

    public function edit(Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('mata-kuliah.edit', compact('mataKuliah', 'kurikulum'));
    }

    public function update(Request $request, Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'nama' => 'required',
            'sks_teori' => 'required|integer|min:0|max:6',
            'sks_praktikum' => 'required|integer|min:0|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
        ]);

        $mataKuliah->update([
            'nama' => $request->nama,
            'sks_teori' => $request->sks_teori,
            'sks_praktikum' => $request->sks_praktikum,
            'semester' => $request->semester,
            'jenis' => $request->jenis,
        ]);

        return redirect()
            ->route('kurikulum.mata-kuliah.index', $kurikulum->id)
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $this->authorizeKurikulum($kurikulum);
        $mataKuliah->delete();

        return redirect()
            ->route('kurikulum.mata-kuliah.index', $kurikulum->id)
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function struktur(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $mataKuliahs = $kurikulum->mataKuliahs()
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        return view('mata-kuliah.struktur', compact('kurikulum', 'mataKuliahs'));
    }
}
