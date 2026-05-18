<?php

namespace App\Services;

use App\Models\Affectation;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

/**
 * Centralise les règles métier des affectations.
 *
 * CDC : le doublon strict (mêmes collaborateur, restaurant, fonction,
 * date_debut, date_fin) est rejeté par l'index unique PostgreSQL
 * `affectations_doublon_strict_unique` (NULLS NOT DISTINCT). Le service
 * capture la `QueryException` SQLSTATE 23505 et la convertit en
 * `ValidationException` lisible côté formulaire.
 *
 * La cohérence date_fin >= date_debut est vérifiée à la fois côté FormRequest
 * (`after_or_equal`) et côté BDD (CHECK constraint, défense en profondeur).
 */
class AffectationService
{
    /** Code SQLSTATE PostgreSQL pour violation d'unicité. */
    private const UNIQUE_VIOLATION = '23505';

    /**
     * Crée une affectation et traduit l'éventuel doublon strict en erreur
     * de validation.
     *
     * @param  array{collaborateur_id:int,restaurant_id:int,fonction_id:int,date_debut:string,date_fin:?string}  $data
     */
    public function create(array $data): Affectation
    {
        try {
            return Affectation::create($data);
        } catch (QueryException $e) {
            $this->rethrowIfDoublonStrict($e);
            throw $e;
        }
    }

    /**
     * Met à jour une affectation existante et gère le doublon strict.
     *
     * @param  array{collaborateur_id:int,restaurant_id:int,fonction_id:int,date_debut:string,date_fin:?string}  $data
     */
    public function update(Affectation $affectation, array $data): Affectation
    {
        try {
            $affectation->update($data);

            return $affectation->fresh();
        } catch (QueryException $e) {
            $this->rethrowIfDoublonStrict($e);
            throw $e;
        }
    }

    private function rethrowIfDoublonStrict(QueryException $e): void
    {
        if ($e->getCode() === self::UNIQUE_VIOLATION) {
            throw ValidationException::withMessages([
                'date_debut' => 'Cette affectation existe déjà (même collaborateur, restaurant, fonction et dates).',
            ]);
        }
    }
}
