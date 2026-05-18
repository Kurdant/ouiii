<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Restaurant.
 *
 * @property int $id
 * @property string $nom
 * @property string $adresse
 * @property string $code_postal
 * @property string $ville
 */
class Restaurant extends Model
{
    use HasFactory;

    protected $table = 'restaurants';

    protected $fillable = [
        'nom',
        'adresse',
        'code_postal',
        'ville',
    ];

    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }

    /**
     * CDC 7.9 : recherche d'un restaurant sur nom, code postal ou ville.
     *
     * @param  array{nom?:?string,code_postal?:?string,ville?:?string}  $filters
     */
    public function scopeRechercher(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['nom'] ?? null, fn ($q, $v) => $q->where('nom', 'ilike', '%'.$v.'%'))
            ->when($filters['code_postal'] ?? null, fn ($q, $v) => $q->where('code_postal', 'ilike', $v.'%'))
            ->when($filters['ville'] ?? null, fn ($q, $v) => $q->where('ville', 'ilike', '%'.$v.'%'));
    }
}
