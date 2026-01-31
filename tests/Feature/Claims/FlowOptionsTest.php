<?php

namespace Tests\Feature\Claims;

use App\Models\User;
use App\Modules\Claims\Models\ClaimEntity;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlowOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_dynamic_options_based_on_entity_type()
    {
        // 1. Create a user (assuming User factory exists, otherwise verify)
        // If User factory doesn't exist, I'll create one manually.
        // Let's assume standard Laravel User factory usage.
        $user = User::first();
        if (!$user) {
            $user = new User();
            $user->name = 'Test User';
            $user->email = 'test@example.com';
            $user->password = bcrypt('password');
            $user->save();
        }

        // 2. Create entities
        $insuranceA = ClaimEntity::create(['name' => 'Insurance A', 'type' => 'insurance']);
        $insuranceB = ClaimEntity::create(['name' => 'Insurance B', 'type' => 'insurance']);
        // Create one that should NOT appear
        $ministryX = ClaimEntity::create(['name' => 'Ministry X', 'type' => 'ministry']);

        // 3. Act: Login and visit the page with session type 'insurance'
        $response = $this->actingAs($user)
            ->withSession(['flow_entity_type' => 'insurance'])
            ->get(route('flow.options'));

        // 4. Assert
        $response->assertStatus(200);

        // These should be visible
        $response->assertSee($insuranceA->name);
        $response->assertSee($insuranceB->name);

        // This should NOT be visible (because we filtered by insurance)
        // NOTE: Currently the controller logic isn't there, so if I were TDDing strictly I'd run this and fail.
        // I will implement the controller logic next.
        $response->assertDontSee($ministryX->name);
    }
}
