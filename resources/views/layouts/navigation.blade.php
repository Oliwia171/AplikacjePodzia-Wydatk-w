<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 transition-colors dark:bg-gray-900 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-lg font-black tracking-tight text-indigo-700 dark:text-indigo-300">
                    Rozliczenia
                </a>

                <div class="hidden space-x-8 sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Start') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('groups.index')" :active="request()->routeIs('groups.*')">
                            {{ __('Moje grupy') }}
                        </x-nav-link>

                        @if(Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.*')">
                                {{ __('Panel admina') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                <x-theme-toggle />

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                                {{ Auth::user()->name }}
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Wyloguj') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-700 dark:text-gray-200 dark:hover:text-indigo-300">
                        Zaloguj
                    </a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">
                            Utworz konto
                        </a>
                    @endif
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out dark:border-gray-700 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800 dark:focus:bg-gray-800">
                    Menu
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Start') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('groups.index')" :active="request()->routeIs('groups.*')">
                    {{ __('Moje grupy') }}
                </x-responsive-nav-link>

                @if(Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.*')">
                        {{ __('Panel admina') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-800">
            <div class="px-4 pb-3">
                <x-theme-toggle />
            </div>

            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-100">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profil') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Wyloguj') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Zaloguj') }}
                    </x-responsive-nav-link>
                    @if(Route::has('register'))
                        <x-responsive-nav-link :href="route('register')">
                            {{ __('Utworz konto') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
