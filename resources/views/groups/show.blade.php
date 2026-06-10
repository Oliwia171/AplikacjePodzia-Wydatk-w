<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200">{{ session('error') }}</div>
            @endif

            <div class="rounded-3xl bg-gradient-to-br from-indigo-700 to-sky-600 p-8 text-white shadow-xl">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-indigo-100">Szczegoly grupy</p>
                        <h1 class="mt-3 text-3xl font-black sm:text-4xl">{{ $group->name }}</h1>
                        <p class="mt-4 max-w-3xl text-sm leading-6 text-indigo-50">
                            {{ $group->description ?: 'Ta grupa nie ma jeszcze opisu.' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @if(auth()->user()->isAdmin() || $group->owner_id === auth()->id())
                            <a href="{{ route('groups.edit', $group) }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-indigo-700 hover:bg-indigo-50">Edytuj grupe</a>
                        @endif
                        <a href="{{ route('groups.index') }}" class="rounded-xl border border-white/70 px-5 py-3 text-sm font-black text-white hover:bg-white/10">Wroc do listy</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Czlonkowie grupy</h2>
                        <div class="mt-4 space-y-3">
                            @foreach($group->users as $user)
                                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3 dark:bg-gray-950">
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-gray-100">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                    </div>
                                    @if($user->id === $group->owner_id)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black uppercase text-amber-700 dark:bg-amber-950 dark:text-amber-300">Lider</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <form action="{{ route('groups.add-user', $group) }}" method="POST" class="mt-6 space-y-3">
                            @csrf
                            <div>
                                <label for="email" class="text-sm font-bold text-gray-700 dark:text-gray-200">E-mail uzytkownika</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="znajomy@example.com" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-black text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">Dodaj do grupy</button>
                        </form>
                    </div>

                    <div class="rounded-2xl bg-gray-950 p-6 text-white shadow-lg">
                        <h2 class="text-lg font-black">Panel rozliczen</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-300">
                            Podzial dziala po rowno: kazdy wydatek jest dzielony miedzy aktualnych czlonkow grupy, a saldo pokazuje roznice miedzy zaplaconymi kwotami i naleznosciami.
                        </p>
                        <div class="mt-5 space-y-4">
                            @foreach($group->getBalances() as $data)
                                <div class="border-b border-gray-800 pb-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-bold text-sm">{{ $data['user']->name }}</span>
                                        <span class="{{ $data['balance'] >= 0 ? 'text-green-300' : 'text-red-300' }} font-black text-sm">
                                            {{ number_format($data['balance'], 2) }} PLN
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">Zaplacil: {{ number_format($data['paid'], 2) }} zl | Naleznosc: {{ number_format($data['owed'], 2) }} zl</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 rounded-xl bg-white/10 p-4 text-center">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Suma wydatkow</p>
                            <p class="mt-1 text-2xl font-black text-white">{{ number_format($group->total_amount, 2) }} PLN</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Dodaj wydatek</h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Nazwa i dodatnia kwota sa wymagane. Bledy nie kasuja wpisanych danych.</p>
                            </div>
                        </div>
                        <form action="{{ route('bills.store', $group) }}" method="POST" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-4">
                            @csrf
                            <div class="md:col-span-2">
                                <label for="description" class="text-sm font-bold text-gray-700 dark:text-gray-200">Nazwa wydatku</label>
                                <input id="description" type="text" name="description" value="{{ old('description') }}" placeholder="Np. obiad" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            <div>
                                <label for="amount" class="text-sm font-bold text-gray-700 dark:text-gray-200">Kwota</label>
                                <input id="amount" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>
                            <div>
                                <label for="payer_id" class="text-sm font-bold text-gray-700 dark:text-gray-200">Platnik</label>
                                <select id="payer_id" name="payer_id" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                    @foreach($group->users as $user)
                                        <option value="{{ $user->id }}" @selected((int) old('payer_id', auth()->id()) === $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('payer_id')" class="mt-2" />
                            </div>
                            <div class="md:col-span-4">
                                <button type="submit" class="w-full rounded-xl bg-green-600 px-5 py-3 text-sm font-black text-white hover:bg-green-700 dark:bg-green-500 dark:text-gray-950 dark:hover:bg-green-400">Zapisz wydatek</button>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Filtr historii</h2>
                        <form action="{{ route('groups.show', $group) }}" method="GET" class="mt-5 grid gap-4 md:grid-cols-5">
                            <div class="md:col-span-2">
                                <label for="bill_search" class="text-sm font-bold text-gray-700 dark:text-gray-200">Szukaj wydatku</label>
                                <input id="bill_search" type="search" name="bill_search" value="{{ $filters['bill_search'] ?? '' }}" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                            </div>
                            <div>
                                <label for="payer_filter" class="text-sm font-bold text-gray-700 dark:text-gray-200">Platnik</label>
                                <select id="payer_filter" name="payer_id" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                    <option value="">Wszyscy</option>
                                    @foreach($group->users as $user)
                                        <option value="{{ $user->id }}" @selected((string) ($filters['payer_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="amount_from" class="text-sm font-bold text-gray-700 dark:text-gray-200">Od kwoty</label>
                                <input id="amount_from" type="number" min="0" step="0.01" name="amount_from" value="{{ $filters['amount_from'] ?? '' }}" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                            </div>
                            <div>
                                <label for="amount_to" class="text-sm font-bold text-gray-700 dark:text-gray-200">Do kwoty</label>
                                <input id="amount_to" type="number" min="0" step="0.01" name="amount_to" value="{{ $filters['amount_to'] ?? '' }}" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                            </div>
                            <div class="flex gap-2 md:col-span-5">
                                <button type="submit" class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-black text-white hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">Filtruj</button>
                                <a href="{{ route('groups.show', $group) }}" class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-black text-gray-700 hover:border-indigo-400 hover:text-indigo-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">Wyczysc</a>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="border-b border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-950">
                            <h2 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-gray-300">Historia rozliczen</h2>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($bills as $bill)
                                <div class="p-5">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-lg font-black text-gray-900 dark:text-gray-100">{{ $bill->description }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Platnik: <span class="font-bold text-indigo-600 dark:text-indigo-300">{{ $bill->payer->name }}</span></p>
                                            <p class="mt-2 text-2xl font-black text-green-600 dark:text-green-300">{{ number_format($bill->amount, 2) }} PLN</p>
                                        </div>
                                        <form action="{{ route('bills.destroy', [$group, $bill]) }}" method="POST" onsubmit="return confirm('Usunac rachunek?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-red-50 px-3 py-2 text-sm font-bold text-red-700 hover:bg-red-100 dark:bg-red-950 dark:text-red-200 dark:hover:bg-red-900">Usun</button>
                                        </form>
                                    </div>

                                    @if($bill->splits->isNotEmpty())
                                        <div class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-700 dark:bg-gray-950 dark:text-gray-300">
                                            <p class="font-black">Podzial kosztu:</p>
                                            <p class="mt-1">
                                                @foreach($bill->splits as $split)
                                                    {{ $split->user->name }}: {{ number_format($split->amount, 2) }} zl{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </p>
                                        </div>
                                    @endif

                                    @if($bill->items->isNotEmpty())
                                        <div class="mt-4 rounded-xl border border-indigo-100 p-4 dark:border-indigo-900">
                                            <p class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Pozycje z paragonu</p>
                                            <div class="mt-2 space-y-2">
                                                @foreach($bill->items as $item)
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                                        <span class="font-bold">{{ $item->name }}</span>
                                                        ({{ number_format($item->price, 2) }} zl x {{ $item->quantity }})
                                                        - przypisane: {{ $item->users->pluck('name')->join(', ') ?: 'brak' }}
                                                    </p>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @php
                                        $isOldBillItem = (int) old('bill_item_bill_id') === $bill->id;
                                    @endphp
                                    <details class="mt-4 rounded-xl border border-gray-200 p-4 dark:border-gray-800" @if($isOldBillItem) open @endif>
                                        <summary class="cursor-pointer text-sm font-black text-indigo-700 dark:text-indigo-300">Dodaj pozycje z paragonu</summary>
                                        <form action="{{ route('bill-items.store', [$group, $bill]) }}" method="POST" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                                            @csrf
                                            <input type="hidden" name="bill_item_bill_id" value="{{ $bill->id }}">
                                            <div>
                                                <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Nazwa pozycji</label>
                                                <input type="text" name="name" value="{{ $isOldBillItem ? old('name') : '' }}" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-gray-900 text-sm dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                                @if($isOldBillItem)
                                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                @endif
                                            </div>
                                            <div>
                                                <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Cena</label>
                                                <input type="number" step="0.01" min="0.01" name="price" value="{{ $isOldBillItem ? old('price') : '' }}" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-gray-900 text-sm dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                                @if($isOldBillItem)
                                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                                @endif
                                            </div>
                                            <div>
                                                <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Liczba sztuk</label>
                                                <input type="number" name="quantity" value="{{ $isOldBillItem ? old('quantity', 1) : 1 }}" min="1" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-gray-900 text-sm dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                                @if($isOldBillItem)
                                                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Przypisz do</p>
                                                <div class="mt-2 flex flex-wrap gap-3">
                                                    @foreach($group->users as $user)
                                                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked($isOldBillItem && in_array($user->id, old('user_ids', [])))>
                                                            <span>{{ $user->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @if($isOldBillItem)
                                                    <x-input-error :messages="$errors->get('user_ids')" class="mt-2" />
                                                    <x-input-error :messages="$errors->get('user_ids.*')" class="mt-2" />
                                                @endif
                                            </div>
                                            <button class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-black text-white hover:bg-indigo-700 md:col-span-2 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">Dodaj pozycje</button>
                                        </form>
                                    </details>
                                </div>
                            @empty
                                <div class="p-10 text-center text-gray-500 dark:text-gray-400">Brak wydatkow dla podanych filtrow.</div>
                            @endforelse
                        </div>
                        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                            {{ $bills->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
