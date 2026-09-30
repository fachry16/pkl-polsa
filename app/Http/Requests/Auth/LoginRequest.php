<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'email' => $this->resolveLoginEmail(trim((string) $this->input('email'))),
            'password' => (string) $this->input('password'),
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Resolve input string to matching user identifier (NIM, NIDN, username, or email).
     */
    protected function resolveLoginEmail(string $input): string
    {
        if (str_contains($input, '@')) {
            return $input;
        }

        // 1. Cocokkan langsung users.email (contoh: NIM polos mahasiswa 202401001)
        if (\App\Models\User::where('email', $input)->exists()) {
            return $input;
        }

        // 2. Cocokkan dengan domain kampus @polsa.ac.id (contoh: admin, direktur, budi.santoso)
        $campusEmail = $input.'@polsa.ac.id';
        if (\App\Models\User::where('email', $campusEmail)->exists()) {
            return $campusEmail;
        }

        // 3. Cocokkan NIDN dosen
        $dosenUser = \App\Models\Dosen::where('nidn', $input)->with('user')->first()?->user;
        if ($dosenUser && $dosenUser->email) {
            return $dosenUser->email;
        }

        // 4. Cocokkan NIM mahasiswa jika terdaftar di tabel mahasiswas
        $mhsUser = \App\Models\Mahasiswa::where('nim', $input)->with('user')->first()?->user;
        if ($mhsUser && $mhsUser->email) {
            return $mhsUser->email;
        }

        return $input;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
