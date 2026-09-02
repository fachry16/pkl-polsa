<?php

use App\Http\Controllers\BahanKajianController;
use App\Http\Controllers\BahanKajianMataKuliahController;
use App\Http\Controllers\CplBkMkController;
use App\Http\Controllers\CplController;
use App\Http\Controllers\CplCpmkMkController;
use App\Http\Controllers\CplPlController;
use App\Http\Controllers\CpmkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\KhsController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\LmsAbsensiController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\LmsFileController;
use App\Http\Controllers\LmsForumController;
use App\Http\Controllers\LmsMahasiswaController;
use App\Http\Controllers\LmsMateriController;
use App\Http\Controllers\LmsPengumumanController;
use App\Http\Controllers\LmsTugasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MahasiswaTahunAkademikController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PemenuhanCplsController;
use App\Http\Controllers\PengampuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilLulusanController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\RpsController;
use App\Http\Controllers\RpsPenilaianController;
use App\Http\Controllers\RpsPertemuanController;
use App\Http\Controllers\RpsTugasController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

/* Dosen — lihat data diri sendiri */
Route::middleware(['auth'])->group(function () {
    Route::get(
        'dosen/self',
        [DosenController::class, 'self']
    )->name('dosen.self');

    Route::get(
        'dosen/self/riwayat',
        [DosenController::class, 'riwayatSelf']
    )->name('dosen.self.riwayat');
});

/* Master Data (Admin only) */
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get(
        'tahun-akademik',
        [TahunAkademikController::class, 'index']
    )->name('tahun-akademik.index');

    Route::get(
        'tahun-akademik/{tahunAkademik}/mahasiswa',
        [MahasiswaTahunAkademikController::class, 'index']
    )->name('tahun-akademik.mahasiswa.index');

    Route::get(
        'program-studi',
        [ProgramStudiController::class, 'index']
    )->name('program-studi.index');

    Route::get(
        'dosen',
        [DosenController::class, 'index']
    )->name('dosen.index');

    Route::get(
        'dosen/{dosen}/riwayat',
        [DosenController::class, 'riwayat']
    )->name('dosen.riwayat');

    Route::get(
        'mahasiswa',
        [MahasiswaController::class, 'index']
    )->name('mahasiswa.index');

    Route::get(
        'pengampu',
        [PengampuController::class, 'index']
    )->name('pengampu.index');
});

/* Admin only (mutations + user management) */
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('tahun-akademik', TahunAkademikController::class)
        ->except(['index']);

    Route::patch(
        'tahun-akademik/{tahunAkademik}/aktifkan',
        [TahunAkademikController::class, 'aktifkan']
    )->name('tahun-akademik.aktifkan');

    Route::get(
        'tahun-akademik/{tahunAkademik}/mahasiswa/create',
        [MahasiswaTahunAkademikController::class, 'create']
    )->name('tahun-akademik.mahasiswa.create');

    Route::post(
        'tahun-akademik/{tahunAkademik}/mahasiswa',
        [MahasiswaTahunAkademikController::class, 'store']
    )->name('tahun-akademik.mahasiswa.store');

    Route::delete(
        'tahun-akademik/{tahunAkademik}/mahasiswa/{mahasiswaTahunAkademik}',
        [MahasiswaTahunAkademikController::class, 'destroy']
    )->name('tahun-akademik.mahasiswa.destroy');

    Route::resource('program-studi', ProgramStudiController::class)
        ->except(['index']);

    Route::get('dosen/template-import', [DosenController::class, 'downloadTemplate'])->name('dosen.template-import');
    Route::post('dosen/import', [DosenController::class, 'import'])->name('dosen.import');
    Route::resource('dosen', DosenController::class)
        ->except(['index']);

    Route::get('mahasiswa/template-import', [MahasiswaController::class, 'downloadTemplate'])->name('mahasiswa.template-import');
    Route::post('mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::resource('mahasiswa', MahasiswaController::class)
        ->except(['index'])
        ->where(['mahasiswa' => '[0-9]+']);

    Route::resource('pengampu', PengampuController::class)
        ->except(['index', 'show', 'edit', 'update']);

    Route::post(
        'pengampu/{pengampu}/kelas/mahasiswa',
        [PengampuController::class, 'storeMahasiswa']
    )->name('pengampu.kelas.mahasiswa.store');

    Route::delete(
        'pengampu/{pengampu}/kelas/mahasiswa/{mahasiswa}',
        [PengampuController::class, 'destroyMahasiswa']
    )->name('pengampu.kelas.mahasiswa.destroy');

    Route::resource('users', UserController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
});

