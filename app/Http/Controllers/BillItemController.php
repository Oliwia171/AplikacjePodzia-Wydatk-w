<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BillItemController extends Controller
{
    public function store(Request $request, Group $group, Bill $bill)
    {
        $this->authorizeGroupAccess($group);
        abort_unless($bill->group_id === $group->id, 404);

        $validated = $request->validate([
            'bill_item_bill_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'quantity' => ['required', 'integer', 'min:1'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => [
                Rule::exists('group_user', 'user_id')->where('group_id', $group->id),
            ],
        ], [
            'name.required' => 'Podaj nazwe pozycji z paragonu.',
            'price.required' => 'Podaj cene pozycji.',
            'price.numeric' => 'Cena musi byc liczba.',
            'price.min' => 'Cena musi byc dodatnia. Nie mozna wpisac kwoty ujemnej ani zera.',
            'quantity.required' => 'Podaj liczbe sztuk.',
            'quantity.integer' => 'Liczba sztuk musi byc liczba calkowita.',
            'quantity.min' => 'Liczba sztuk musi wynosic co najmniej 1.',
            'user_ids.required' => 'Wybierz co najmniej jedna osobe do tej pozycji.',
            'user_ids.*.exists' => 'Pozycje mozna przypisac tylko czlonkom tej grupy.',
        ]);

        $item = $bill->items()->create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
        ]);

        $item->users()->syncWithoutDetaching($validated['user_ids']);

        return back()->with('success', 'Pozycja z paragonu dodana.');
    }

    private function authorizeGroupAccess(Group $group): void
    {
        if (!auth()->user()->isAdmin() && !$group->users->contains(Auth::id())) {
            abort(403, 'Brak dostepu.');
        }
    }
}
