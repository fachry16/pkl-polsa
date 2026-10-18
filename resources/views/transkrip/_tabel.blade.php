@if(count($rows) > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3rem; text-align: center;">No.</th>
                    <th rowspan="2">Status</th>
                    <th rowspan="2">Kode MK</th>
                    <th rowspan="2">Nama MK</th>
                    <th rowspan="2" style="text-align: right;">Bobot MK (sks)</th>
                    <th colspan="3" style="text-align: center;">Nilai</th>
                    <th rowspan="2" style="text-align: right;">sks * N.Indeks</th>
                </tr>
                <tr>
                    <th style="text-align: right;">Angka</th>
                    <th style="text-align: center;">Huruf</th>
                    <th style="text-align: right;">Indeks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                    <tr>
                        <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                        <td>
                            @if($row['is_sync'])
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Sudah Sync
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">
                                    Belum Sync
                                </span>
                            @endif
                        </td>
                        <td style="color: #475569;">{{ $row['kode'] }}</td>
                        <td style="font-weight: 600; color: #1e293b;">{{ $row['nama'] }}</td>
                        <td style="text-align: right; color: #475569;">{{ number_format($row['sks'], 2) }}</td>
                        <td style="text-align: right; color: #475569;">{{ $row['nilai_akhir'] !== null ? number_format($row['nilai_akhir'], 2) : '-' }}</td>
                        <td style="text-align: center;">
                            @if($row['huruf'])
                                @php
                                    $c = match ($row['huruf']) {
                                        'A', 'A-' => ['bg' => '#ecfdf5', 'fg' => '#059669', 'bd' => '#a7f3d0'],
                                        'B+', 'B', 'B-' => ['bg' => '#FFF8E0', 'fg' => '#B8860B', 'bd' => '#FFE88F'],
                                        'C+', 'C' => ['bg' => '#fef3c7', 'fg' => '#b45309', 'bd' => '#fde68a'],
                                        default => ['bg' => '#fee2e2', 'fg' => '#dc2626', 'bd' => '#fecaca'],
                                    };
                                @endphp
                                <span style="display: inline-block; background: {{ $c['bg'] }}; color: {{ $c['fg'] }}; border: 1px solid {{ $c['bd'] }}; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">{{ $row['huruf'] }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td style="text-align: right; color: #475569;">{{ $row['indeks'] !== null ? number_format($row['indeks'], 2) : '-' }}</td>
                        <td style="text-align: right; font-weight: 700; color: #1e293b;">{{ number_format($row['sks_indeks'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="overflow: hidden; border-radius: 0 0 12px 12px; margin-bottom: 1.25rem;">
        <table style="width: 100%; border-collapse: collapse; background: #f8fafc;">
            <tr>
                <td style="padding: 0.85rem 1.25rem; border: 1px solid #e2e8f0; border-top: none;">
                    <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">TOTAL SKS</span>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #1e293b;">{{ number_format($summary['total_sks'], 2) }}</div>
                </td>
                <td style="padding: 0.85rem 1.25rem; border: 1px solid #e2e8f0; border-top: none; border-left: none;">
                    <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">TOTAL NILAI (SKS × N.INDEKS)</span>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #1e293b;">{{ number_format($summary['total_nilai'], 2) }}</div>
                </td>
                <td style="padding: 0.85rem 1.25rem; border: 1px solid #e2e8f0; border-top: none; border-left: none;">
                    <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">IPK</span>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #059669;">{{ number_format($summary['ipk'], 2) }}</div>
                </td>
            </tr>
        </table>
    </div>
@else
    <div class="card" style="padding: 2.5rem; text-align: center; margin-bottom: 1.25rem;">
        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Belum ada data transkrip</div>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.3rem;">Mahasiswa belum memiliki mata kuliah pada rentang yang dipilih.</div>
    </div>
@endif