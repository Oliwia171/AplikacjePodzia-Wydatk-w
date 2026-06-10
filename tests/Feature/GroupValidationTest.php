<?php

namespace Tests\Feature;

use App\Models\BillSplit;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_name_must_be_unique(): void
    {
        $user = User::factory()->create();

        Group::create([
            'name' => 'Wakacje',
            'description' => 'Pierwsza grupa',
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('groups.store'), [
                'name' => 'Wakacje',
                'description' => 'Duplikat',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_group_description_is_saved(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('groups.store'), [
                'name' => 'Mieszkanie',
                'description' => 'Wspolne oplaty domowe',
            ])
            ->assertRedirect(route('groups.index'));

        $this->assertDatabaseHas('groups', [
            'name' => 'Mieszkanie',
            'description' => 'Wspolne oplaty domowe',
            'owner_id' => $user->id,
        ]);
    }

    public function test_expense_requires_name_and_positive_amount(): void
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Wyjazd',
            'owner_id' => $user->id,
        ]);
        $group->users()->attach($user->id);

        $this->actingAs($user)
            ->from(route('groups.show', $group))
            ->post(route('bills.store', $group), [
                'description' => '',
                'amount' => -10,
                'payer_id' => $user->id,
            ])
            ->assertRedirect(route('groups.show', $group))
            ->assertSessionHasErrors(['description', 'amount']);

        $this->assertDatabaseCount('bills', 0);
    }

    public function test_group_page_explains_how_balances_are_calculated(): void
    {
        $payer = User::factory()->create(['name' => 'Ania']);
        $member = User::factory()->create(['name' => 'Bartek']);
        $group = Group::create([
            'name' => 'Projekt',
            'owner_id' => $payer->id,
        ]);
        $group->users()->attach([$payer->id, $member->id]);

        $this->actingAs($payer)
            ->post(route('bills.store', $group), [
                'description' => 'Kolacja',
                'amount' => 100,
                'payer_id' => $payer->id,
            ]);

        $this->actingAs($payer)
            ->get(route('groups.show', $group))
            ->assertOk()
            ->assertSeeText('Jak to sie liczy')
            ->assertSeeText('Saldo = zaplacone - naleznosc')
            ->assertSeeText('100.00 PLN / 2 osob = ok. 50.00 PLN na osobe')
            ->assertSeeText('Suma udzialow: 100.00 PLN');
    }

    public function test_expense_split_keeps_every_cent_when_amount_is_not_evenly_divisible(): void
    {
        $payer = User::factory()->create();
        $secondMember = User::factory()->create();
        $thirdMember = User::factory()->create();
        $group = Group::create([
            'name' => 'Grosze',
            'owner_id' => $payer->id,
        ]);
        $group->users()->attach([$payer->id, $secondMember->id, $thirdMember->id]);

        $this->actingAs($payer)
            ->post(route('bills.store', $group), [
                'description' => 'Zakupy',
                'amount' => 10,
                'payer_id' => $payer->id,
            ])
            ->assertRedirect();

        $amounts = BillSplit::query()
            ->orderBy('user_id')
            ->pluck('amount')
            ->map(fn ($amount) => number_format((float) $amount, 2, '.', ''))
            ->all();

        $this->assertSame(['3.34', '3.33', '3.33'], $amounts);
        $this->assertSame('10.00', number_format((float) BillSplit::sum('amount'), 2, '.', ''));
    }
}
