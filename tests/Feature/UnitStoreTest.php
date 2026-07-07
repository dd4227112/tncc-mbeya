<?php

namespace Tests\Feature;

use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_unit_can_be_created_via_store_endpoint(): void
    {
        $response = $this->actingAs(
            \App\Models\User::factory()->create()
        )->postJson(route('units.store'), [
            'name' => 'Kilogram',
            'abbreviation' => 'kg',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Kilogram')
            ->assertJsonPath('data.abbreviation', 'kg');

        $this->assertDatabaseHas('units', [
            'name' => 'Kilogram',
            'abbreviation' => 'kg',
        ]);
    }
}
