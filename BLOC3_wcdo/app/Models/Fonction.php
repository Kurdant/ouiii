<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Fonction.
 *
 * @property int $id
 * @property string $intitule_poste
 */
class Fonction extends Model
{
    use HasFactory;

    protected $table = 'fonctions';

    protected $fillable = [
        'intitule_poste',
    ];

    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }

    /** CDC 7.9 : tri alphabétique standard des fonctions. */
    public function scopeOrdonnerParIntitule(Builder $query): Builder
    {
        return $query->orderBy('intitule_poste');
    }
}
