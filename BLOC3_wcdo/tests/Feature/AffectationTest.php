<?php

namespace Tests\Feature;

use App\Models\Affectation;
use App\Models\Collaborateur;
use App\Models\Fonction;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffectationTest extends TestCase
{
    use RefreshDatabase;

    protected Collaborateur $admin;
    protected Collaborateur $collab;
    protected Restaurant $resto;
    protected Fonction $fonction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin    = Collaborateur::factory()->admin()->create();
        $this->collab   = Collaborateur::factory()->create();
        $this->resto    = Restaurant::factory()->create();
        $this->fonction = Fonction::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'collaborateur_id' => $this->collab->id,
            'restaurant_id'    => $this->resto->id,
            'fonction_id'      => $this->fonction->id,
            'date_debut'       => '2024-06-01',
            'date_fin'         => null,
        ], $overrides);
    }

    public function test_valid_affectation_is_created(): void
    {
        $this->actingAs($this->admin)
            ->post('/affectations', $this->payload())
            ->assertRedirect();

        $this->assertDatabaseHas('affectations', [
            'collaborateur_id' => $this->collab->id,
            'restaurant_id'    => $this->resto->id,
            'fonction_id'      => $this->fonction->id,
        ]);
    }

    public function test_missing_required_fields_are_rejected(): void
    {
        $this->actingAs($this->admin)
            ->from('/affectations/create')
            ->post('/affectations', ['date_debut' => '2024-06-01'])
            ->assertSessionHasErrors(['collaborateur_id', 'restaurant_id', 'fonction_id']);
    }

    public function test_date_fin_before_date_debut_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->from('/affectations/create')
            ->post('/affectations', $this->payload([
                'date_debut' => '2024-06-01',
                'date_fin'   => '2024-05-01',
            ]))
            ->assertSessionHasErrors('date_fin');
    }

    public function test_strict_duplicate_is_rejected(): void
    {
        // Premi\u00e8re cr\u00e9ation OK.
        $this->actingAs($this->admin)
            ->post('/affectations', $this->payload(['date_debut' => '2024-06-01']));

        // Doublon strict : refus\u00e9 via index unique BDD + service.
        $this->actingAs($this->admin)
            ->from('/affectations/create')
            ->post('/affectations', $this->payload(['date_debut' => '2024-06-01']))
            ->assertSessionHasErrors('date_debut');

        $this->assertSame(1, Affectation::count());
    }

    public function test_no_status_column_in_database(): void
    {
        $a = Affectation::factory()->enCours()->create();
        $this->assertArrayNotHasKey('statut', $a->getAttributes());
    }

    public function test_scope_en_cours_filters_correctly(): void
    {
        Affectation::factory()->enCours()->count(2)->create();
        Affectation::factory()->terminee()->create();
        Affectation::factory()->future()->create();

        $this->assertSame(2, Affectation::enCours()->count());
        $this->assertSame(1, Affectation::terminees()->count());
        $this->assertSame(1, Affectation::futures()->count());
    }

    public function test_affectation_en_cours_is_modifiable(): void
    {
        $a = Affectation::factory()->enCours()->create([
            'collaborateur_id' => $this->collab->id,
            'restaurant_id'    => $this->resto->id,
            'fonction_id'      => $this->fonction->id,
        ]);

        $newFonction = Fonction::factory()->create();

        $this->actingAs($this->admin)
            ->put('/affectations/'.$a->id, [
                'collaborateur_id' => $this->collab->id,
                'restaurant_id'    => $this->resto->id,
                'fonction_id'      => $newFonction->id,
                'date_debut'       => $a->date_debut->format('Y-m-d'),
                'date_fin'         => null,
            ])
            ->assertRedirect();

        $this->assertSame($newFonction->id, $a->fresh()->fonction_id);
    }
}
