<?php

namespace Database\Factories;

use App\Models\Collaborateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collaborateur>
 */
class CollaborateurFactory extends Factory
{
    protected $model = Collaborateur::class;

    public function definition(): array
    {
        return [
            'nom'                    => $this->faker->lastName(),
            'prenom'                 => $this->faker->firstName(),
            'email'                  => $this->faker->unique()->safeEmail(),
            'telephone'              => null,
            'date_premiere_embauche' => $this->faker->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
            'administrateur'         => false,
            'password'               => null,
            'remember_token'         => null,
        ];
    }

    public function admin(): self
    {
        return $this->state(fn () => [
            'administrateur' => true,
            'password'       => 'MotDePasseTestAdmin1!',
        ]);
    }
}
