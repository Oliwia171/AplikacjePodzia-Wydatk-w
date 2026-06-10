<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-100">
            {{ __('Start') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-700 via-indigo-600 to-sky-600 shadow-xl">
                <div class="px-6 py-10 sm:px-10 lg:px-14 lg:py-14">
                    <p class="text-sm font-bold uppercase tracking-widest text-indigo-100">Aplikacja do rozliczen grupowych</p>
                    <h1 class="mt-4 max-w-3xl text-3xl font-black text-white sm:text-5xl">
                        Rozliczaj wspolne wydatki w odizolowanych grupach.
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-indigo-50 sm:text-lg">
                        Strona startowa jest dostepna bez logowania. Tworzenie grup, dodawanie wydatkow i panel admina wymagaja konta, zeby dane prywatnych grup byly widoczne tylko dla ich czlonkow.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('groups.index') }}" class="inline-flex justify-center rounded-xl bg-white px-6 py-3 text-sm font-black text-indigo-700 shadow hover:bg-indigo-50">
                                Przejdz do moich grup
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex justify-center rounded-xl bg-white px-6 py-3 text-sm font-black text-indigo-700 shadow hover:bg-indigo-50">
                                Zaloguj sie
                            </a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex justify-center rounded-xl border border-white/70 px-6 py-3 text-sm font-black text-white hover:bg-white/10">
                                    Utworz konto
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Grupy</p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ $groupsCount ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Uzytkownicy</p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ $usersCount ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Wydatki</p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ $billsCount ?? 0 }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-lg font-black text-gray-900 dark:text-gray-100">Dwa typy kont</h3>
                    <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Konto user zarzadza wlasnymi grupami i wydatkami. Konto admin widzi panel administracyjny, moze zarzadzac uzytkownikami i kontrolowac role.
                    </p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-lg font-black text-gray-900 dark:text-gray-100">Odizolowane grupy</h3>
                    <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Zwykly uzytkownik widzi tylko grupy, do ktorych nalezy. Link do panelu admina pojawia sie w menu tylko administratorom.
                    </p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-lg font-black text-gray-900 dark:text-gray-100">Walidacja danych</h3>
                    <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Formularze odrzucaja brak nazwy wydatku, duplikaty nazw grup oraz kwoty ujemne. Po bledzie wpisane dane zostaja w formularzu.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
