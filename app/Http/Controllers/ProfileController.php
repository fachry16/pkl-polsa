<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        if ($request->hasFile('avatar')) {
            $this->processAvatarUpload($request);
        }

        return Redirect::route('profile.edit')->with('toast_success', 'Profil berhasil diperbarui.');
    }

    /**
     * Upload or update user avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'file', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Pilih file gambar terlebih dahulu.',
            'avatar.file' => 'File harus berupa dokumen gambar valid.',
            'avatar.mimes' => 'Format gambar yang diperbolehkan: JPEG, PNG, JPG, WEBP.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $this->processAvatarUpload($request);

        return Redirect::route('profile.edit')->with('toast_success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Delete user avatar (revert to default initial).
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('toast_success', 'Foto profil berhasil dihapus. Kembali ke avatar inisial default.');
    }

    /**
     * Process avatar storage.
     */
    protected function processAvatarUpload(Request $request): void
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
    }

    /**
     * Delete the user's account (Disabled for academic data integrity).
     */
    public function destroy(Request $request): RedirectResponse
    {
        return Redirect::route('profile.edit')->with('toast_error', 'Penghapusan akun dinonaktifkan demi menjaga integritas data akademik.');
    }
}
