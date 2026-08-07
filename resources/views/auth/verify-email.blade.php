<x-guest-layout>
    <h1>{{ __('Verify Email') }}</h1>

    <div class="mb-4 text-sm">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    <x-alert type="success" :message="session('status') == 'verification-link-sent' ? __('A new verification link has been sent to the email address you provided during registration.') : null" />

    <div class="flex items-center justify-between mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
