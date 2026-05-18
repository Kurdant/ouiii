<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Modèle Collaborateur.
 *
 * CDC : aucune entité Utilisateur séparée. Le collaborateur porte lui-même
 * l'identité de connexion (`email`, `password`, `administrateur`). Le modèle
 * étend Authenticatable dès Sprint 1 ; la logique de login est ajoutée en
 * Sprint 2.
 *
 * @property int $id
 * @property string $nom
 * @property string $prenom
 * @property string $email
 * @property string|null $telephone
 * @property \Illuminate\Support\Carbon $date_premiere_embauche
 * @property bool $administrateur
 * @property string|null $password
 */
class Collaborateur extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'collaborateurs';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'date_premiere_embauche',
        'administrateur',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_premiere_embauche' => 'date',
            'administrateur'         => 'boolean',
            'password'               => 'hashed',
        ];
    }

    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }

    /**
     * CDC : un collaborateur non affecté n'a aucune affectation en cours
     * à la date de référence (today par défaut).
     */
    public function scopeNonAffectes(Builder $query, ?Carbon $reference = null): Builder
    {
        $today = ($reference ?? now())->toDateString();

        return $query->whereDoesntHave('affectations', function ($sub) use ($today) {
            $sub->whereDate('date_debut', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->whereNull('date_fin')
                        ->orWhereDate('date_fin', '>=', $today);
                });
        });
    }
}
