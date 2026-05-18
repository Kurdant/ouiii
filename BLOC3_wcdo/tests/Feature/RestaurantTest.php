<?php

namespace Tests\Feature;

use App\Models\Collaborateur;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected Collaborateur $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Collaborateur::factory()->admin()->create();
    }

    public function test_creation_requires_all_mandatory_fields(): void
    {
        $this->actingAs($this->admin)
            ->from('/restaurants/create')
            ->post('/restaurants', [])
            ->assertSessionHasErrors(['nom', 'adresse', 'code_postal', 'ville']);
    }

    public function test_creation_succeeds_with_valid_data(): void
    {
        $this->actingAs($this->admin)
            ->post('/restaurants', [
                'nom'         => 'Wacdo Lyon',
                'adresse'     => '12 rue de la R\u00e9publique',
                'code_postal' => '69002',
                'ville'       => 'Lyon',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('restaurants', ['nom' => 'Wacdo Lyon']);
    }

    public function test_search_by_ville_uses_ilike(): void
    {
        Restaurant::factory()->create(['ville' => 'Marseille']);
        Restaurant::factory()->create(['ville' => 'Toulouse']);

        $response = $this->actingAs($this->admin)
            ->get('/restaurants?ville=mars');

        $response->assertOk();
        $response->assertSee('Marseille');
        $response->assertDontSee('Toulouse');
    }
}
