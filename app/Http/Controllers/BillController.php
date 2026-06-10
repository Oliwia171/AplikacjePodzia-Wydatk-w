<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BillController extends Controller
{
    public function store(Request $request, Group $group)
    {
        $this->authorizeGroupAccess($group);

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payer_id' => [
                'required',
                Rule::exists('group_user', 'user_id')->where('group_id', $group->id),
            ],
        ], [
            'description.required' => 'Podaj nazwe wydatku. Bez nazwy wydatek nie zostanie zapisany.',
            'amount.required' => 'Podaj kwote wydatku.',
            'amount.numeric' => 'Kwota musi byc liczba.',
            'amount.min' => 'Kwota musi byc dodatnia. Nie mozna wpisac kwoty ujemnej ani zera.',
            'payer_id.required' => 'Wybierz platnika.',
            'payer_id.exists' => 'Platnik musi byc czlonkiem tej grupy.',
        ]);

        $bill = $group->bills()->create([
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'payer_id' => $validated['payer_id'],
            'date' => now(),
        ]);

        $this->createEqualSplits($bill, $group, (int) $validated['payer_id']);

        if (DB::getDriverName() !== 'mysql') {
            $group->increment('total_amount', $validated['amount']);
        }

        return back()->with('success', 'Wydatek dodany!');
    }

    public function destroy(Group $group, Bill $bill)
    {
        $this->authorizeGroupAccess($group);
        abort_unless($bill->group_id === $group->id, 404);

        $amount = $bill->amount;
        $bill->delete();

        if (DB::getDriverName() !== 'mysql') {
            $group->decrement('total_amount', $amount);
        }

        return back()->with('success', 'Rachunek usuniety.');
    }

    private function createEqualSplits(Bill $bill, Group $group, int $payerId): void
    {
        $members = $group->users;
        if ($members->isEmpty()) {
            return;
        }

        $share = round($bill->amount / $members->count(), 2);

        foreach ($members as $member) {
            $bill->splits()->create([
                'user_id' => $member->id,
                'amount' => $share,
                'is_paid' => $member->id === $payerId,
            ]);
        }
    }

    private function authorizeGroupAccess(Group $group): void
    {
        if (!auth()->user()->isAdmin() && !$group->users->contains(Auth::id())) {
            abort(403, 'Brak dostepu.');
        }
    }
}
