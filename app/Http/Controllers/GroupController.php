<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'owner' => ['nullable', 'string', 'max:255'],
        ]);

        $query = auth()->user()->isAdmin()
            ? Group::query()
            : Auth::user()->groups();

        $groups = $query
            ->with('owner')
            ->withCount(['users', 'bills'])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(($filters['owner'] ?? null) && auth()->user()->isAdmin(), function ($query) use ($filters) {
                $query->whereHas('owner', function ($query) use ($filters) {
                    $query->where('name', 'like', '%'.$filters['owner'].'%')
                        ->orWhere('email', 'like', '%'.$filters['owner'].'%');
                });
            })
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        return view('groups.index', [
            'groups' => $groups,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], $this->groupValidationMessages());

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => Auth::id(),
        ]);

        $group->users()->syncWithoutDetaching([Auth::id()]);

        return redirect()->route('groups.index')->with('success', 'Grupa utworzona!');
    }

    public function show(Request $request, Group $group)
    {
        $this->authorizeGroupAccess($group);

        $filters = $request->validate([
            'bill_search' => ['nullable', 'string', 'max:255'],
            'payer_id' => ['nullable', 'integer'],
            'amount_from' => ['nullable', 'numeric', 'min:0'],
            'amount_to' => ['nullable', 'numeric', 'min:0'],
        ]);

        $group->load(['owner', 'users', 'bills.splits.user']);

        $bills = $group->bills()
            ->with(['payer', 'items.users', 'splits.user'])
            ->when($filters['bill_search'] ?? null, function ($query, string $search) {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when($filters['payer_id'] ?? null, function ($query, int $payerId) {
                $query->where('payer_id', $payerId);
            })
            ->when($filters['amount_from'] ?? null, function ($query, $amountFrom) {
                $query->where('amount', '>=', $amountFrom);
            })
            ->when($filters['amount_to'] ?? null, function ($query, $amountTo) {
                $query->where('amount', '<=', $amountTo);
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return view('groups.show', [
            'group' => $group,
            'bills' => $bills,
            'filters' => $filters,
        ]);
    }

    public function edit(Group $group)
    {
        $this->authorizeGroupOwnerOrAdmin($group);

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorizeGroupOwnerOrAdmin($group);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('groups', 'name')->ignore($group->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ], $this->groupValidationMessages());

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('groups.show', $group)->with('success', 'Grupa zaktualizowana.');
    }

    public function addUser(Request $request, Group $group)
    {
        $this->authorizeGroupAccess($group);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Podaj adres e-mail uzytkownika.',
            'email.email' => 'Podaj poprawny adres e-mail.',
            'email.exists' => 'Nie znaleziono uzytkownika o takim adresie e-mail.',
        ]);

        $userToAdd = User::where('email', $validated['email'])->first();

        if ($group->users->contains($userToAdd->id)) {
            return back()->withInput()->with('error', 'Ten uzytkownik juz jest w grupie!');
        }

        $group->users()->syncWithoutDetaching([$userToAdd->id]);

        return back()->with('success', 'Dodano uzytkownika: ' . $userToAdd->name);
    }

    public function destroy(Group $group)
    {
        $this->authorizeGroupOwnerOrAdmin($group);
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Grupa usunieta.');
    }

    private function authorizeGroupAccess(Group $group): void
    {
        if (!auth()->user()->isAdmin() && !$group->users->contains(Auth::id())) {
            abort(403, 'Brak dostepu.');
        }
    }

    private function authorizeGroupOwnerOrAdmin(Group $group): void
    {
        if (!auth()->user()->isAdmin() && $group->owner_id !== Auth::id()) {
            abort(403);
        }
    }

    private function groupValidationMessages(): array
    {
        return [
            'name.required' => 'Podaj nazwe grupy.',
            'name.unique' => 'Grupa o takiej nazwie juz istnieje. Nazwy grup nie moga sie powtarzac.',
            'description.max' => 'Opis grupy moze miec maksymalnie 1000 znakow.',
        ];
    }
}
