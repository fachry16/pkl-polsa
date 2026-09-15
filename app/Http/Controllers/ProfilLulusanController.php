<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use App\Models\ProfilLulusan;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class ProfilLulusanController extends Controller
{
    use AuthorizesKurikulum;

    public function downloadTemplate(CsvImportService $csvService)
    {
        $headers = ['kode_pl', 'nama_pl', 'profesi'];
        $samples = [
            ['PL01', 'Software Engineer', 'Software Engineer, System Analyst'],
            ['PL02', 'Database Administrator', 'Database Administrator'],
        ];

        return $csvService->downloadTemplate('template_import_profil_lulusan.csv', $headers, $samples);
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
        $existing = $kurikulum->profilLulusans()->pluck('kode_pl')->map(fn ($k) => strtoupper(trim($k)))->all();

        foreach ($rows as $row) {
            $rowNum = $row['_row_number'] ?? '?';
            $kode = strtoupper(trim($row['kode_pl'] ?? ''));
            $nama = trim($row['nama_pl'] ?? '');
            $profesi = trim($row['profesi'] ?? '');

            if ($kode === '' || $nama === '') {
                $skipped[] = "Baris {$rowNum}: kode_pl dan nama_pl wajib diisi.";

                continue;
            }

            if (in_array($kode, $existing)) {
                $skipped[] = "Baris {$rowNum}: Kode PL {$kode} sudah terdaftar pada kurikulum ini.";

                continue;
            }

            ProfilLulusan::create([
                'kurikulum_id' => $kurikulum->id,
                'kode_pl' => $kode,
                'nama_pl' => $nama,
                'profesi' => $profesi,
            ]);
            $existing[] = $kode;
            $imported++;
        }

        $msg = "Berhasil mengimpor {$imported} data Profil Lulusan.";
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
        $profilLulusans = $kurikulum->profilLulusans()->latest()->paginate(10);

        return view(
            'profil-lulusan.index',
            compact('kurikulum', 'profilLulusans')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('profil-lulusan.create', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_pl' => 'required',
            'nama_pl' => 'required',
        ]);
        ProfilLulusan::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_pl' => $request->kode_pl,
            'nama_pl' => $request->nama_pl,
            'profesi' => $request->profesi,
        ]);

        return redirect()->route('kurikulum.profil-lulusan.index', $kurikulum->id)->with('success', 'Profil Lulusan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum, ProfilLulusan $profilLulusan)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('profil-lulusan.edit', compact('kurikulum', 'profilLulusan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum, ProfilLulusan $profilLulusan)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode_pl' => 'required',
            'nama_pl' => 'required',
        ]);
        $profilLulusan->update([
            'kode_pl' => $request->kode_pl,
            'nama_pl' => $request->nama_pl,
            'profesi' => $request->profesi,
        ]);

        return redirect()->route('kurikulum.profil-lulusan.index', $kurikulum->id)->with('success', 'Profil Lulusan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum, ProfilLulusan $profilLulusan)
    {
        $this->authorizeKurikulum($kurikulum);
        $profilLulusan->delete();

        return redirect()->route('kurikulum.profil-lulusan.index', $kurikulum->id)->with('success', 'Profil Lulusan berhasil dihapus.');
    }
}