/* KRS — Admin, Kaprodi */
Route::middleware(['auth', 'role:admin,kaprodi'])->group(function () {

    Route::get(
        'krs',
        [KrsController::class, 'index']
    )->name('krs.index');

    Route::get(
        'krs/{krs}',
        [KrsController::class, 'show']
    )->where('krs', '[0-9]+')->name('krs.show');

    Route::get(
        'krs/cetak/pilih-mahasiswa',
        [KrsController::class, 'cetakPilih']
    )->name('krs.cetak-pilih');

    Route::post(
        'krs/cetak/pilih-mahasiswa',
        [KrsController::class, 'pilihMahasiswa']
    )->name('krs.pilih-mahasiswa');

    Route::get(
        'krs/cetak/{mahasiswa}',
        [KrsController::class, 'cetak']
    )->where(['mahasiswa' => '[0-9]+'])->name('krs.cetak');

    Route::get(
        'krs/cetak/{mahasiswa}/pdf',
        [KrsController::class, 'cetakPdf']
    )->where(['mahasiswa' => '[0-9]+'])->name('krs.cetak-pdf');

    Route::get(
        'krs/cetak/mahasiswa-options',
        [KrsController::class, 'mahasiswaOptions']
    )->name('krs.mahasiswa-options');
});

/* KHS — Admin, Kaprodi, Direktur, Mahasiswa */
Route::middleware(['auth'])->group(function () {
    Route::get(
        'khs',
        [KhsController::class, 'index']
    )->middleware('role:admin,kaprodi,direktur')->name('khs.index');

    Route::post(
        'khs/approve-all',
        [KhsController::class, 'approveAll']
    )->middleware('role:admin,kaprodi')->name('khs.approve-all');

    Route::post(
        'khs/{mahasiswa}/{tahunAkademik}/approve',
        [KhsController::class, 'approve']
    )->middleware('role:admin,kaprodi')->name('khs.approve');

    Route::post(
        'khs/{mahasiswa}/{tahunAkademik}/unapprove',
        [KhsController::class, 'unapprove']
    )->middleware('role:admin,kaprodi')->name('khs.unapprove');

    Route::get(
        'khs/cetak/pilih-mahasiswa',
        [KhsController::class, 'cetakPilih']
    )->middleware('role:admin,kaprodi,direktur')->name('khs.cetak-pilih');

    Route::post(
        'khs/cetak/pilih-mahasiswa',
        [KhsController::class, 'pilihMahasiswa']
    )->middleware('role:admin,kaprodi,direktur')->name('khs.pilih-mahasiswa');

    Route::get(
        'khs/self',
        [KhsController::class, 'self']
    )->name('khs.self');

    Route::get(
        'khs/cetak/{mahasiswa}',
        [KhsController::class, 'cetak']
    )->where(['mahasiswa' => '[0-9]+'])->name('khs.cetak');

    Route::get(
        'khs/cetak/{mahasiswa}/pdf',
        [KhsController::class, 'cetakPdf']
    )->where(['mahasiswa' => '[0-9]+'])->name('khs.cetak-pdf');
});

/* KRS — Admin + Kaprodi (mutations) */
Route::middleware(['auth', 'role:admin,kaprodi'])->group(function () {

    Route::get(
        'krs/create',
        [KrsController::class, 'create']
    )->name('krs.create');

    Route::post(
        'krs',
        [KrsController::class, 'store']
    )->name('krs.store');

    Route::delete(
        'krs/{krs}',
        [KrsController::class, 'destroy']
    )->name('krs.destroy');

    Route::post(
        'krs/{krs}/mahasiswa',
        [KrsController::class, 'storeMahasiswa']
    )->name('krs.mahasiswa.store');

    Route::delete(
        'krs/{krs}/mahasiswa/{mahasiswa}',
        [KrsController::class, 'destroyMahasiswa']
    )->name('krs.mahasiswa.destroy');
});

