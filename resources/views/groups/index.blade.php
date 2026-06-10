<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-3xl bg-gradient-to-br from-indigo-700 to-sky-600 p-8 text-white shadow-xl">
                <p class="text-sm font-bold uppercase tracking-widest text-indigo-100">Grupy rozliczeniowe</p>
                <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-black sm:text-4xl">Moje grupy</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-indigo-50">
                            Grupy sa odizolowane: zwykly uzytkownik widzi tylko grupy, do ktorych nalezy. Nazwy grup nie moga sie powtarzac.
                        </p>
                    </div>
                    <div class="rounded-2xl bg-white/15 px-5 py-4 text-sm font-bold">
                        Widoczne grupy: {{ $groups->total() }}
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200">{{ session('error') }}</div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Dodaj nowa grupe</h2>
                    <form action="{{ route('groups.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-200">Nazwa grupy</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Np. Wakacje 2026" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <label for="description" class="text-sm font-bold text-gray-700 dark:text-gray-200">Opis grupy</label>
                            <textarea id="description" name="description" rows="4" placeholder="Krótki opis celu grupy, np. wspolny wyjazd albo mieszkanie." class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">
                            Dodaj grupe
                        </button>
                    </form>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Wyszukiwarka i filtry</h2>
                    <form action="{{ route('groups.index') }}" method="GET" class="mt-5 grid gap-4 md:grid-cols-3">
                        <div class="{{ auth()->user()->isAdmin() ? '' : 'md:col-span-2' }}">
                            <label for="search" class="text-sm font-bold text-gray-700 dark:text-gray-200">Szukaj po nazwie lub opisie</label>
                            <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                        </div>
                        @if(auth()->user()->isAdmin())
                            <div>
                                <label for="owner" class="text-sm font-bold text-gray-700 dark:text-gray-200">Wlasciciel</label>
                                <input id="owner" type="search" name="owner" value="{{ $filters['owner'] ?? '' }}" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                            </div>
                        @endif
                        <div class="flex items-end gap-2">
                            <button type="submit" class="w-full rounded-xl bg-gray-900 px-5 py-3 text-sm font-black text-white hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">
                                Filtruj
                            </button>
                            <a href="{{ route('groups.index') }}" class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-black text-gray-700 hover:border-indigo-400 hover:text-indigo-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">
                                Wyczyść
                            </a>
                        </div>
                    </form>

                    <div class="mt-6 rounded-xl bg-indigo-50 p-4 text-sm leading-6 text-indigo-900 dark:bg-indigo-950 dark:text-indigo-200">
                        Jesli formularz nie przejdzie walidacji, zobaczysz tekstowy komunikat pod polem, a poprzednio wpisane dane zostana w formularzu.
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nazwa i opis</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Statystyki</th>
                                @if(auth()->user()->isAdmin())
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Wlasciciel</th>
                                @endif
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-gray-900">
                            @forelse($groups as $group)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-black text-gray-900 dark:text-gray-100">{{ $group->name }}</p>
                                        <p class="mt-1 max-w-xl text-sm text-gray-600 dark:text-gray-300">{{ $group->description ?: 'Brak opisu grupy.' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <p>{{ $group->users_count }} osob</p>
                                        <p>{{ $group->bills_count }} wydatkow</p>
                                    </td>
                                    @if(auth()->user()->isAdmin())
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $group->owner->name ?? '-' }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <a href="{{ route('groups.show', $group) }}" class="rounded-lg bg-indigo-50 px-3 py-2 font-bold text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-200 dark:hover:bg-indigo-900">Otworz</a>

                                            @if(auth()->user()->isAdmin() || $group->owner_id === auth()->id())
                                                <a href="{{ route('groups.edit', $group) }}" class="rounded-lg bg-gray-100 px-3 py-2 font-bold text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Edytuj</a>
                                                <form action="{{ route('groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunac te grupe?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg bg-red-50 px-3 py-2 font-bold text-red-700 hover:bg-red-100 dark:bg-red-950 dark:text-red-200 dark:hover:bg-red-900">Usun</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdmin() ? 4 : 3 }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Nie znaleziono grup dla podanych filtrow.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                    {{ $groups->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
