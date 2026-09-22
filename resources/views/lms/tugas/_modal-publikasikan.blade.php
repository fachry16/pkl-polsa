<x-modal name="publikasikan-tugas-modal" maxWidth="lg">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div style="width: 2rem; height: 2rem; border-radius: 8px; background: #FFF3C4; color: #A16207; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #0f172a;">Publikasikan Tugas ke Kelas</h3>
                <p style="margin: 0; font-size: 0.78rem; color: #64748b;">Konfirmasi publikasi draf tugas agar dapat diakses oleh mahasiswa.</p>
            </div>
        </div>
        <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'publikasikan-tugas-modal' }))" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0.25rem; border-radius: 6px;" onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94a3b8'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="form-publikasikan-tugas" method="POST" style="padding: 1.25rem 1.5rem;">
        @csrf

        {{-- Info Box Judul Tugas --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; margin-bottom: 0.25rem;">Judul Tugas</div>
            <div id="pub-modal-judul" style="font-size: 0.92rem; font-weight: 600; color: #1e293b;">-</div>
        </div>

        {{-- Batas Ukuran File Upload --}}
        <div style="margin-bottom: 1.25rem;">
            <label for="pub-modal-batas-mb" style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem;">
                Batas Ukuran File Mahasiswa (MB)
            </label>
            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
                <input type="number" id="pub-modal-batas-mb" name="batas_upload_mb" min="1" max="50" value="50" required class="form-input" style="width: 110px; font-weight: 600; text-align: center;">
                <span style="font-size: 0.85rem; color: #64748b; font-weight: 500;">Megabyte (MB)</span>
            </div>

            {{-- Quick Presets --}}
            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                <span style="font-size: 0.72rem; color: #94a3b8; margin-right: 0.2rem;">Pilihan Cepat:</span>
                @foreach([5, 10, 25, 50] as $preset)
                    <button type="button" onclick="setBatasUploadPreset({{ $preset }})" style="font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 6px; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; font-weight: 500; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#94a3b8';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#cbd5e1';">
                        {{ $preset }} MB
                    </button>
                @endforeach
            </div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.4rem; line-height: 1.4;">
                Maksimal ukuran file jawaban yang dapat diunggah setiap mahasiswa (1 - 50 MB, default: 50 MB).
            </div>
        </div>

        {{-- Tenggat Waktu (Deadline) --}}
        <div style="margin-bottom: 1.5rem;">
            <label for="pub-modal-deadline" style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem;">
                Tenggat Waktu Pengumpulan (Deadline)
            </label>
            <input type="datetime-local" id="pub-modal-deadline" name="deadline" class="form-input" style="width: 100%;">
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.4rem;">
                Tenggat waktu pengumpulan tugas mahasiswa. Dapat disesuaikan bila perlu.
            </div>
        </div>

        {{-- Info Alert --}}
        <div style="background: #FFF8E0; border: 1px solid #FFE88F; border-radius: 8px; padding: 0.75rem 0.9rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.6rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0A0D40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.1rem;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <div style="font-size: 0.75rem; color: #A16207; line-height: 1.45;">
                Setelah dipublikasikan, notifikasi tugas baru akan otomatis dikirimkan ke seluruh mahasiswa kelas, dosen pengampu, kaprodi, dan pimpinan terkait.
            </div>
        </div>

        {{-- Actions --}}
        <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
            <button type="button" class="btn btn-secondary" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'publikasikan-tugas-modal' }))">
                Batal
            </button>
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Publikasikan ke Kelas
            </button>
        </div>
    </form>
</x-modal>

<script>
function bukaModalPublikasi(url, judul, batasMb = 50, deadline = '') {
    const form = document.getElementById('form-publikasikan-tugas');
    if (form) form.action = url;
    const judulEl = document.getElementById('pub-modal-judul');
    if (judulEl) judulEl.textContent = judul;
    const batasEl = document.getElementById('pub-modal-batas-mb');
    if (batasEl) batasEl.value = batasMb || 50;
    const deadlineEl = document.getElementById('pub-modal-deadline');
    if (deadlineEl) deadlineEl.value = deadline || '';
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'publikasikan-tugas-modal' }));
}

function setBatasUploadPreset(mb) {
    const el = document.getElementById('pub-modal-batas-mb');
    if (el) el.value = mb;
}
</script>
