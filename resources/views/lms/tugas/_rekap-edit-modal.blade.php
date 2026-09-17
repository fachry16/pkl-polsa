@php
    $url = route('lms.tugas.komponen', $pengampu->id);
@endphp

<div
    x-data="{ show: false, m: null }"
    x-on:open-rekap-edit.window="m = $event.detail; show = true"
    x-on:keydown.escape.window="show = false"
    x-cloak
    x-show="show"
    style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px);">

    <div @click.outside="show = false"
         style="background: #ffffff; border-radius: 14px; max-width: 480px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2), 0 10px 10px -5px rgba(0,0,0,0.04); overflow: hidden; border: 1px solid #e2e8f0;">

        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Edit Penilaian Soft Skills</h3>
                    <p style="margin: 0; font-size: 0.78rem; color: #64748b;" x-text="m ? m.nim + ' — ' + m.nama : ''"></p>
                </div>
            </div>
            <button type="button" @click="show = false" style="background: none; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer; padding: 0.25rem; border-radius: 6px;">&times;</button>
        </div>

        <form method="POST" :action="m ? '{{ $url }}' : '#'" style="margin: 0;">
            @csrf

            <template x-if="m">
                <div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.1rem;">

                        <div>
                            <label style="font-weight: 600; font-size: 0.82rem; color: #1e293b; margin-bottom: 0.4rem; display: block;">Absensi (%)</label>
                            <input type="number" min="0" max="100" step="0.01" x-model="m.absensi" :name="'nilai[' + m.id + '][absensi]'" placeholder="-" style="width: 100%; padding: 0.5rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
                            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem;">Kosongkan untuk memakai hitungan otomatis dari presensi.</div>
                        </div>

                        <div>
                            <label style="font-weight: 600; font-size: 0.82rem; color: #1e293b; margin-bottom: 0.4rem; display: block;">Keaktifan (%)</label>
                            <input type="number" min="0" max="100" step="0.01" x-model="m.keaktifan" :name="'nilai[' + m.id + '][keaktifan]'" placeholder="-" style="width: 100%; padding: 0.5rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
                        </div>

                        <div>
                            <label style="font-weight: 600; font-size: 0.82rem; color: #1e293b; margin-bottom: 0.4rem; display: block;">Etika (%)</label>
                            <input type="number" min="0" max="100" step="0.01" x-model="m.etika" :name="'nilai[' + m.id + '][etika]'" placeholder="-" style="width: 100%; padding: 0.5rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
                        </div>

                    </div>

                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 0.6rem;">
                        <button type="button" @click="show = false" class="btn btn-secondary btn-sm" style="padding: 0.45rem 0.9rem;">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.45rem 1rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Simpan
                        </button>
                    </div>
                </div>
            </template>
        </form>
    </div>
</div>