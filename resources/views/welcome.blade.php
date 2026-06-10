<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Rozliczenia') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @include('layouts.theme-script')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <div class="min-h-screen">
            <header class="border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="text-xl font-black tracking-tight text-indigo-700 dark:text-indigo-300">
                        Rozliczenia
                    </a>

                    <div class="flex items-center gap-3">
                        <x-theme-toggle />
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">
                                Panel startowy
                            </a>
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
                </div>
            </header>

            <main>
                <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                    <div class="grid items-center gap-10 lg:grid-cols-[1.1fr_0.9fr]">
                        <div>
                            <p class="text-sm font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-300">Dostepne bez logowania</p>
                            <h1 class="mt-4 max-w-4xl text-4xl font-black tracking-tight text-gray-950 dark:text-white sm:text-6xl">
                                Proste rozliczanie wspolnych wydatkow w prywatnych grupach.
                            </h1>
                            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-600 dark:text-gray-300">
                                Strona startowa i opis aplikacji sa widoczne dla kazdego. Po zalogowaniu uzytkownik tworzy grupy, dodaje znajomych i wydatki, a administrator zarzadza kontami w osobnym panelu.
                            </p>

                            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ route('dashboard') }}" class="inline-flex justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">
                                    Zobacz strone
                                </a>
                                @guest
                                    <a href="{{ route('login') }}" class="inline-flex justify-center rounded-xl border border-gray-300 px-6 py-3 text-sm font-black text-gray-800 hover:border-indigo-400 hover:text-indigo-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">
                                        Przejdz do logowania
                                    </a>
                                @endguest
                            </div>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                            <h2 class="text-xl font-black text-gray-950 dark:text-white">Co jest zabezpieczone</h2>
                            <div class="mt-6 space-y-4">
                                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-950">
                                    <p class="font-bold text-gray-900 dark:text-gray-100">Role uzytkownikow</p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Dostepne sa dwa typy kont: user oraz admin.</p>
                                </div>
                                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-950">
                                    <p class="font-bold text-gray-900 dark:text-gray-100">Izolacja grup</p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Zwykly uzytkownik widzi tylko grupy, do ktorych nalezy.</p>
                                </div>
                                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-950">
                                    <p class="font-bold text-gray-900 dark:text-gray-100">Poprawne formularze</p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Nazwa wydatku jest wymagana, kwoty musza byc dodatnie, a nazwy grup nie moga sie powtarzac.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-y border-gray-200 bg-white py-10 dark:border-gray-800 dark:bg-gray-900">
                    <div class="mx-auto grid max-w-7xl gap-4 px-4 sm:grid-cols-3 sm:px-6 lg:px-8">
                        <div class="rounded-2xl bg-gray-50 p-6 dark:bg-gray-950">
                            <p class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Grupy</p>
                            <p class="mt-2 text-3xl font-black">{{ $groupsCount ?? 0 }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 p-6 dark:bg-gray-950">
                            <p class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Uzytkownicy</p>
                            <p class="mt-2 text-3xl font-black">{{ $usersCount ?? 0 }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 p-6 dark:bg-gray-950">
                            <p class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Wydatki</p>
                            <p class="mt-2 text-3xl font-black">{{ $billsCount ?? 0 }}</p>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                    <div class="grid gap-6 lg:grid-cols-3">
                        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <h3 class="text-lg font-black">Jak dziala podzial</h3>
                            <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                Wydatek jest dzielony po rowno miedzy czlonkow grupy. System zapisuje splity i pokazuje, kto zaplacil oraz ile kazdy powinien oddac.
                            </p>
                        </article>
                        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <h3 class="text-lg font-black">Co gdy cos nie dziala</h3>
                            <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                Formularze wyswietlaja bledy jako zwykly tekst pod polami. Po nieudanej probie poprzednio wpisane wartosci zostaja zachowane.
                            </p>
                        </article>
                        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <h3 class="text-lg font-black">Panel admina</h3>
                            <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                Administrator ma osobny link w menu, wyszukiwarke uzytkownikow, filtr roli, paginacje i mozliwosc zmiany typu konta.
                            </p>
                        </article>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
