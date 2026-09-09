@php
    $assessment = $selected->assessment ?? null;
    $filters = $filters ?? [];
    $config = $summary['config'] ?? collect();
    $bobot = [];
    foreach ($config as $cpmkId => $meta) {
        $bobot[$cpmkId] = (float) $meta['bobot'];
    }
    $mkMax = array_sum($bobot);

    $xScores = [];
    if ($summary) {
        foreach ($summary['rows'] as $row) {
            $xScores[$row['mahasiswa']->id] = [];
            foreach ($config as $cpmkId => $meta) {
                $xScores[$row['mahasiswa']->id][$cpmkId] = $row['scores'][$cpmkId]['nilai'] ?? '';
            }
        }
    }

    $isFinal = ($assessment->status ?? '') === 'final';
    $canEdit = auth()->user()->isAdmin() || auth()->user()->isKaprodi() || (int) (auth()->user()->dosen?->id ?? 0) === (int) $selected->dosen_id;
    $readonly = $isFinal || ! $canEdit;
@endphp

@if($summary && $assessment && $config->isNotEmpty())

<div class="card" style="margin-bottom: 1.25rem; overflow: hidden;">

    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <div>
            <h2 style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin: 0;">
                {{ $selected->mataKuliah->kode }} — {{ $selected->mataKuliah->nama }}
            </h2>
            <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                Kelas {{ $selected->kelas }} · {{ ucfirst($selected->semester_akademik ?? '') }} · {{ $selected->mataKuliah->nama }}
            </div>
        </div>

        <div style="margin-left: auto; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @php
                $warna = $assessment->status === 'final' ? '#059669' : ($assessment->status === 'dinilai' ? '#2563eb' : '#94a3b8');
                $bg = $assessment->status === 'final' ? '#d1fae5' : ($assessment->status === 'dinilai' ? '#dbeafe' : '#f1f5f9');
            @endphp
            <span style="background: {{ $bg }}; color: {{ $warna }}; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                {{ $assessment->status_label }}
            </span>

            @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
            <form action="{{ route('assessment.finalize', $assessment->id) }}" method="POST" style="display: flex; gap: 8px; align-items: center; margin: 0;">
                @csrf
                @method('PATCH')
                <select name="status" class="form-select" style="width: auto; margin-bottom: 0;">
                    <option value="dinilai" {{ $assessment->status === 'dinilai' ? 'selected' : '' }}>Sudah Dinilai</option>
                    <option value="final" {{ $assessment->status === 'final' ? 'selected' : '' }}>Final (Dikunci)</option>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">Simpan Status</button>
            </form>
            <x-confirm
                action="{{ route('assessment.reset', $assessment->id) }}"
                method="DELETE"
                title="Reset Nilai Assessment"
                message="Hapus semua nilai CPMK pada assessment ini?"
                sub-message="Status akan kembali ke Draft."
                buttonText="Reset"
                confirmText="Ya, Reset"
                buttonClass="btn btn-sm btn-warning"
            />
            <x-confirm
                action="{{ route('assessment.destroy', $assessment->id) }}"
                method="DELETE"
                title="Hapus Assessment"
                message="Hapus assessment ini beserta seluruh nilainya?"
                sub-message="Data tidak dapat dikembalikan."
                buttonText="Hapus"
                confirmText="Ya, Hapus"
            />
            @elseif($isFinal)
            <span style="font-size: 0.8rem; color: #059669;">Dikunci final oleh Kaprodi.</span>
            @endif
        </div>
    </div>

    @if($readonly)
    <div style="background: #eff6ff; color: #1d4ed8; border-bottom: 1px solid #bfdbfe; padding: 0.75rem 1.25rem; font-size: 0.85rem;">
        {{ $isFinal ? 'Assessment telah difinalkan, nilai tidak dapat diubah.' : 'Anda hanya dapat melihat nilai di kelas ini.' }}
    </div>
    @endif

    <form action="{{ route('assessment.store') }}" method="POST"
          x-data="assessmentInput({{ json_encode(['scores' => $xScores, 'bobot' => $bobot, 'mkMax' => $mkMax]) }})">

        @csrf
        <input type="hidden" name="pengampu_id" value="{{ $selected->id }}">

        <div style="padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; gap: 16px; align-items: center;">
            <div>
                <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Progres Pengisian</div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 160px; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                        <div style="height: 100%; width: {{ collect($summary['rows'])->filter(fn ($r) => $r['dinilai'])->count() / max(1, collect($summary['rows'])->count()) * 100 }}%; background: #4f46e5; border-radius: 999px;"></div>
                    </div>
                    <span style="font-size: 0.85rem; font-weight: 700; color: #1e293b;">
                        {{ collect($summary['rows'])->filter(fn ($r) => $r['dinilai'])->count() }}/{{ count($summary['rows']) }} mahasiswa dinilai
                    </span>
                </div>
            </div>

            <div style="margin-left: auto; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" {{ $readonly ? 'disabled' : '' }}>
                    Simpan Nilai CPMK
                </button>
            </div>
        </div>

        <div style="overflow-x: auto; max-height: 600px; overflow-y: auto; position: relative;">
            <table class="data-table" style="min-width: {{ 320 + $config->count() * 150 }}px;">
                <thead style="position: sticky; top: 0; z-index: 5; background: #fff;">
                    <tr>
                        <th style="position: sticky; left: 0; background: #fff; z-index: 6; min-width: 220px;">Mahasiswa</th>
                        @foreach($config as $cpmkId => $meta)
                            <th title="{{ $meta['cpmk']->deskripsi ?? '' }}">
                                <div>{{ $meta['cpmk']->kode_cpmk }}</div>
                                <div style="font-weight: 500; font-size: 0.72rem; color: #94a3b8;">bobot {{ $meta['bobot'] }}</div>
                            </th>
                        @endforeach
                        <th>Nilai MK ({{ $mkMax }})</th>
                        <th>Capaian (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary['rows'] as $row)
                        <tr>
                            <td style="position: sticky; left: 0; background: #fff; z-index: 4;">
                                <div style="font-weight: 700; color: #1e293b;">{{ $row['mahasiswa']->nim }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $row['mahasiswa']->nama }}</div>
                            </td>
                            @foreach($config as $cpmkId => $meta)
                                <td>
                                    @if(! $readonly)
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           :max="{{ $meta['bobot'] }}"
                                           class="form-input"
                                           style="width: 90px; text-align: right;"
                                           name="scores[{{ $row['mahasiswa']->id }}][{{ $cpmkId }}]"
                                           x-model.number="scores[{{ $row['mahasiswa']->id }}][{{ $cpmkId }}]"
                                           placeholder="0">
                                    @else
                                    <div style="text-align: right; padding-right: 0.5rem;">
                                        {{ $row['scores'][$cpmkId]['nilai'] !== null ? number_format((float) $row['scores'][$cpmkId]['nilai'], 2) : '—' }}
                                    </div>
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <div style="font-weight: 800; color: #1e293b;">
                                    <span x-text="formatAngka(mkTotal({{ $row['mahasiswa']->id }}))">0</span>
                                </div>
                            </td>
                            <td>
                                <template x-if="mkCapaian({{ $row['mahasiswa']->id }}) > 0">
                                    <div>
                                        <div style="font-weight: 700; color: #2563eb;">
                                            <span x-text="mkCapaian({{ $row['mahasiswa']->id }}) + '%'">—</span>
                                        </div>
                                        <div style="height: 6px; width: 80px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                                            <div style="height: 100%; width: 0%; background: #2563eb; border-radius: 999px;"
                                                 :style="'width: ' + Math.min(100, mkCapaian({{ $row['mahasiswa']->id }})) + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!mkCapaian({{ $row['mahasiswa']->id }})">
                                    <span style="color: #94a3b8;">—</span>
                                </template>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding: 1rem 1.25rem; display: flex; gap: 10px; align-items: center;">
            <button type="submit" class="btn btn-primary" {{ $readonly ? 'disabled' : '' }}>
                Simpan Nilai CPMK
            </button>
            <a href="{{ route('assessment.rekap', array_filter(['mata_kuliah_id' => $selected->mata_kuliah_id] + $filters)) }}" class="btn btn-secondary">
                Lihat Rekap
            </a>
        </div>

    </form>

