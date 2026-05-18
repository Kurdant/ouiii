<?php

namespace Tests\Feature;

use App\Models\Collaborateur;
use App\Models\Fonction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FonctionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Collaborateur::factory()->admin()->create();
    }

    protected Collaborateur $admin;

    public function test_intitule_poste_is_required(): void
    {
        $this->actingAs($this->admin)
            ->from('/fonctions/create')
            ->post('/fonctions', ['intitule_poste' => ''])
            ->assertSessionHasErrors('intitule_poste');
    }

    public function test_intitule_poste_must_be_unique(): void
    {
        Fonction::factory()->create(['intitule_poste' => 'Manager']);

        $this->actingAs($this->admin)
            ->from('/fonctions/create')
            ->post('/fonctions', ['intitule_poste' => 'Manager'])
            ->assertSessionHasErrors('intitule_poste');
    }

    public function test_fonction_creation_succeeds_with_valid_data(): void
    {
        $this->actingAs($this->admin)
            ->post('/fonctions', ['intitule_poste' => 'Chef de service'])
            ->assertRedirect();

        $this->assertDatabaseHas('fonctions', ['intitule_poste' => 'Chef de service']);
    }

    public function test_no_destroy_route_for_fonctions(): void
    {
        $fonction = Fonction::factory()->create();
        $this->actingAs($this->admin)
            ->delete('/fonctions/'.$fonction->id)
            ->assertStatus(405); // Method Not Allowed
    }
}
