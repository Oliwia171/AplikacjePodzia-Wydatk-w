<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'in:user,admin'],
        ]);

        $users = User::query()
            ->withCount('groups')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, function ($query, string $role) {
                $query->where('role', $role);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'groups' => Group::count(),
            'bills' => Bill::count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:user,admin'],
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Podaj imie uzytkownika.',
            'role.required' => 'Wybierz role uzytkownika.',
            'role.in' => 'Dostepne sa tylko dwa typy kont: user oraz admin.',
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Nie mozesz odebrac sobie dostepu administratora.');
        }

        $user->update([
            'name' => $validated['name'],
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Profil uzytkownika zaktualizowany.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie mozesz usunac wlasnego konta.');
        }

        $user->delete();

        return back()->with('success', 'Uzytkownik usuniety.');
    }
}
