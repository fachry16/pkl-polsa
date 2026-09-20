<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForceChangePasswordController extends Controller
{
    /**
     * Tampilkan formulir wajib ganti password.
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ! $user->harus_ganti_password || $user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        return view('auth.force-change-password', compact('user'));
    }

    /**
     * Simpan password baru dari user.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || $user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Cegah menggunakan kembali password default
        $defaultPassword = $user->defaultPassword();
        if ($request->password === $defaultPassword) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password default ('.$defaultPassword.'). Silakan gunakan password lain.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'harus_ganti_password' => false,
        ])->save();

        return redirect()->route('dashboard')->with('success', 'Password Anda berhasil diperbarui. Selamat datang di Eduva!');
    }
}
