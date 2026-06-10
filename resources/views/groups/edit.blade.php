<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <p class="text-sm font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-300">Edycja grupy</p>
                <h1 class="mt-2 text-2xl font-black text-gray-900 dark:text-gray-100">{{ $group->name }}</h1>

                <form action="{{ route('groups.update', $group) }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-200">Nazwa grupy</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $group->name) }}" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <label for="description" class="text-sm font-bold text-gray-700 dark:text-gray-200">Opis grupy</label>
                        <textarea id="description" name="description" rows="5" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">{{ old('description', $group->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">Zapisz zmiany</button>
                        <a href="{{ route('groups.show', $group) }}" class="rounded-xl border border-gray-300 px-5 py-3 text-center text-sm font-black text-gray-700 hover:border-indigo-400 hover:text-indigo-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">Anuluj</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
