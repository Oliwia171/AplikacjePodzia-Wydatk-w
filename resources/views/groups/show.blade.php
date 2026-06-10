<x-app-layout>
    @php
        $balances = collect($group->getBalances());
        $membersCount = $group->users->count();
        $billsCount = $group->bills->count();
        $totalAmount = (float) $group->total_amount;
        $averagePerMember = $membersCount > 0 ? $totalAmount / $membersCount : 0;
        $settledMembers = $balances->filter(fn ($data) => abs((float) $data['balance']) < 0.01)->count();
    @endphp

    <div class="min-h-screen bg-slate-100 py-12 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800 shadow-sm dark:border-green-900 dark:bg-green-950 dark:text-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800 shadow-sm dark:border-red-900 dark:bg-red-950 dark:text-red-200">{{ session('error') }}</div>
            @endif

            <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 via-indigo-900 to-sky-700 text-white shadow-2xl shadow-indigo-950/20">
                <div class="p-8 sm:p-10">
                    <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.3em] text-sky-200">Profesjonalny panel grupy</p>
                            <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-5xl">{{ $group->name }}</h1>
                            <p class="mt-5 max-w-3xl text-sm leading-6 text-slate-200 sm:text-base">
                                {{ $group->description ?: 'Ta grupa nie ma jeszcze opisu. Dodaj opis, zeby latwiej rozpoznac cel rozliczenia.' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @if(auth()->user()->isAdmin() || $group->owner_id === auth()->id())
                                <a href="{{ route('groups.edit', $group) }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-indigo-800 shadow-lg shadow-black/10 transition hover:bg-indigo-50">Edytuj grupe</a>
                            @endif
                            <a href="{{ route('groups.index') }}" class="rounded-xl border border-white/60 px-5 py-3 text-sm font-black text-white transition hover:bg-white/10">Wroc do listy</a>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Suma wydatkow</p>
                            <p class="mt-2 text-3xl font-black">{{ number_format($totalAmount, 2) }} PLN</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Czlonkowie</p>
                            <p class="mt-2 text-3xl font-black">{{ $membersCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Rachunki</p>
                            <p class="mt-2 text-3xl font-black">{{ $billsCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Srednio na osobe</p>
                            <p class="mt-2 text-3xl font-black">{{ number_format($averagePerMember, 2) }} PLN</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6">
                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-300">Zespol</p>
                                <h2 class="mt-2 text-xl font-black text-gray-900 dark:text-gray-100">Czlonkowie grupy</h2>
                            </div>
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-black text-indigo-700 dark:bg-indigo-950 dark:text-indigo-200">{{ $membersCount }} osob</span>
                        </div>

                        <div class="mt-5 space-y-3">
                            @foreach($group->users as $user)
                                <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                    </div>
                                    @if($user->id === $group->owner_id)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black uppercase text-amber-700 dark:bg-amber-950 dark:text-amber-300">Lider</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <form action="{{ route('groups.add-user', $group) }}" method="POST" class="mt-6 space-y-3 rounded-2xl border border-dashed border-gray-300 p-4 dark:border-gray-700">
                            @csrf
                            <div>
                                <label for="email" class="text-sm font-bold text-gray-700 dark:text-gray-200">E-mail uzytkownika</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="znajomy@example.com" class="mt-1 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-black text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">Dodaj do grupy</button>
                        </form>
                    </div>

                    <div class="rounded-3xl bg-gray-950 p-6 text-white shadow-xl shadow-gray-950/20">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-sky-300">Bilans</p>
                                <h2 class="mt-2 text-xl font-black">Panel rozliczen</h2>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-slate-200">{{ $settledMembers }}/{{ $membersCount }} na zero</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-gray-300">
                            Saldo pokazuje roznice miedzy tym, ile dana osoba zaplacila za rachunki, a ile wynosi suma jej udzialow w kosztach.
                        </p>

                        <div class="mt-5 rounded-2xl border border-white/10 bg-white/10 p-4">
                            <p class="text-xs font-black uppercase tracking-widest text-slate-300">Wzor salda</p>
                            <p class="mt-2 font-mono text-sm text-white">Saldo = zaplacone - naleznosc</p>
                            <p class="mt-2 text-xs leading-5 text-slate-300">Wynik dodatni oznacza nadplate, wynik ujemny oznacza kwote do oddania.</p>
                        </div>

                        <div class="mt-5 space-y-4">
                            @forelse($balances as $data)
                                @php
                                    $balance = (float) $data['balance'];
                                @endphp
                                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-bold text-sm">{{ $data['user']->name }}</span>
                                        <span class="{{ $balance >= 0 ? 'text-green-300' : 'text-red-300' }} font-black text-sm">
                                            {{ number_format($balance, 2) }} PLN
                                        </span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-400">
                                        Zaplacil: {{ number_format($data['paid'], 2) }} zl | Naleznosc: {{ number_format($data['owed'], 2) }} zl
                                    </p>
                                    <div class="mt-3 rounded-xl bg-black/20 p-3 text-xs text-slate-300">
                                        {{ number_format($data['paid'], 2) }} - {{ number_format($data['owed'], 2) }} = <span class="font-black text-white">{{ number_format($balance, 2) }} PLN</span>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-sm text-gray-300">Dodaj czlonkow, zeby zobaczyc bilans.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-6 shadow-sm dark:border-indigo-900 dark:bg-indigo-950/60">
                        <p class="text-xs font-black uppercase tracking-widest text-indigo-700 dark:text-indigo-300">Jak to sie liczy</p>
                        <h2 class="mt-2 text-xl font-black text-gray-950 dark:text-white">Zasada rozliczenia</h2>
                        <div class="mt-5 space-y-4 text-sm leading-6 text-indigo-950 dark:text-indigo-100">
                            <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-gray-950">
                                <p class="font-black">1. Udzial w rachunku</p>
                                <p class="mt-1">Udzial = kwota rachunku / liczba osob w grupie.</p>
                            </div>
                            <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-gray-950">
                                <p class="font-black">2. Grosze</p>
                                <p class="mt-1">Kwota jest dzielona w groszach, wiec suma udzialow zawsze rowna sie kwocie rachunku.</p>
                            </div>
                            <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-gray-950">
                                <p class="font-black">3. Saldo osoby</p>
                                <p class="mt-1">Saldo = suma zaplaconych rachunkow - suma udzialow tej osoby.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-green-600 dark:text-green-300">Nowy koszt</p>
                                <h2 class="mt-2 text-xl font-black text-gray-900 dark:text-gray-100">Dodaj wydatek</h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Nazwa i dodatnia kwota sa wymagane. Po zapisie system od razu tworzy udzialy dla czlonkow grupy.</p>
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
                                <button type="submit" class="w-full rounded-xl bg-green-600 px-5 py-3 text-sm font-black text-white transition hover:bg-green-700 dark:bg-green-500 dark:text-gray-950 dark:hover:bg-green-400">Zapisz wydatek i przelicz saldo</button>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Historia</p>
                                <h2 class="mt-2 text-xl font-black text-gray-900 dark:text-gray-100">Filtr rachunkow</h2>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Filtry nie zmieniaja salda, tylko liste widocznych wpisow.</p>
                        </div>
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
                                <button type="submit" class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-black text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">Filtruj</button>
                                <a href="{{ route('groups.show', $group) }}" class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-black text-gray-700 transition hover:border-indigo-400 hover:text-indigo-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">Wyczysc</a>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="border-b border-gray-200 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-950">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Audyt rozliczen</p>
                                    <h2 class="mt-2 text-xl font-black text-gray-900 dark:text-gray-100">Historia z obliczeniami</h2>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kazdy rachunek pokazuje kwote, platnika, udzialy i wzor.</p>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($bills as $bill)
                                @php
                                    $splitCount = $bill->splits->count();
                                    $averageSplit = $splitCount > 0 ? (float) $bill->amount / $splitCount : 0;
                                    $totalSplit = $bill->splits->sum('amount');
                                @endphp
                                <div class="p-6">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-xl font-black text-gray-900 dark:text-gray-100">{{ $bill->description }}</p>
                                                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-black text-green-700 dark:bg-green-950 dark:text-green-300">{{ number_format($bill->amount, 2) }} PLN</span>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Platnik: <span class="font-bold text-indigo-600 dark:text-indigo-300">{{ $bill->payer->name }}</span></p>
                                        </div>
                                        <form action="{{ route('bills.destroy', [$group, $bill]) }}" method="POST" onsubmit="return confirm('Usunac rachunek?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-xl bg-red-50 px-4 py-2 text-sm font-bold text-red-700 transition hover:bg-red-100 dark:bg-red-950 dark:text-red-200 dark:hover:bg-red-900">Usun</button>
                                        </form>
                                    </div>

                                    @if($bill->splits->isNotEmpty())
                                        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <p class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Podzial kosztu</p>
                                                    <p class="mt-2 font-mono text-sm text-slate-800 dark:text-slate-200">
                                                        {{ number_format($bill->amount, 2) }} PLN / {{ $splitCount }} osob = ok. {{ number_format($averageSplit, 2) }} PLN na osobe
                                                    </p>
                                                </div>
                                                <div class="rounded-xl bg-white px-3 py-2 text-xs font-black text-slate-600 shadow-sm dark:bg-gray-900 dark:text-slate-300">
                                                    Suma udzialow: {{ number_format($totalSplit, 2) }} PLN
                                                </div>
                                            </div>
                                            <p class="mt-3 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                                Jesli kwota nie dzieli sie rowno, pojedyncze grosze sa rozdzielane tak, zeby suma udzialow byla dokladnie taka jak kwota rachunku.
                                            </p>
                                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                                @foreach($bill->splits as $split)
                                                    <div class="rounded-xl bg-white p-3 shadow-sm dark:bg-gray-900">
                                                        <div class="flex items-center justify-between gap-3">
                                                            <span class="font-bold text-gray-800 dark:text-gray-100">{{ $split->user->name }}</span>
                                                            <span class="font-black text-gray-950 dark:text-white">{{ number_format($split->amount, 2) }} PLN</span>
                                                        </div>
                                                        <p class="mt-1 text-xs {{ $split->is_paid ? 'text-green-600 dark:text-green-300' : 'text-gray-500 dark:text-gray-400' }}">
                                                            {{ $split->is_paid ? 'Platnik rachunku - ta kwota jest juz pokryta.' : 'Udzial tej osoby w koszcie rachunku.' }}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($bill->items->isNotEmpty())
                                        <div class="mt-5 rounded-2xl border border-indigo-100 p-4 dark:border-indigo-900">
                                            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                                                <div>
                                                    <p class="text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-300">Pozycje z paragonu</p>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pozycje sa opisem paragonu; saldo rachunku liczy sie z podzialu kosztu powyzej.</p>
                                                </div>
                                            </div>
                                            <div class="mt-4 space-y-3">
                                                @foreach($bill->items as $item)
                                                    @php
                                                        $lineTotal = (float) $item->price * (int) $item->quantity;
                                                        $assignedCount = $item->users->count();
                                                        $lineShare = $assignedCount > 0 ? $lineTotal / $assignedCount : 0;
                                                    @endphp
                                                    <div class="rounded-xl bg-indigo-50 p-4 text-sm text-gray-700 dark:bg-gray-950 dark:text-gray-300">
                                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                            <div>
                                                                <p class="font-black text-gray-900 dark:text-gray-100">{{ $item->name }}</p>
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ number_format($item->price, 2) }} zl x {{ $item->quantity }} = {{ number_format($lineTotal, 2) }} zl
                                                                </p>
                                                            </div>
                                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-indigo-700 dark:bg-gray-900 dark:text-indigo-300">
                                                                {{ $assignedCount > 0 ? number_format($lineShare, 2).' zl/os.' : 'brak przypisania' }}
                                                            </span>
                                                        </div>
                                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Przypisane: {{ $item->users->pluck('name')->join(', ') ?: 'brak' }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @php
                                        $isOldBillItem = (int) old('bill_item_bill_id') === $bill->id;
                                    @endphp
                                    <details class="mt-5 rounded-2xl border border-gray-200 p-4 dark:border-gray-800" @if($isOldBillItem) open @endif>
                                        <summary class="cursor-pointer text-sm font-black text-indigo-700 dark:text-indigo-300">Dodaj pozycje z paragonu</summary>
                                        <form action="{{ route('bill-items.store', [$group, $bill]) }}" method="POST" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                                            @csrf
                                            <input type="hidden" name="bill_item_bill_id" value="{{ $bill->id }}">
                                            <div>
                                                <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Nazwa pozycji</label>
                                                <input type="text" name="name" value="{{ $isOldBillItem ? old('name') : '' }}" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                                @if($isOldBillItem)
                                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                @endif
                                            </div>
                                            <div>
                                                <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Cena</label>
                                                <input type="number" step="0.01" min="0.01" name="price" value="{{ $isOldBillItem ? old('price') : '' }}" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
                                                @if($isOldBillItem)
                                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                                @endif
                                            </div>
                                            <div>
                                                <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Liczba sztuk</label>
                                                <input type="number" name="quantity" value="{{ $isOldBillItem ? old('quantity', 1) : 1 }}" min="1" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100" required>
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
                                            <button class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-black text-white transition hover:bg-indigo-700 md:col-span-2 dark:bg-indigo-500 dark:text-gray-950 dark:hover:bg-indigo-400">Dodaj pozycje</button>
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
