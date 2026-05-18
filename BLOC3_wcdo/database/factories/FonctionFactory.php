<?php

namespace Database\Factories;

use App\Models\Fonction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fonction>
 */
class FonctionFactory extends Factory
{
    protected $model = Fonction::class;

    public function definition(): array
    {
        return [
            'intitule_poste' => $this->faker->unique()->jobTitle(),
        ];
    }
}
