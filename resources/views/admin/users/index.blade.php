<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-3xl bg-gradient-to-br from-gray-950 to-indigo-900 p-8 text-white shadow-xl">
                <p class="text-sm font-bold uppercase tracking-widest text-indigo-200">Panel administratora</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Uzytkownicy i role</h1>
                <p class="mt-4 max-w-3xl text-sm leading-6 text-gray-200">
                    W aplikacji sa dwa typy kont: user oraz admin. Link do tego panelu jest widoczny tylko administratorom.
                </p>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200">{{ session('error') }}</div>
            @endif

            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Uzytkownicy</p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ $stats['users'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Administratorzy</p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ $stats['admins'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Grupy</p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ $stats['groups'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Wydatki</p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ $stats['bills'] }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Wyszukiwarka uzytkownikow</h2>
                <form action="{{ route('admin.users.index') }}" method="GET" class="mt-5 grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <label for="search" class="text-sm font-bold text-gray-700 dark:text-gray-200">Imie lub e-mail</label>
                        <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                    </div>
                    <div>
                        <label for="role" class="text-sm font-bold text-gray-700 dark:text-gray-200">Rola</label>
                        <select id="role" name="role" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                            <option value="">Wszystkie</option>
                            <option value="user" @selected(($filters['role'] ?? '') === 'user')>user</option>
                            <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>admin</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full rounded-xl bg-gray-900 px-5 py-3 text-sm font-black text-white hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">Filtruj</button>
                        <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-black text-gray-700 hover:border-indigo-400 hover:text-indigo-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">Wyczysc</a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Uzytkownik</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rola</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Grupy</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-4 py-4">
                                        <form id="user-form-{{ $user->id }}" action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="sr-only" for="name-{{ $user->id }}">Imie</label>
                                            <input id="name-{{ $user->id }}" type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-gray-300 bg-white text-sm font-bold text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4">
                                        <select name="role" form="user-form-{{ $user->id }}" class="rounded-lg border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                            <option value="user" @selected($user->role === 'user')>user</option>
                                            <option value="admin" @selected($user->role === 'admin')>admin</option>
                                        </select>
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $user->role === 'admin' ? 'Pelny dostep do panelu admina.' : 'Dostep tylko do wlasnych grup.' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $user->groups_count }}
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <button form="user-form-{{ $user->id }}" class="rounded-lg bg-indigo-50 px-3 py-2 text-sm font-bold text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-200 dark:hover:bg-indigo-900">Zapisz</button>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Usunac uzytkownika?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg bg-red-50 px-3 py-2 text-sm font-bold text-red-700 hover:bg-red-100 dark:bg-red-950 dark:text-red-200 dark:hover:bg-red-900">Usun</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Nie znaleziono uzytkownikow dla podanych filtrow.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
