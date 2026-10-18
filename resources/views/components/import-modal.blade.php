@props([
    'title',
    'templateUrl',
    'actionUrl',
    'note' => 'Upload file CSV/Excel sesuai format template baku.',
    'columns' => [],
])

<div x-data="{ show: false }">
    <button type="button" {{ $attributes->merge(['class' => 'btn btn-secondary', 'style' => 'display: inline-flex; align-items: center; gap: 0.4rem;']) }} x-on:click="show = true">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Import Data Excel / CSV
    </button>

    <div x-show="show" x-cloak
         style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px);"
         @keydown.escape.window="show = false">

        <div @click.outside="show = false"
             style="background: #ffffff; border-radius: 14px; max-width: 540px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; border: 1px solid #e2e8f0;">

            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #FFF3C4; color: #A16207; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">{{ $title }}</h3>
                        <p style="margin: 0; font-size: 0.75rem; color: #64748b;">{{ $note }}</p>
                    </div>
                </div>
                <button type="button" @click="show = false" style="background: none; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer; padding: 0.25rem; border-radius: 6px;">&times;</button>
            </div>

            <form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
                @csrf
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">

                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 1rem;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #166534;">Langkah 1: Unduh Format Template</div>
                                <div style="font-size: 0.75rem; color: #15803d; margin-top: 0.2rem;">
                                    Gunakan template resmi agar kolom data sesuai dan dapat diproses sistem.
                                </div>
                            </div>
                            <a href="{{ $templateUrl }}" class="btn btn-sm" style="background: #16a34a; color: #fff; text-decoration: none; font-weight: 600; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Download Template
                            </a>
                        </div>
                    </div>

                    @if(count($columns) > 0)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem;">
                        <div style="font-weight: 700; font-size: 0.8rem; color: #334155; margin-bottom: 0.35rem;">Struktur Kolom Template:</div>
                        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.74rem; color: #64748b; line-height: 1.6;">
                            @foreach($columns as $column)
                                @php
                                    $label = is_array($column) ? ($column['label'] ?? $column['name']) : $column;
                                    $desc = is_array($column) ? ($column['desc'] ?? '') : '';
                                @endphp
                                <li><code>{{ $label }}</code>{!! $desc !== '' ? ': '.$desc : '' !!}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div>
                        <label class="form-label" style="font-weight: 600; font-size: 0.82rem; color: #1e293b; margin-bottom: 0.4rem; display: block;">
                            Langkah 2: Pilih File yang Sudah Diisi <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="form-input" style="width: 100%; font-size: 0.82rem; padding: 0.45rem;">
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem;">Format: CSV (Maksimal 5 MB). Pastikan kolom header tidak diubah.</div>
                    </div>

                </div>

                <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 0.6rem;">
                    <button type="button" @click="show = false" class="btn btn-secondary btn-sm" style="padding: 0.45rem 0.9rem;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.45rem 1rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Mulai Import Data
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>