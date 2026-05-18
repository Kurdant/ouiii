# Wacdo - Projet Bloc 3

Application Laravel 11 de gestion des collaborateurs, restaurants, fonctions et affectations.

## Stack technique

- **PHP 8.3** / **Laravel 11**
- **PostgreSQL 16** (avec `NULLS NOT DISTINCT` et CHECK constraints)
- **Nginx 1.27** + **PHP-FPM**
- **Docker Compose** (3 services : `app`, `web`, `db`)
- **PHPUnit 11** pour les tests

## Lancement en local (développement)

Pré-requis : Docker et Docker Compose installés.

```bash
# 1. Préparer l'environnement
cp .env.example .env

# 2. Construire et démarrer les conteneurs
docker compose up -d --build
```

Au premier démarrage, l'entrypoint du conteneur `app` exécute automatiquement :

- `composer install`
- copie de `.env.example` vers `.env` si absent
- `php artisan key:generate` si `APP_KEY` est vide
- `php artisan migrate --force`
- `php artisan db:seed --force` (admin + démo)

L'application est ensuite accessible sur : <http://localhost:8080>

### Connexion administrateur de démonstration

- Email : `admin@wacdo.local`
- Mot de passe : `AdminWacdo2026!`

(Défini par `AdminCollaborateurSeeder`. À changer impérativement en production.)

## Lancer les tests

```bash
# 1. Créer la base de test
docker compose exec db psql -U wacdo -d postgres -c "CREATE DATABASE wacdo_test;"

# 2. Lancer la suite
docker compose exec app php artisan test
```

La configuration `phpunit.xml` force `DB_DATABASE=wacdo_test` et `BCRYPT_ROUNDS=4` pour des tests rapides. `RefreshDatabase` rejoue toutes les migrations à chaque test, donc les CHECK constraints et l'index unique `NULLS NOT DISTINCT` sont réellement éprouvés.

## Déploiement en production

1. Cloner le dépôt sur le serveur cible.
2. Copier `.env.example` vers `.env` et adapter :
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://<domaine-production>`
   - `DB_PASSWORD=<mot-de-passe-fort>`
   - `SESSION_ENCRYPT=true` (recommandé)
3. Construire et démarrer :
   ```bash
   docker compose -f docker-compose.yml up -d --build
   ```
4. L'entrypoint applique automatiquement les migrations et seeders.
5. Vérifier les logs : `docker compose logs -f app web`.

Pour relancer manuellement les migrations seules :

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=AdminCollaborateurSeeder --force
```

## Commandes utiles

```bash
# Entrer dans le conteneur PHP
docker compose exec app sh

# Lancer Artisan
docker compose exec app php artisan <commande>

# Vider les caches après modification config
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Accéder à PostgreSQL
docker compose exec db psql -U wacdo -d wacdo

# Lancer Pint (formatage)
docker compose exec app ./vendor/bin/pint
```

## Architecture applicative

| Couche | Rôle |
|---|---|
| `routes/web.php` | Routes regroupées par middleware (`guest`, `auth`, `auth + admin`). |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Refuse les non-administrateurs (403) et invalide la session. |
| `app/Http/Controllers/` | 5 contrôleurs : Auth, Fonction, Restaurant, Collaborateur, Affectation. |
| `app/Http/Requests/` | 9 FormRequests pour la validation et l'anti mass-assignment. |
| `app/Services/AffectationService.php` | Centralise les règles métier des affectations (doublon strict retraduit en erreur de validation). |
| `app/Models/` | 4 modèles Eloquent avec scopes métier (`enCours`, `terminees`, `futures`, `filtrer`, `nonAffectes`, `ordonnerParIntitule`, `rechercher`). |
| `resources/views/` | Layout unique + dashboard + 4 dossiers de vues (fonctions, restaurants, collaborateurs, affectations). |
| `database/migrations/` | 6 migrations : référentiels, table métier `affectations`, sessions, cache. |
| `database/factories/` | Factories pour tous les modèles. |
| `database/seeders/` | `AdminCollaborateurSeeder` + `DemoSeeder` (idempotent). |
| `tests/Feature/` | 8 fichiers de tests Feature couvrant Auth, accès admin, validations, règles d'affectation, recherches, CSRF. |

## Choix techniques structurants

- **Aucune table `users` séparée** : `Collaborateur` étend `Authenticatable`, provider `collaborateurs`. Cohérent avec le CDC (un seul référentiel humain).
- **Aucun statut d'affectation stocké** : déduit des dates via scopes Eloquent. La règle est unique et non duplicable.
- **Doublon strict bloqué en BDD** : index unique PostgreSQL avec `NULLS NOT DISTINCT` (CDC). Le `AffectationService` capture la `QueryException` SQLSTATE 23505 et la retraduit en erreur de formulaire lisible.
- **Aucune suppression** : pas de route `destroy` (préservation de l'historique).
- **OWASP** : CSRF natif, throttle `5,1` sur login POST, message d'erreur générique anti-énumération, password en cast `hashed`, session `regenerate` post-login, `invalidate` + `regenerateToken` sur logout.

## Documents projet

- [bloc3_referentiel.md](bloc3_referentiel.md) — référentiel jury RNCP
- [CDC_fonctionnel_bloc3.md](CDC_fonctionnel_bloc3.md) — CDC fonctionnel
- [CDC_technique_bloc3.md](CDC_technique_bloc3.md) — CDC technique (source de vérité)
- [Sprints.md](Sprints.md) — plan de sprints et workflow de validation
- [SCHEMA_BDD_bloc3_wacdo.drawio](SCHEMA_BDD_bloc3_wacdo.drawio) — schéma BDD
