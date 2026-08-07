<section>
    <header>
        <h2 class="text-lg font-semibold">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-sm">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div class="mt-3">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Delete Account') }}</x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="card">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-2 text-sm">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="form-group mt-4">
                <x-input-label for="password" value="{{ __('Password') }}" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="flex justify-end gap-3 mt-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
