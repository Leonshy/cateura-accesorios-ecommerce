<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('account.profile'));

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('account.profile.update'), [
                'name'  => 'Test User',
                'phone' => '0981234567',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $user->refresh();
        $this->assertSame('Test User', $user->name);
        $this->assertSame('0981234567', $user->profile->phone);
    }

    public function test_profile_update_requires_a_name(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('account.profile.update'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');
    }
}
