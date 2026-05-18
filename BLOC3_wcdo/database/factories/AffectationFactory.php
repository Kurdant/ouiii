<?php

namespace Database\Factories;

use App\Models\Affectation;
use App\Models\Collaborateur;
use App\Models\Fonction;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Affectation>
 */
class AffectationFactory extends Factory
{
    protected $model = Affectation::class;

    public function definition(): array
    {
        return [
            'collaborateur_id' => Collaborateur::factory(),
            'restaurant_id'    => Restaurant::factory(),
            'fonction_id'      => Fonction::factory(),
            'date_debut'       => now()->subMonth()->format('Y-m-d'),
            'date_fin'         => null,
        ];
    }

    public function enCours(): self
    {
        return $this->state(fn () => [
            'date_debut' => now()->subMonth()->format('Y-m-d'),
            'date_fin'   => null,
        ]);
    }

    public function terminee(): self
    {
        return $this->state(fn () => [
            'date_debut' => now()->subMonths(6)->format('Y-m-d'),
            'date_fin'   => now()->subMonth()->format('Y-m-d'),
        ]);
    }

    public function future(): self
    {
        return $this->state(fn () => [
            'date_debut' => now()->addMonth()->format('Y-m-d'),
            'date_fin'   => null,
        ]);
    }
}
