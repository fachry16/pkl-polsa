<x-guest-layout>
    <h1>{{ __('Forgot Password') }}</h1>

    <div class="mb-4 text-sm">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
