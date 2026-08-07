<nav x-data="{ open: false }" class="topnav">
    <div class="topnav-links">
        <a href="{{ route('dashboard') }}">
            {{ __('Dashboard') }}
        </a>
    </div>

    <div class="topnav-links">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="btn btn-secondary btn-sm">
                    {{ Auth::user()->name }}
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>

    <div x-show="open" class="hidden">
        <div>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div>
            <div>
                <div class="font-semibold">{{ Auth::user()->name }}</div>
                <div class="text-sm">{{ Auth::user()->email }}</div>
            </div>

            <div>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
