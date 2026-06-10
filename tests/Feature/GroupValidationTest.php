<?php

namespace Tests\Feature;

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
}