/* Semua yang login (Dosen + Kaprodi + Admin) */
Route::middleware(['auth'])->group(function () {

    Route::get(
        'pengampu/{pengampu}/kelas',
        [PengampuController::class, 'lihatKelas']
    )->name('pengampu.lihat-kelas');

    Route::get(
        'program-studi/{programStudi}/kurikulum',
        [KurikulumController::class, 'indexByProgramStudi']
    )->name('program-studi.kurikulum');

    Route::get(
        'kurikulum/{kurikulum}/detail',
        [KurikulumController::class, 'detail']
    )->name('kurikulum.detail');

    Route::get(
        'kurikulum/{kurikulum}/mata-kuliah/struktur',
        [MataKuliahController::class, 'struktur']
    )->name('kurikulum.struktur');

    /* Kurikulum management — Admin + Kaprodi */
    Route::middleware(['role:admin,kaprodi'])->group(function () {

        /* Kurikulum */
        Route::get(
            'kurikulum/create',
            [KurikulumController::class, 'create']
        )->name('kurikulum.create');

        Route::post(
            'kurikulum',
            [KurikulumController::class, 'store']
        )->name('kurikulum.store');

        Route::get(
            'kurikulum/{kurikulum}/edit',
            [KurikulumController::class, 'edit']
        )->name('kurikulum.edit');

        Route::put(
            'kurikulum/{kurikulum}',
            [KurikulumController::class, 'update']
        )->name('kurikulum.update');

        Route::delete(
            'kurikulum/{kurikulum}',
            [KurikulumController::class, 'destroy']
        )->name('kurikulum.destroy');

        Route::patch(
            'kurikulum/{kurikulum}/aktifkan',
            [KurikulumController::class, 'aktifkan']
        )->name('kurikulum.aktifkan');

        Route::get(
            'kurikulum',
            [KurikulumController::class, 'index']
        )->name('kurikulum.index');

        /* CPL */
        Route::get(
            'kurikulum/{kurikulum}/cpl',
            [CplController::class, 'index']
        )->name('kurikulum.cpl.index');

        Route::get(
            'kurikulum/{kurikulum}/cpl/create',
            [CplController::class, 'create']
        )->name('kurikulum.cpl.create');

        Route::post(
            'kurikulum/{kurikulum}/cpl',
            [CplController::class, 'store']
        )->name('kurikulum.cpl.store');

        Route::get(
            'kurikulum/{kurikulum}/cpl/{cpl}/edit',
            [CplController::class, 'edit']
        )->name('kurikulum.cpl.edit');

        Route::put(
            'kurikulum/{kurikulum}/cpl/{cpl}',
            [CplController::class, 'update']
        )->name('kurikulum.cpl.update');

        Route::delete(
            'kurikulum/{kurikulum}/cpl/{cpl}',
            [CplController::class, 'destroy']
        )->name('kurikulum.cpl.destroy');

        /* CPMK */
        Route::get(
            'kurikulum/{kurikulum}/cpmk',
            [CpmkController::class, 'index']
        )->name('kurikulum.cpmk.index');

        Route::get(
            'kurikulum/{kurikulum}/cpmk/create',
            [CpmkController::class, 'create']
        )->name('kurikulum.cpmk.create');

        Route::post(
            'kurikulum/{kurikulum}/cpmk',
            [CpmkController::class, 'store']
        )->name('kurikulum.cpmk.store');

        Route::get(
            'kurikulum/{kurikulum}/cpmk/{cpmk}/edit',
            [CpmkController::class, 'edit']
        )->name('kurikulum.cpmk.edit');

        Route::put(
            'kurikulum/{kurikulum}/cpmk/{cpmk}',
            [CpmkController::class, 'update']
        )->name('kurikulum.cpmk.update');

        Route::delete(
            'kurikulum/{kurikulum}/cpmk/{cpmk}',
            [CpmkController::class, 'destroy']
        )->name('kurikulum.cpmk.destroy');

        /* Bahan Kajian */
        Route::get(
            'kurikulum/{kurikulum}/bahan-kajian',
            [BahanKajianController::class, 'index']
        )->name('kurikulum.bahan-kajian.index');

        Route::get(
            'kurikulum/{kurikulum}/bahan-kajian/create',
            [BahanKajianController::class, 'create']
        )->name('kurikulum.bahan-kajian.create');

        Route::post(
            'kurikulum/{kurikulum}/bahan-kajian',
            [BahanKajianController::class, 'store']
        )->name('kurikulum.bahan-kajian.store');

        Route::get(
            'kurikulum/{kurikulum}/bahan-kajian/{bahanKajian}/edit',
            [BahanKajianController::class, 'edit']
        )->name('kurikulum.bahan-kajian.edit');

        Route::put(
            'kurikulum/{kurikulum}/bahan-kajian/{bahanKajian}',
            [BahanKajianController::class, 'update']
        )->name('kurikulum.bahan-kajian.update');

        Route::delete(
            'kurikulum/{kurikulum}/bahan-kajian/{bahanKajian}',
            [BahanKajianController::class, 'destroy']
        )->name('kurikulum.bahan-kajian.destroy');

        /* Profil Lulusan */
        Route::get(
            'kurikulum/{kurikulum}/profil-lulusan',
            [ProfilLulusanController::class, 'index']
        )->name('kurikulum.profil-lulusan.index');

        Route::get(
            'kurikulum/{kurikulum}/profil-lulusan/create',
            [ProfilLulusanController::class, 'create']
        )->name('kurikulum.profil-lulusan.create');

        Route::post(
            'kurikulum/{kurikulum}/profil-lulusan',
            [ProfilLulusanController::class, 'store']
        )->name('kurikulum.profil-lulusan.store');

        Route::get(
            'kurikulum/{kurikulum}/profil-lulusan/{profilLulusan}/edit',
            [ProfilLulusanController::class, 'edit']
        )->name('kurikulum.profil-lulusan.edit');

        Route::put(
            'kurikulum/{kurikulum}/profil-lulusan/{profilLulusan}',
            [ProfilLulusanController::class, 'update']
        )->name('kurikulum.profil-lulusan.update');

        Route::delete(
            'kurikulum/{kurikulum}/profil-lulusan/{profilLulusan}',
            [ProfilLulusanController::class, 'destroy']
        )->name('kurikulum.profil-lulusan.destroy');

        /* Matriks Mapping */
        Route::get(
            'kurikulum/{kurikulum}/cpl-pl',
            [CplPlController::class, 'index']
        )->name('kurikulum.cpl-pl.index');

        Route::match(
            ['PUT', 'POST'],
            'kurikulum/{kurikulum}/cpl-pl',
            [CplPlController::class, 'update']
        )->name('kurikulum.cpl-pl.update');

        Route::get(
            'kurikulum/{kurikulum}/bk-mk',
            [BahanKajianMataKuliahController::class, 'index']
        )->name('kurikulum.bk-mk.index');

        Route::match(
            ['PUT', 'POST'],
            'kurikulum/{kurikulum}/bk-mk',
            [BahanKajianMataKuliahController::class, 'update']
        )->name('kurikulum.bk-mk.update');

        Route::get(
            'kurikulum/{kurikulum}/cpl-bk-mk',
            [CplBkMkController::class, 'index']
        )->name('kurikulum.cpl-bk-mk.index');

        Route::post(
            'kurikulum/{kurikulum}/cpl-bk-mk',
            [CplBkMkController::class, 'store']
        )->name('kurikulum.cpl-bk-mk.store');

        Route::get(
            'kurikulum/{kurikulum}/cpl-cpmk-mk',
            [CplCpmkMkController::class, 'index']
        )->name('kurikulum.cpl-cpmk-mk.index');

        Route::post(
            'kurikulum/{kurikulum}/cpl-cpmk-mk',
            [CplCpmkMkController::class, 'store']
        )->name('kurikulum.cpl-cpmk-mk.store');

        Route::get(
            'kurikulum/{kurikulum}/pemenuhan-cpl',
            [PemenuhanCplsController::class, 'index']
        )->name('kurikulum.pemenuhan-cpl.index');

        Route::post(
            'kurikulum/{kurikulum}/pemenuhan-cpl',
            [PemenuhanCplsController::class, 'store']
        )->name('kurikulum.pemenuhan-cpl.store');

        Route::get(
            'kurikulum/{kurikulum}/mata-kuliah',
            [MataKuliahController::class, 'index']
        )->name('kurikulum.mata-kuliah.index');

        Route::get(
            'kurikulum/{kurikulum}/mata-kuliah/create',
            [MataKuliahController::class, 'create']
        )->name('kurikulum.mata-kuliah.create');

        Route::post(
            'kurikulum/{kurikulum}/mata-kuliah',
            [MataKuliahController::class, 'store']
        )->name('kurikulum.mata-kuliah.store');

        Route::get(
            'kurikulum/{kurikulum}/mata-kuliah/{mataKuliah}/edit',
            [MataKuliahController::class, 'edit']
        )->name('kurikulum.mata-kuliah.edit');

        Route::put(
            'kurikulum/{kurikulum}/mata-kuliah/{mataKuliah}',
            [MataKuliahController::class, 'update']
        )->name('kurikulum.mata-kuliah.update');

        Route::delete(
            'kurikulum/{kurikulum}/mata-kuliah/{mataKuliah}',
            [MataKuliahController::class, 'destroy']
        )->name('kurikulum.mata-kuliah.destroy');

    });

    Route::resource(
        'mata-kuliah.rps',
        RpsController::class
    )->parameters(['rps' => 'rps']);

    Route::patch(
        'rps/{rps}/ajukan',
        [RpsController::class, 'ajukan']
    )->name('rps.ajukan');

    Route::get(
        'rps/{rps}/ekstrak-pdf',
        [RpsController::class, 'ekstrakPdf']
    )->name('rps.ekstrak-pdf');

    Route::get(
        'rps/{rps}/pertemuan',
        [RpsPertemuanController::class, 'index']
    )->name('rps.pertemuan.index');

    Route::get(
        'rps/{rps}/pertemuan/create',
        [RpsPertemuanController::class, 'create']
    )->name('rps.pertemuan.create');

    Route::post(
        'rps/{rps}/pertemuan',
        [RpsPertemuanController::class, 'store']
    )->name('rps.pertemuan.store');

    Route::get(
        'rps/{rps}/pertemuan/{pertemuan}/edit',
        [RpsPertemuanController::class, 'edit']
    )->name('rps.pertemuan.edit');

    Route::put(
        'rps/{rps}/pertemuan/{pertemuan}',
        [RpsPertemuanController::class, 'update']
    )->name('rps.pertemuan.update');

    Route::delete(
        'rps/{rps}/pertemuan/{pertemuan}',
        [RpsPertemuanController::class, 'destroy']
    )->name('rps.pertemuan.destroy');

    Route::get(
        'rps/{rps}/penilaian',
        [RpsPenilaianController::class, 'index']
    )->name('rps.penilaian.index');

    Route::get(
        'rps/{rps}/penilaian/create',
        [RpsPenilaianController::class, 'create']
    )->name('rps.penilaian.create');

    Route::post(
        'rps/{rps}/penilaian',
        [RpsPenilaianController::class, 'store']
    )->name('rps.penilaian.store');

    Route::get(
        'rps/{rps}/penilaian/edit',
        [RpsPenilaianController::class, 'edit']
    )->name('rps.penilaian.edit');

    Route::put(
        'rps/{rps}/penilaian',
        [RpsPenilaianController::class, 'update']
    )->name('rps.penilaian.update');

    Route::get(
        'rps/{rps}/tugas',
        [RpsTugasController::class, 'index']
    )->name('rps.tugas.index');

    Route::get(
        'rps/{rps}/tugas/create',
        [RpsTugasController::class, 'create']
    )->name('rps.tugas.create');

    Route::post(
        'rps/{rps}/tugas',
        [RpsTugasController::class, 'store']
    )->name('rps.tugas.store');

    Route::get(
        'rps/{rps}/tugas/{tugas}/edit',
        [RpsTugasController::class, 'edit']
    )->name('rps.tugas.edit');

    Route::put(
        'rps/{rps}/tugas/{tugas}',
        [RpsTugasController::class, 'update']
    )->name('rps.tugas.update');

    Route::delete(
        'rps/{rps}/tugas/{tugas}',
        [RpsTugasController::class, 'destroy']
    )->name('rps.tugas.destroy');
});

