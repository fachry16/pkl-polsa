<section>
    <header style="margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">
            Perbarui Kata Sandi
        </h2>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 0;">
            Gunakan kata sandi yang panjang dan unik untuk menjaga keamanan akun Anda.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="update_password_current_password" style="font-size: 0.85rem; font-weight: 600; color: #334155; display: block; margin-bottom: 0.35rem;">
                Kata Sandi Saat Ini
            </label>
            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   style="width: 100%; max-width: 450px; padding: 0.55rem 0.85rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;"
                   autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="update_password_password" style="font-size: 0.85rem; font-weight: 600; color: #334155; display: block; margin-bottom: 0.35rem;">
                Kata Sandi Baru
            </label>
            <input id="update_password_password"
                   name="password"
                   type="password"
                   style="width: 100%; max-width: 450px; padding: 0.55rem 0.85rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;"
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="update_password_password_confirmation" style="font-size: 0.85rem; font-weight: 600; color: #334155; display: block; margin-bottom: 0.35rem;">
                Konfirmasi Kata Sandi Baru
            </label>
            <input id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   style="width: 100%; max-width: 450px; padding: 0.55rem 0.85rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;"
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div style="display: flex; align-items: center; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.85rem; border-radius: 8px;">
                Simpan Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <span x-data="{ show: true }"
                      x-show="show"
                      x-transition
                      x-init="setTimeout(() => show = false, 3000)"
                      style="color: #059669; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Kata sandi berhasil diperbarui.
                </span>
            @endif
        </div>
    </form>
</section>
