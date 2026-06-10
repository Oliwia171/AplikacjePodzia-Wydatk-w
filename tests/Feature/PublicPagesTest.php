<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_visible_without_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Proste rozliczanie wspolnych wydatkow');
    }

    public function test_dashboard_is_visible_without_login(): void
    {
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Rozliczaj wspolne wydatki');
    }

    public function test_regular_user_does_not_see_admin_link(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Panel admina');
    }

    public function test_admin_sees_admin_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Panel admina');
    }
}