/* Kaprodi */
Route::middleware(['auth', 'Kaprodi'])->group(function () {

    Route::get(
        'rps/pengajuan',
        [RpsController::class, 'pengajuan']
    )->name('rps.pengajuan');

    Route::patch(
        'rps/{rps}/setujui',
        [RpsController::class, 'setujui']
    )->name('rps.setujui');

    Route::patch(
        'rps/{rps}/revisi',
        [RpsController::class, 'revisi']
    )->name('rps.revisi');
});

/* Direktur */
Route::middleware(['auth', 'role:direktur'])->group(function () {

    Route::get(
        '/dashboard-direktur',
        [DashboardController::class, 'index']
    )->name('dashboard-direktur');
});

/* LMS */
Route::middleware(['auth'])->prefix('kelas')->name('lms.')->group(function () {

    Route::get('/kelas-saya', [LmsController::class, 'index'])->name('index');

    Route::get('/monitor-kelas', [LmsController::class, 'monitor'])->middleware('role:admin')->name('monitor');

    Route::get('/file/{model}/{id}', [LmsFileController::class, 'show'])->name('file');

    Route::get('/{pengampu}', [LmsController::class, 'show'])->name('show');

    Route::get('/{pengampu}/materi', [LmsMateriController::class, 'index'])->name('materi.index');
    Route::post('/{pengampu}/materi', [LmsMateriController::class, 'store'])->name('materi.store');
    Route::get('/{pengampu}/materi/{materi}', [LmsMateriController::class, 'show'])->name('materi.show');
    Route::get('/{pengampu}/materi/{materi}/edit', [LmsMateriController::class, 'edit'])->name('materi.edit');
    Route::patch('/{pengampu}/materi/{materi}', [LmsMateriController::class, 'update'])->name('materi.update');
    Route::delete('/{pengampu}/materi/{materi}', [LmsMateriController::class, 'destroy'])->name('materi.destroy');

    Route::get('/{pengampu}/tugas', [LmsTugasController::class, 'index'])->name('tugas.index');
    Route::post('/{pengampu}/tugas', [LmsTugasController::class, 'store'])->name('tugas.store');
    Route::get('/{pengampu}/tugas/{tugas}', [LmsTugasController::class, 'show'])->name('tugas.show');
    Route::get('/{pengampu}/tugas/{tugas}/edit', [LmsTugasController::class, 'edit'])->name('tugas.edit');
    Route::patch('/{pengampu}/tugas/{tugas}', [LmsTugasController::class, 'update'])->name('tugas.update');
    Route::delete('/{pengampu}/tugas/{tugas}', [LmsTugasController::class, 'destroy'])->name('tugas.destroy');

    Route::post('/{pengampu}/topik-komentar', [\App\Http\Controllers\LmsTopikKomentarController::class, 'store'])->name('topik.komentar.store');
    Route::delete('/{pengampu}/topik-komentar/{komentar}', [\App\Http\Controllers\LmsTopikKomentarController::class, 'destroy'])->name('topik.komentar.destroy');

    Route::patch('/submission/{submission}/nilai', [LmsTugasController::class, 'nilai'])->name('submission.nilai');

    Route::get('/{pengampu}/forum', [LmsForumController::class, 'index'])->name('forum.index');
    Route::post('/{pengampu}/forum', [LmsForumController::class, 'store'])->name('forum.store');
    Route::get('/{pengampu}/forum/{diskusi}/edit', [LmsForumController::class, 'edit'])->name('forum.edit');
    Route::patch('/{pengampu}/forum/{diskusi}', [LmsForumController::class, 'update'])->name('forum.update');
    Route::delete('/{pengampu}/forum/{diskusi}', [LmsForumController::class, 'destroy'])->name('forum.destroy');

    Route::get('/{pengampu}/pengumuman', [LmsPengumumanController::class, 'index'])->name('pengumuman.index');
    Route::post('/{pengampu}/pengumuman', [LmsPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::get('/{pengampu}/pengumuman/{pengumuman}/edit', [LmsPengumumanController::class, 'edit'])->name('pengumuman.edit');
    Route::patch('/{pengampu}/pengumuman/{pengumuman}', [LmsPengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/{pengampu}/pengumuman/{pengumuman}', [LmsPengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    Route::get('/{pengampu}/rekap-nilai', [LmsTugasController::class, 'rekap'])->name('tugas.rekap');
    Route::post('/{pengampu}/rekap-nilai/komponen', [LmsTugasController::class, 'simpanKomponen'])->name('tugas.komponen');
    Route::post('/{pengampu}/hitung-ulang-nilai', [LmsTugasController::class, 'hitungUlangNilai'])->name('tugas.sync');

    Route::get('/{pengampu}/absensi', [LmsAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/{pengampu}/absensi', [LmsAbsensiController::class, 'bukaSesi'])->name('absensi.buka');
    Route::get('/{pengampu}/absensi/sesi/{sesi}', [LmsAbsensiController::class, 'show'])->name('absensi.show');
    Route::post('/{pengampu}/absensi/sesi/{sesi}', [LmsAbsensiController::class, 'simpan'])->name('absensi.store');
});

/* Mahasiswa LMS */
Route::middleware(['auth'])->prefix('mahasiswa')->name('mahasiswa.lms.')->group(function () {
    Route::get('/kelas-saya', [LmsMahasiswaController::class, 'index'])->name('index');
    Route::get('/kelas/{pengampu}', [LmsMahasiswaController::class, 'show'])->name('show');
    Route::get('/kelas/{pengampu}/materi/{materi}', [LmsMahasiswaController::class, 'showMateri'])->name('materi.show');
    Route::get('/kelas/{pengampu}/tugas/{tugas}', [LmsMahasiswaController::class, 'showTugas'])->name('tugas.show');
    Route::post('/kelas/{pengampu}/topik-komentar', [\App\Http\Controllers\LmsTopikKomentarController::class, 'store'])->name('topik.komentar.store');
    Route::delete('/kelas/{pengampu}/topik-komentar/{komentar}', [\App\Http\Controllers\LmsTopikKomentarController::class, 'destroy'])->name('topik.komentar.destroy');
    Route::post('/kelas/{pengampu}/forum', [LmsMahasiswaController::class, 'storeForum'])->name('forum.store');
    Route::patch('/kelas/{pengampu}/forum/{diskusi}', [LmsMahasiswaController::class, 'updateForum'])->name('forum.update');
    Route::delete('/kelas/{pengampu}/forum/{diskusi}', [LmsMahasiswaController::class, 'destroyForum'])->name('forum.destroy');
    Route::post('/tugas/{tugas}/kumpul', [LmsMahasiswaController::class, 'storeSubmission'])->name('tugas.kumpul');
    Route::patch('/tugas/{submission}/perbarui', [LmsMahasiswaController::class, 'updateSubmission'])->name('tugas.update');
    Route::post('/materi/{materi}/selesai', [LmsMahasiswaController::class, 'toggleMateriSelesai'])->name('materi.selesai');
    Route::get('/file/{model}/{id}', [LmsFileController::class, 'show'])->name('file');
});

require __DIR__.'/auth.php';
