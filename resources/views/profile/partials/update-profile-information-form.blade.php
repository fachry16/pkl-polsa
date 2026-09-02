<section>
    <header style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">
            Foto Profil &amp; Informasi Akun
        </h2>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 0;">
            Kelola foto profil Anda. Nama dan email resmi terikat dengan data akademik dan dikelola oleh Administrator.
        </p>
    </header>

    {{-- Bagian Personalisasi Foto Profil --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
            </svg>
            <span>Foto Profil Pengguna</span>
        </h3>

        <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;"
             x-data="{
                 previewUrl: null,
                 fileChosen(event) {
                     const file = event.target.files[0];
                     if (file) {
                         this.previewUrl = URL.createObjectURL(file);
                     }
                 }
             }">
            {{-- Preview Box --}}
            <div style="position: relative; flex-shrink: 0;">
                <template x-if="previewUrl">
                    <img :src="previewUrl" alt="Preview" style="width: 88px; height: 88px; border-radius: 14px; object-fit: cover; border: 2px solid #6366f1; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);">
                </template>
                <template x-if="!previewUrl">
                    <div>
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 88px; height: 88px; border-radius: 14px; object-fit: cover; border: 2px solid #e0e7ff; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                        @else
                            <div style="width: 88px; height: 88px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </template>
            </div>

            {{-- Form Upload & Action Buttons --}}
            <div style="flex: 1; min-width: 240px;">
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" style="margin-bottom: 0.5rem;">
                    @csrf
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                        <label class="btn btn-secondary btn-sm" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.85rem; font-size: 0.8rem; border-radius: 6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span>Pilih Gambar</span>
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/jpg" @change="fileChosen" style="display: none;" required>
                        </label>

                        <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.8rem; padding: 0.45rem 0.85rem; border-radius: 6px;">
                            Simpan Foto
                        </button>
                    </div>
                    @error('avatar')
                        <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </form>

                @if($user->avatar)
                    <form method="POST" action="{{ route('profile.avatar.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali menggunakan avatar inisial?');" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: transparent; border: none; padding: 0; color: #dc2626; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; text-decoration: underline;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            <span>Hapus foto (gunakan inisial)</span>
                        </button>
                    </form>
                @endif

                <div style="color: #94a3b8; font-size: 0.72rem; margin-top: 0.5rem;">
                    Format yang didukung: JPG, PNG, WEBP. Ukuran maksimal 2 MB.
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian Informasi Akun (Read-only / Non-editable) --}}
    <div>
        <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span>Identitas Akun Terdaftar (Data Akademik)</span>
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label style="font-size: 0.82rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.35rem;">
                    Nama Lengkap
                </label>
                <div style="position: relative;">
                    <input type="text"
                           value="{{ $user->name }}"
                           readonly
                           style="width: 100%; padding: 0.55rem 0.85rem 0.55rem 2.2rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #334155; font-size: 0.88rem; font-weight: 600; cursor: not-allowed;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="font-size: 0.82rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.35rem;">
                    Alamat Email
                </label>
                <div style="position: relative;">
                    <input type="email"
                           value="{{ $user->email }}"
                           readonly
                           style="width: 100%; padding: 0.55rem 0.85rem 0.55rem 2.2rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #334155; font-size: 0.88rem; font-weight: 600; cursor: not-allowed;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
            </div>
        </div>

        @php
            $roleMap = \App\Models\Role::all()->pluck('nama', 'kode');
            $rolesList = $user->getRolesList();
        @endphp
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
            <span style="font-size: 0.78rem; font-weight: 600; color: #64748b;">Peran Terdaftar:</span>
            @foreach($rolesList as $roleCode)
                <span style="font-size: 0.72rem; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 0.18rem 0.6rem; border-radius: 6px;">
                    {{ $roleMap->get($roleCode) ?? ucfirst(str_replace('_', ' ', $roleCode)) }}
                </span>
            @endforeach
        </div>

        {{-- Notice Security --}}
        <div style="display: flex; align-items: flex-start; gap: 0.6rem; padding: 0.75rem 1rem; border-radius: 8px; background: #eff6ff; border: 1px solid #dbeafe; color: #1e40af; font-size: 0.78rem; line-height: 1.5;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.1rem;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <strong>Informasi Integritas Data:</strong> Pengubahan nama lengkap dan email akun hanya dapat diproses oleh Administrator Kampus guna menjaga kesesuaian dengan arsip KRS, presensi, RPS, dan transkrip nilai akademik.
            </div>
        </div>
    </div>
</section>