</div>

@push('scripts')
<script>
    window.formatAngka = function (v) {
        return (v === null || v === undefined || v === '' || isNaN(v)) ? '0' : Number(v).toFixed(2).replace(/\.00$/, '');
    };

    window.assessmentInput = function (initial) {
        return {
            scores: initial.scores,
            bobot: initial.bobot,
            mkMax: initial.mkMax,
            nilaiMhs(mhsId, cpmkId) {
                const v = this.scores[mhsId]?.[cpmkId];
                return (v === null || v === undefined || v === '' || isNaN(parseFloat(v))) ? 0 : parseFloat(v);
            },
            mkTotal(mhsId) {
                let total = 0;
                for (const cpmkId in this.bobot) {
                    total += this.nilaiMhs(mhsId, cpmkId);
                }
                return Math.round(total * 100) / 100;
            },
            mkCapaian(mhsId) {
                const t = this.mkTotal(mhsId);
                return this.mkMax > 0 ? Math.round((t / this.mkMax) * 100) : 0;
            },
        };
    };
</script>
@endpush

@else
<div class="card" style="padding: 1.5rem; margin-bottom: 1.25rem; border-left: 4px solid #f59e0b; background: #fffbeb;">
    <div style="display: flex; gap: 12px; align-items: flex-start;">
        <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div>
            <h3 style="font-size: 0.95rem; font-weight: 700; color: #92400e; margin: 0 0 0.4rem 0;">
                Rumusan Bobot CPMK Belum Dikonfigurasi di Kurikulum
            </h3>
            <p style="font-size: 0.85rem; color: #b45309; margin: 0 0 0.75rem 0; line-height: 1.5;">
                Mata kuliah <strong>{{ $selected->mataKuliah->nama ?? 'ini' }}</strong> belum memiliki Rumusan Nilai Akhir / Bobot CPMK. Agar nilai mahasiswa di LMS dapat dipetakan ke Asesmen OBE secara otomatis, silakan ikuti langkah-langkah berikut:
            </p>
            <ol style="font-size: 0.82rem; color: #78350f; margin: 0; padding-left: 1.2rem; line-height: 1.6;">
                <li>Buka menu <strong>Kurikulum</strong> &rarr; pilih Kurikulum aktif &rarr; klik menu <strong>Rumusan Nilai Akhir MK</strong>.</li>
                <li>Klik tombol <strong>+ Tambah Rumusan Nilai Akhir MK</strong> untuk memetakan Mata Kuliah, CPMK, CPL, dan Bobot/Skor Maks.</li>
                <li>Kembali ke halaman <strong>Kelas LMS (Rekap Nilai)</strong> lalu klik tombol <strong>Simpan & Kirim ke Asesmen OBE</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endif