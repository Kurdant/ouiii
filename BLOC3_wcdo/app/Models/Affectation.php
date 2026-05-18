<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modèle Affectation.
 *
 * CDC : aucun statut d'affectation n'est stocké. L'état (en cours, future,
 * terminée) est déduit des dates via les scopes ci-dessous.
 *
 * @property int $id
 * @property int $collaborateur_id
 * @property int $restaurant_id
 * @property int $fonction_id
 * @property \Illuminate\Support\Carbon $date_debut
 * @property \Illuminate\Support\Carbon|null $date_fin
 */
class Affectation extends Model
{
    use HasFactory;

    protected $table = 'affectations';

    protected $fillable = [
        'collaborateur_id',
        'restaurant_id',
        'fonction_id',
        'date_debut',
        'date_fin',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin'   => 'date',
        ];
    }

    public function collaborateur(): BelongsTo
    {
        return $this->belongsTo(Collaborateur::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function fonction(): BelongsTo
    {
        return $this->belongsTo(Fonction::class);
    }

    /**
     * CDC 3.1 : date_debut <= today AND (date_fin IS NULL OR date_fin >= today).
     */
    public function scopeEnCours(Builder $query, ?Carbon $reference = null): Builder
    {
        $today = ($reference ?? now())->toDateString();

        return $query
            ->whereDate('date_debut', '<=', $today)
            ->where(function ($sub) use ($today) {
                $sub->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>=', $today);
            });
    }

    /** Affectation planifiée mais pas encore active. */
    public function scopeFutures(Builder $query, ?Carbon $reference = null): Builder
    {
        $today = ($reference ?? now())->toDateString();

        return $query->whereDate('date_debut', '>', $today);
    }

    /** Affectation appartenant à l'historique. */
    public function scopeTerminees(Builder $query, ?Carbon $reference = null): Builder
    {
        $today = ($reference ?? now())->toDateString();

        return $query
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<', $today);
    }

    /**
     * Filtre transversal pour la recherche d'affectations (Sprint 5).
     *
     * @param  array<string,mixed>  $filters
     */
    public function scopeFiltrer(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['collaborateur_id'] ?? null, fn ($q, $v) => $q->where('collaborateur_id', $v))
            ->when($filters['restaurant_id'] ?? null, fn ($q, $v) => $q->where('restaurant_id', $v))
            ->when($filters['fonction_id'] ?? null, fn ($q, $v) => $q->where('fonction_id', $v))
            ->when($filters['date_debut'] ?? null, fn ($q, $v) => $q->whereDate('date_debut', '>=', $v))
            ->when($filters['date_fin'] ?? null, fn ($q, $v) => $q->whereDate('date_debut', '<=', $v))
            ->when(($filters['nom'] ?? null), function ($q, $v) {
                $like = '%'.$v.'%';
                $q->whereHas('collaborateur', function ($sub) use ($like) {
                    $sub->where('nom', 'ilike', $like)
                        ->orWhere('prenom', 'ilike', $like);
                });
            })
            ->when(($filters['ville'] ?? null), function ($q, $v) {
                $like = '%'.$v.'%';
                $q->whereHas('restaurant', fn ($sub) => $sub->where('ville', 'ilike', $like));
            });
    }
}
