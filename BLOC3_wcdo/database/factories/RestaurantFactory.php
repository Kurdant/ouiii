<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        return [
            'nom'         => 'Wacdo '.$this->faker->unique()->city(),
            'adresse'     => $this->faker->streetAddress(),
            'code_postal' => (string) $this->faker->numberBetween(10000, 99999),
            'ville'       => $this->faker->city(),
        ];
    }
}
