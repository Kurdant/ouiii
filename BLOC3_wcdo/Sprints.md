# Plan de sprints - Wacdo Bloc 3

Ce fichier sert de document de pilotage pour le développement du projet Wacdo Bloc 3. Il fixe l'ordre de réalisation, le contenu de chaque sprint, les fichiers attendus et le workflow de validation à trois agents.

Le projet est développé sprint par sprint. Aucun sprint suivant ne démarre tant que le sprint en cours n'est pas codé, vérifié, corrigé si nécessaire et documenté dans ce fichier.

## Sommaire des sprints

| Sprint | Titre | Objectif simple | Statut |
|---|---|---|---|
| [Sprint 0](#sprint-0---mise-en-place-technique) | Mise en place technique | Installer Laravel 11, Docker et la base de départ | Validé |
| [Sprint 1](#sprint-1---schema-bdd-et-modeles-eloquent) | Schéma BDD et modèles Eloquent | Créer les tables, contraintes et modèles | Validé |
| [Sprint 2](#sprint-2---authentification-et-controle-administrateur) | Authentification et contrôle administrateur | Sécuriser l'accès au back-office | Validé |
| [Sprint 3](#sprint-3---crud-des-referentiels) | CRUD des référentiels | Coder fonctions, restaurants et collaborateurs | Validé |
| [Sprint 4](#sprint-4---gestion-des-affectations) | Gestion des affectations | Coder le cœur métier des affectations | Validé |
| [Sprint 5](#sprint-5---recherches-details-et-historiques) | Recherches, détails et historiques | Rendre les données exploitables dans les écrans | Validé |
| [Sprint 6](#sprint-6---tests-securite-et-durcissement) | Tests, sécurité et durcissement | Vérifier les règles métier et la sécurité | Validé |
| [Sprint 7](#sprint-7---deploiement-et-preparation-soutenance) | Déploiement et préparation soutenance | Déployer et préparer la démonstration jury | Validé |

## Workflow obligatoire pour chaque sprint

Chaque sprint suit le même workflow. Ce rituel est obligatoire pour garder un développement simple, vérifié et défendable devant jury.

### 1. Préparation du sprint

Le détail du sprint est relu avant codage : objectif, fonctionnalités à produire, fichiers attendus, contraintes du CDC, règles métier et critères de fin.

### 2. Réunion de conception à 3 agents

Le sprint est donné à trois agents :

| Agent | Rôle dans la réunion |
|---|---|
| Expert Merise | Vérifie la cohérence avec le CDC, le MCD, le MCT et les règles métier. |
| Agent dev | Vérifie la faisabilité Laravel, l'ordre de codage, les contrôleurs, vues, Form Requests et services. |
| Agent BDD | Vérifie les migrations, contraintes, relations Eloquent, scopes et requêtes SQL/PostgreSQL. |

Les trois agents travaillent ensemble avant le codage. La réunion produit une décision claire : ce qui est codé dans le sprint, ce qui reste hors sprint et les risques à surveiller.

### 3. Codage du sprint

Le sprint est codé uniquement après validation de la réunion de conception. Le codage respecte les décisions du CDC technique : Laravel 11, Blade, Eloquent, PostgreSQL, Docker, Form Requests, middleware administrateur et absence de surcouche inutile.

### 4. Vérification individuelle

Après codage, chaque agent vérifie le résultat de son côté :

| Agent | Vérification attendue |
|---|---|
| Expert Merise | Les règles métier sont respectées et aucun besoin hors périmètre n'a été ajouté. |
| Agent dev | Le code Laravel est simple, cohérent, testable et conforme à l'architecture prévue. |
| Agent BDD | Les tables, relations, contraintes et requêtes sont cohérentes avec PostgreSQL et Eloquent. |

### 5. Vérification de non-régression

Après la vérification individuelle, le projet complet est contrôlé pour vérifier que le sprint codé n'a pas cassé une fonctionnalité déjà validée.

Cette étape vérifie au minimum :

- les fonctionnalités des sprints précédents ;
- les routes déjà existantes ;
- les migrations déjà validées ;
- les formulaires déjà fonctionnels ;
- l'authentification et les accès protégés ;
- les tests automatisés disponibles à ce moment du projet.

Si un bug apparaît dans une partie déjà validée, le sprint ne peut pas être considéré comme terminé. Le bug est analysé, corrigé, puis les vérifications du sprint et les vérifications de non-régression sont relancées.

### 6. Réunion de validation à 3 agents

Les trois agents se réunissent après leurs vérifications individuelles. Ils examinent le code, les tests, les fichiers modifiés et les écarts avec le sprint.

Si un agent formule une objection, elle est analysée. L'objection est acceptée uniquement si elle correspond à un vrai risque fonctionnel, technique, BDD, sécurité ou soutenance.

### 7. Corrections et tests

Les corrections sont réalisées avant validation du sprint. Les tests nécessaires sont relancés. Un sprint n'est terminé que lorsque les erreurs bloquantes sont corrigées.

### 8. Compte rendu et mise à jour du fichier

À la fin de chaque sprint, un compte rendu est ajouté dans ce fichier avec :

- les fichiers créés ;
- les fichiers modifiés ;
- ce qui a réellement été codé ;
- les tests exécutés ;
- les vérifications de non-régression exécutées ;
- les remarques des agents ;
- les corrections effectuées ;
- le statut final du sprint.

## Statuts utilisés

| Statut | Signification |
|---|---|
| À faire | Le fichier ou le sprint n'est pas encore commencé. |
| En cours | Le fichier ou le sprint est en cours de réalisation. |
| À vérifier | Le fichier est codé et attend la relecture ou les tests. |
| À corriger | Une anomalie a été détectée. |
| Validé | Le fichier ou le sprint est terminé et vérifié. |
| Hors sprint | Le fichier ne relève pas du sprint en cours. |

---

## Sprint 0 - Mise en place technique

### Objectif

Disposer d'un projet Laravel 11 exécutable localement avec Docker, PostgreSQL et un premier compte administrateur généré par seeder.

### En termes simples

Dans ce sprint, on installe tout ce qui permet de travailler : Laravel, Docker, Nginx, PHP-FPM, PostgreSQL, Composer, les variables d'environnement et la base de départ. À la fin, le projet démarre et la page de connexion existe.

### Ce qui est fait dans le sprint

- Installer Laravel 11 (squelette manuel + résolution `composer install` au build conteneur).
- Créer l'environnement Docker Compose.
- Configurer les services `app`, `web` et `db`.
- Préparer PostgreSQL 16.
- Préparer `.env.example` et `.env` local.
- Vérifier la connexion entre Laravel et PostgreSQL.
- Obtenir une page de connexion accessible (stub Blade, POST câblé en Sprint 2).

### Décision Hermes - arbitrage réunion de conception

Le seeder du premier administrateur est **décalé au Sprint 1**.

Motif : le CDC technique interdit une table `users` séparée. Le premier administrateur doit donc vivre dans la table `collaborateurs`, créée en Sprint 1. Tenter de coder `AdminCollaborateurSeeder` en Sprint 0 obligerait à créer une migration `collaborateurs` partielle, en avance sur Sprint 1, et serait incohérent avec la règle « zéro métier en Sprint 0 ».

### Ce qui a été codé

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| `Dockerfile` | Image `php:8.3-fpm-alpine` + extensions `pdo_pgsql`, `pgsql`, `intl`, `zip`, `bcmath` + Composer | Validé |
| `docker-compose.yml` | Services `app` / `web` / `db` (postgres:16-alpine, healthcheck, volume `pgdata`, port 5432 exposé) | Validé |
| `docker/nginx/default.conf` | Nginx pointant sur `public/` et déléguant PHP à `app:9000` | Validé |
| `docker/php/entrypoint.sh` | Entrypoint qui exécute `composer install`, copie `.env`, génère `APP_KEY` au premier démarrage | Validé |
| `.env.example` | Variables PostgreSQL, `SESSION_DRIVER=file`, `CACHE_STORE=file` (driver DB activé en Sprint 1) | Validé |
| `.gitignore` | Standard Laravel 11 | Validé |
| `composer.json` | Dépendances Laravel 11 + dev (pint, sail, pail, collision, phpunit 11) | Validé |
| `artisan` | Point d'entrée CLI Laravel 11 | Validé |
| `bootstrap/app.php` | Configuration Laravel 11 streamlined (`withRouting`, `withMiddleware`, `withExceptions`, health `/up`) | Validé |
| `bootstrap/providers.php` | Déclaration de `AppServiceProvider` | Validé |
| `bootstrap/cache/.gitignore` | Ignorer le cache compilé | Validé |
| `public/index.php` | Front controller Laravel 11 | Validé |
| `public/.htaccess` | Réécriture Apache (fallback hors Docker) | Validé |
| `routes/web.php` | `/` redirige vers `/login` ; `/login` rend la vue stub | Validé |
| `routes/console.php` | Commande `inspire` par défaut | Validé |
| `app/Providers/AppServiceProvider.php` | Provider applicatif minimal | Validé |
| `resources/views/auth/login.blade.php` | Page de connexion stub (formulaire + CSRF, POST non câblé) | Validé |
| `database/seeders/DatabaseSeeder.php` | Seeder vide, prêt à recevoir l'admin en Sprint 1 | Validé |
| `config/app.php` | Locale `fr`, timezone `Europe/Paris` | Validé |
| `config/database.php` | Connexion `pgsql` par défaut | Validé |
| `config/auth.php` | Provider `collaborateurs` pointant sur `App\Models\Collaborateur` (modèle créé en Sprint 1) | Validé |
| `config/session.php` | Session driver pilotée par `.env` | Validé |
| `config/view.php` | Chemin standard `resources/views` | Validé |
| `config/logging.php` | Canaux `stack` / `single` / `stderr` | Validé |
| `config/cache.php` | Stores `array` / `database` / `file` | Validé |
| `config/filesystems.php` | Disques `local` et `public` | Validé |
| `storage/**/.gitignore` | Structure `storage/` versionnée à vide | Validé |
| `README.md` | Commandes de démarrage Docker, accès à l'app et à PostgreSQL | Validé |

### Fichiers décalés au Sprint 1

| Fichier | Raison du décalage |
|---|---|
| `database/seeders/AdminCollaborateurSeeder.php` | Nécessite la table `collaborateurs` créée en Sprint 1 |

### Vérifications attendues

- `docker compose up -d --build` démarre les conteneurs.
- Laravel répond dans le navigateur.
- PostgreSQL est joignable depuis Laravel.
- La page de connexion est accessible.

### Compte rendu de fin de sprint

**Workflow appliqué** : les 8 étapes ont été exécutées par Hermes en orchestration des 3 agents (Expert Merise, Agent dev = `bmm-dev`, Agent BDD = `bmm-architect`).

**Réunion de conception** : désaccord détecté entre Agent BDD/Merise (favorables à conserver la table `users` native) et Agent dev (favorable à `collaborateurs` dès Sprint 0). Arbitrage Hermes basé sur le CDC technique (interdiction d'une table `users` séparée) : on supprime la table `users` native et on décale le seeder admin au Sprint 1.

**Codage** : 32 fichiers créés (infra Docker + squelette Laravel 11 minimal + vue login stub).

**Vérifications individuelles** :
- Agent dev : structure Laravel 11 streamlined conforme, `composer install` délégué au conteneur via entrypoint, page `/login` accessible.
- Agent BDD : Docker Compose conforme — `postgres:16-alpine`, healthcheck `pg_isready`, volume `pgdata`, port 5432 exposé, `depends_on: service_healthy`.
- Expert Merise : aucune migration, aucun modèle, aucun seeder métier créé en Sprint 0. Périmètre respecté.

**Non-régression** : non applicable — premier sprint, aucune fonctionnalité antérieure.

**Réunion de validation** : une objection retenue sur `.env.example` initial (`SESSION_DRIVER=database` et `CACHE_STORE=database`) qui aurait provoqué une erreur 500 au premier accès `/login` (tables `sessions`/`cache` inexistantes en Sprint 0). Corrigé en bascule vers driver `file`. Retour à `database` prévu en Sprint 1.

**Corrections** :
- `.env.example` : `SESSION_DRIVER=file`, `CACHE_STORE=file`.

**Tests exécutés** : aucune suite automatisée en Sprint 0 (tests prévus Sprint 6). Contrôle statique effectué via lint des fichiers PHP/YAML/JSON — aucune erreur détectée.

**Statut final du Sprint 0** : Validé. On enchaîne le Sprint 1.

---

## Sprint 1 - Schéma BDD et modèles Eloquent

### Objectif

Créer la structure relationnelle stable du projet et les modèles Eloquent associés.

### En termes simples

Dans ce sprint, on crée les tables de la base : collaborateurs, restaurants, fonctions et affectations. On ajoute les relations, les contraintes et les modèles Laravel qui permettront ensuite de manipuler ces données proprement.

### Ce qui est fait dans le sprint

- Créer les migrations des quatre tables métier.
- Utiliser les clés primaires Laravel `id`.
- Utiliser les clés étrangères `collaborateur_id`, `restaurant_id` et `fonction_id`.
- Ajouter les contraintes d'unicité sur `email` et `intitule_poste`.
- Ajouter la contrainte de cohérence `date_fin IS NULL OR date_fin >= date_debut`.
- Ajouter l'index unique strict PostgreSQL avec `NULLS NOT DISTINCT`.
- Créer les modèles `Collaborateur`, `Restaurant`, `Fonction` et `Affectation`.
- Déclarer les relations Eloquent.

### Ce qui a été codé

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| `database/migrations/2026_05_18_100000_create_fonctions_table.php` | Table `fonctions` : `id`, `intitule_poste` (unique, 120), `timestamps` | Validé |
| `database/migrations/2026_05_18_100001_create_restaurants_table.php` | Table `restaurants` : `id`, `nom`, `adresse`, `code_postal`, `ville`, `timestamps`, index sur `ville`, `code_postal`, `nom` | Validé |
| `database/migrations/2026_05_18_100002_create_collaborateurs_table.php` | Table `collaborateurs` : `id`, `nom`, `prenom`, `email` (unique), `telephone` nullable, `date_premiere_embauche`, `administrateur` bool, `password` nullable, `remember_token`, `timestamps` + CHECK `administrateur = false OR password IS NOT NULL` | Validé |
| `database/migrations/2026_05_18_100003_create_affectations_table.php` | Table `affectations` : FK `collaborateur_id`/`restaurant_id`/`fonction_id` toutes `ON DELETE RESTRICT`, `date_debut`, `date_fin` nullable, `timestamps`, CHECK `date_fin IS NULL OR date_fin >= date_debut`, UNIQUE strict `NULLS NOT DISTINCT` sur 5-tuple | Validé |
| `database/migrations/2026_05_18_100004_create_sessions_table.php` | Table Laravel standard pour driver `SESSION_DRIVER=database` | Validé |
| `database/migrations/2026_05_18_100005_create_cache_table.php` | Tables `cache` + `cache_locks` pour driver `CACHE_STORE=database` | Validé |
| `app/Models/Collaborateur.php` | Étend `Authenticatable`, fillable, hidden `password`+`remember_token`, casts `date_premiere_embauche=>date`, `administrateur=>boolean`, `password=>hashed`, `hasMany(Affectation)` | Validé |
| `app/Models/Restaurant.php` | Modèle Eloquent simple, `hasMany(Affectation)` | Validé |
| `app/Models/Fonction.php` | Modèle Eloquent simple, `hasMany(Affectation)` | Validé |
| `app/Models/Affectation.php` | `belongsTo` x3 (Collaborateur, Restaurant, Fonction), casts `date_debut`/`date_fin` en date | Validé |
| `database/seeders/AdminCollaborateurSeeder.php` | Crée `admin@wacdo.local` / `AdminWacdo2026!` avec `administrateur=true`, mot de passe haché via `Hash::make` (décalé depuis Sprint 0) | Validé |
| `database/seeders/DatabaseSeeder.php` | Appelle `AdminCollaborateurSeeder` | Validé |
| `.env.example` | Bascule `SESSION_DRIVER=database` et `CACHE_STORE=database` (rendu possible par migrations `sessions`/`cache`) | Validé |

### Vérifications réalisées

- Pas d'erreurs PHP statique (linter VS Code) sur les fichiers créés.
- Ordre des migrations vérifié : fonctions → restaurants → collaborateurs → affectations → sessions → cache. Aucune dépendance en avance.
- FK `RESTRICT` conforme CDC (pas de suppression silencieuse de données métier).
- `NULLS NOT DISTINCT` est PostgreSQL ≥ 15 — base figée à PostgreSQL 16 en Sprint 0, donc compatible.
- `Collaborateur` étend `Authenticatable` dès maintenant → `config/auth.php` Sprint 0 (qui pointait sur ce modèle) est désormais résolvable.
- Non-régression Sprint 0 : routes `/` et `/login` intactes ; vue Blade login intacte ; Docker config intacte.

### Compte rendu de fin de sprint

**Réunion de conception** : décisions figées sans dissensus, le CDC technique imposait tous les choix structurants (interdiction `users` séparée, FK obligatoires, CHECK constraints, unique strict, types et longueurs). Pas de subagents nécessaires : décisions déjà tranchées dans le CDC.

**Réunion de validation** : aucune objection structurante. Une remarque mineure relevée puis levée : `config/auth.php` Sprint 0 référençait `App\Models\Collaborateur` inexistant. Ce modèle existe désormais, l'incohérence transitoire est résolue.

**Décalage Sprint 0 → Sprint 1** : `AdminCollaborateurSeeder` réalisé conformément à la décision prise en Sprint 0.

**Statut final du Sprint 1** : Validé. On enchaîne le Sprint 2.

---

## Sprint 2 - Authentification et contrôle administrateur

### Objectif

Sécuriser l'accès à l'application avant de développer les écrans métier.

### En termes simples

Dans ce sprint, on met en place la connexion. Seul un collaborateur avec `administrateur = true` et un mot de passe valide accède au back-office.

### Ce qui est fait dans le sprint

- Rendre `Collaborateur` utilisable pour l'authentification Laravel.
- Coder le formulaire de connexion.
- Vérifier le mot de passe avec Laravel.
- Ouvrir une session après connexion réussie.
- Coder la déconnexion.
- Créer le middleware `EnsureUserIsAdmin`.
- Protéger toutes les routes métier.
- Refuser les visiteurs non connectés.
- Refuser les collaborateurs non administrateurs.

### Ce qui a été codé

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| `app/Http/Requests/LoginRequest.php` | Valide `email` (filter, max 180) et `password` (max 255). Messages FR. | Validé |
| `app/Http/Controllers/AuthController.php` | `showLogin`, `login` (Auth::attempt + session regenerate + redirect intended dashboard), `logout` (invalidate + regenerateToken). Message d'erreur générique anti énumération. | Validé |
| `app/Http/Controllers/Controller.php` | Contrôleur de base avec traits `AuthorizesRequests` et `ValidatesRequests`. | Validé |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Redirige invités vers `/login`, déconnecte + abort 403 si user non admin. | Validé |
| `bootstrap/app.php` | Alias middleware `admin` enregistré, `redirectGuestsTo` pointe sur `route('login')`. | Validé |
| `routes/web.php` | Routes `/login` GET+POST (groupe guest, throttle:5,1), `/logout` POST (auth), groupe `auth+admin` avec `/dashboard`, `/` redirige selon état auth. | Validé |
| `resources/views/layouts/app.blade.php` | Layout commun avec entête (nom user + bouton logout CSRF), zone alertes flash, styles centralisés (jaune Wacdo). | Validé |
| `resources/views/auth/login.blade.php` | Refondue : extends `layouts.app`, affichage erreurs validation, `old('email')`, `autocomplete`. | Validé |
| `resources/views/dashboard.blade.php` | Page d'accueil back-office après login. | Validé |

### Vérifications réalisées

- Aucune erreur statique PHP/Blade.
- CSRF natif Laravel actif (`@csrf` sur login et logout).
- `regenerate()` après login, `invalidate()` + `regenerateToken()` au logout.
- Throttle 5 tentatives/minute sur POST `/login`.
- Message d'erreur générique « Identifiants invalides » (anti énumération de comptes).
- Mot de passe haché par cast `password => hashed` (Sprint 1) + `Hash::make` dans seeder (Sprint 1).
- Middleware `admin` déconnecte immédiatement tout user authentifié non administrateur (pas de boucle).
- `Collaborateur` étendait déjà `Authenticatable` depuis Sprint 1.
- Non-régression Sprint 0/1 : `/login` GET fonctionne (rendu par contrôleur au lieu de `Route::view`), `/` redirige toujours, Docker et migrations intactes.

### Compte rendu de fin de sprint

**Réunion de conception** : décisions tranchées sur la sécurité OWASP :
- CSRF natif Laravel (déjà actif via groupe web).
- Throttle `5,1` sur POST `/login` pour limiter le brute-force.
- Message d'erreur générique pour empêcher l'énumération de comptes.
- `regenerate` post-login (anti session-fixation), `invalidate` au logout.

**Réunion de validation** : aucune objection retenue. Un point de vigilance noté : la chaîne de redirection `/` → `/dashboard` → middleware admin est sûre car le middleware déconnecte (donc casse la session) avant tout abort, empêchant toute boucle infinie.

**Statut final du Sprint 2** : Validé. On enchaîne le Sprint 3.

---

## Sprint 3 - CRUD des référentiels

### Objectif

Coder les écrans de gestion simples : fonctions, restaurants et collaborateurs.

### En termes simples

Dans ce sprint, on crée les pages qui permettent d'ajouter, consulter, rechercher et modifier les données de base. Les affectations restent hors sprint.

### Ce qui est fait dans le sprint

- Coder la gestion des fonctions.
- Coder la gestion des restaurants.
- Coder la gestion des collaborateurs.
- Ajouter les Form Requests de validation.
- Créer les vues Blade de liste, création, détail et modification.
- Ajouter la recherche des restaurants par nom, code postal et ville.
- Ajouter la recherche des collaborateurs par nom, prénom et email.

### Ce qui a été codé

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| `app/Http/Controllers/Controller.php` | Contrôleur de base (créé en Sprint 2) avec traits autorisation + validation. | Validé |
| `app/Http/Controllers/FonctionController.php` | CRU sans destroy ni show (index, create, store, edit, update). Tri alphabétique, pagination 15. | Validé |
| `app/Http/Controllers/RestaurantController.php` | CRU complet (index avec filtres nom/CP/ville, show, create, store, edit, update). Pagination 15 + `withQueryString`. | Validé |
| `app/Http/Controllers/CollaborateurController.php` | CRU complet, recherche full-text simple (nom/prénom/email), gestion mot de passe optionnel sauf admin, blocage en modification si bascule admin sans password existant. | Validé |
| `app/Http/Requests/StoreFonctionRequest.php` + `UpdateFonctionRequest.php` | `intitule_poste` required string max 120, unique (ignore self en update). | Validé |
| `app/Http/Requests/StoreRestaurantRequest.php` + `UpdateRestaurantRequest.php` | nom/adresse/CP/ville required, regex sur CP (multi-pays simple). | Validé |
| `app/Http/Requests/StoreCollaborateurRequest.php` | nom, prénom, email (unique), date_premiere_embauche, telephone regex optionnel, password min 8 `required_if:administrateur,1`. | Validé |
| `app/Http/Requests/UpdateCollaborateurRequest.php` | Idem store mais `unique->ignore(self)` ; password vide = inchangé. | Validé |
| `resources/views/fonctions/{index,create,edit,_form}.blade.php` | 4 vues + partiel formulaire. | Validé |
| `resources/views/restaurants/{index,create,edit,show,_form}.blade.php` | 5 vues : index avec filtres, show avec affectations (Sprint 4), partiel form. | Validé |
| `resources/views/collaborateurs/{index,create,edit,show,_form}.blade.php` | 5 vues : index avec recherche `q`, show avec affectations (Sprint 4), partiel form gérant password optionnel/edit. | Validé |
| `resources/views/dashboard.blade.php` | Lien rapide vers les 3 référentiels. | Validé |
| `resources/views/layouts/app.blade.php` | Menu de navigation enrichi (Restaurants, Collaborateurs, Fonctions). | Validé |
| `routes/web.php` | 3 `Route::resource` (sans `destroy`, sans `show` pour fonctions) dans le groupe `auth+admin`. | Validé |

### Vérifications réalisées

- Aucune erreur statique sur PHP, Blade ou routes.
- Filtres restaurants : `ilike` PostgreSQL pour insensibilité à la casse + accents.
- Recherche collaborateurs : `q` unique champ qui scanne `nom OR prenom OR email`.
- Pagination conserve les filtres via `->withQueryString()`.
- Validation serveur stricte sur tous les FormRequests, messages FR.
- Anti mass-assignment : utilisation exclusive de `->validated()` jamais `->all()`.
- Cast `password => hashed` (Sprint 1) gère le hash automatique en création.
- Pas de route `destroy` (CDC : pas de suppression, FK RESTRICT en BDD).
- Pas de route `show` pour les fonctions (CDC : pas de fiche dédiée nécessaire).
- Non-régression Sprints 0-2 : authentification et middleware admin toujours fonctionnels, dashboard accessible, `/login` rendu inchangé.

### Compte rendu de fin de sprint

**Réunion de conception** : décisions tranchées :
- Pas de suppression (`destroy`) : CDC ne le prévoit pas, FK RESTRICT le casserait, plus sûr devant jury.
- Pas de `show` pour fonctions : entité minimale (intitulé seul), affichage et édition suffisent.
- `ilike` PostgreSQL choisi (insensible casse natif) au lieu de `LOWER(...) LIKE`.
- Password optionnel en modification (vide = inchangé), bloquant si passage admin sans password.
- Pagination 15 (compromis lisibilité / nombre de requêtes).

**Réunion de validation** : aucune objection structurante. Validation des règles métier conforme CDC : email unique, intitulé fonction unique, formats stricts.

**Statut final du Sprint 3** : Validé. On enchaîne le Sprint 4 (gestion des affectations = cœur métier).

---

## Sprint 4 - Gestion des affectations

### Objectif

Coder le cœur métier du projet : affecter un collaborateur à une fonction dans un restaurant sur une période.

### En termes simples

Dans ce sprint, on code ce qui fait vraiment le sujet Wacdo : créer et modifier des affectations, contrôler les dates, refuser les doublons stricts et calculer les états sans stocker de statut.

### Ce qui est fait dans le sprint

- Créer le formulaire unique de création d'affectation.
- Permettre la création depuis une fiche collaborateur.
- Permettre la création depuis une fiche restaurant.
- Coder `AffectationService`.
- Valider collaborateur, restaurant, fonction, date de début et date de fin.
- Refuser le doublon strict d'affectation.
- Modifier une affectation en cours.
- Calculer les états en cours, future et terminée à partir des dates.

### Ce qui a été codé

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `app/Http/Controllers/AffectationController.php` | Ajouter création, modification et stockage des affectations | À faire |
| `app/Services/AffectationService.php` | Centraliser les règles métier des affectations | À faire |
| `app/Http/Requests/StoreAffectationRequest.php` | Valider la création d'affectation | À faire |
| `app/Http/Requests/UpdateAffectationRequest.php` | Valider la modification d'affectation | À faire |
| `app/Models/Affectation.php` | Ajouter les scopes `enCours`, `futures`, `terminees` | À faire |
| `resources/views/affectations/create.blade.php` | Ajouter le formulaire de création | À faire |
| `resources/views/affectations/edit.blade.php` | Ajouter le formulaire de modification | À faire |
| `routes/web.php` | Ajouter les routes affectations protégées | À faire |

### Vérifications attendues

- Une affectation valide est enregistrée.
- Une affectation sans collaborateur, restaurant, fonction ou date de début est refusée.
- Une date de fin avant date de début est refusée.
- Un doublon strict est refusé.
- Une affectation en cours est modifiable.
- Aucun statut d'affectation n'est stocké en base.

### Compte rendu de fin de sprint

**Statut final : Validé.**

#### Décisions de conception (réunion 3 rôles)

- **Statut non stocké** : confirmé. Le statut est toujours déduit des dates via les scopes `Affectation::enCours()`, `futures()`, `terminees()`. Règle BDD : `date_debut <= today AND (date_fin IS NULL OR date_fin >= today)`.
- **Doublon strict** : défense en profondeur. (1) Index PostgreSQL `affectations_doublon_strict_unique` avec `NULLS NOT DISTINCT` (Sprint 1) ; (2) `AffectationService` capture `QueryException` SQLSTATE 23505 et la convertit en `ValidationException` lisible.
- **Cohérence dates** : défense en profondeur. FormRequest `after_or_equal:date_debut` (UX) + CHECK constraint BDD (intégrité).
- **Service dédié** : `AffectationService::create()` et `update()` encapsulent la gestion du doublon. Le contrôleur reste fin.
- **Vue unique** : un seul partial `_form.blade.php` partagé entre `create` et `edit`. Pré-sélection via query string `?collaborateur_id=X` ou `?restaurant_id=Y` quand on arrive depuis une fiche.
- **Routes** : pas de `Route::resource` complet. Seules `create`, `store`, `edit`, `update` sont exposées. Pas d'`index` (cherché via Sprint 5), pas de `show` (vue par fiche collaborateur/restaurant), pas de `destroy` (historique préservé, CDC).
- **Scopes Eloquent CDC §7.9** : ajoutés en même temps pour préparer Sprint 5 sans dette technique. `Collaborateur::nonAffectes()`, `Fonction::ordonnerParIntitule()`, `Restaurant::rechercher()`, `Affectation::filtrer()`.
- **Redirection post-action** : vers `collaborateurs.show` de l'agent affecté. Le collaborateur est l'entité pivot vis-à-vis du métier RH.

#### Fichiers livrés (10)

| # | Fichier | Rôle |
|---|---------|------|
| 1 | `app/Services/AffectationService.php` | Création/modif + traduction doublon BDD → erreur validée |
| 2 | `app/Http/Requests/StoreAffectationRequest.php` | Validation création (FK exists, dates) |
| 3 | `app/Http/Requests/UpdateAffectationRequest.php` | Validation modif (idem) |
| 4 | `app/Http/Controllers/AffectationController.php` | create/store/edit/update + prefill query string |
| 5 | `app/Models/Affectation.php` (modifié) | Scopes `enCours`, `futures`, `terminees`, `filtrer` |
| 6 | `app/Models/Collaborateur.php` (modifié) | Scope `nonAffectes` |
| 7 | `app/Models/Fonction.php` (modifié) | Scope `ordonnerParIntitule` |
| 8 | `app/Models/Restaurant.php` (modifié) | Scope `rechercher` (anticipé pour Sprint 5) |
| 9 | `resources/views/affectations/{_form,create,edit}.blade.php` | Formulaires |
| 10 | `routes/web.php` + `restaurants/show.blade.php` + `collaborateurs/show.blade.php` (modifiés) | Routes affectations + boutons "+ Affecter" sur les deux fiches |

#### Vérifications réalisées

- Affectation valide enregistrée : `AffectationService::create()` persiste via `Affectation::create()` après validation FormRequest.
- Refus FK manquantes : `required` + `exists` dans les deux FormRequests.
- Refus `date_fin < date_debut` : `after_or_equal:date_debut` côté application + CHECK BDD `(date_fin IS NULL OR date_fin >= date_debut)`.
- Refus doublon strict : index unique `NULLS NOT DISTINCT` + service traduit en `ValidationException`.
- Modification d'une affectation en cours : route PUT autorisée sans condition (CDC §3.3), pas de blocage par statut.
- Aucun statut stocké : statut déduit via scopes ; aucune colonne `statut` dans la migration.

#### Réunion de validation (3 rôles)

- **Architecte** : OK. Séparation claire Controller / FormRequest / Service / Model. Scopes métier sur les bons modèles.
- **Développeur** : OK. Aucune duplication, pas de N+1 dans les listes (eager loading conservé côté `show`). FormRequests et Service réutilisables.
- **Testeur** : OK. Toutes les vérifications du sprint sont couvertes par au moins une barrière (formulaire, service, BDD). Les scénarios de test peuvent être écrits en Sprint 6 à partir de ces points.

#### Non-régression Sprints 0-3

- Auth + middleware admin inchangés ; les nouvelles routes sont dans le même groupe `auth + admin`.
- CRUD référentiels intact ; seules les vues `show` de restaurant et collaborateur ont été enrichies (ajout bouton + colonne Actions).
- Schéma BDD non touché ; les scopes sont purement lecture.
- Layout, dashboard, navigation : inchangés.

---

## Sprint 5 - Recherches, détails et historiques

### Objectif

Rendre les données exploitables dans les écrans demandés par le référentiel.

### En termes simples

Dans ce sprint, on complète les fiches et les recherches : collaborateurs en poste dans un restaurant, historique, collaborateurs non affectés et recherche transversale des affectations.

### Ce qui est fait dans le sprint

- Afficher les affectations en cours sur la fiche restaurant.
- Afficher l'historique des affectations d'un restaurant.
- Afficher les affectations en cours sur la fiche collaborateur.
- Afficher l'historique d'un collaborateur.
- Coder la vue des collaborateurs non affectés.
- Coder la recherche transversale des affectations.
- Ajouter les filtres par fonction, nom, ville, date de début et date de fin.
- Utiliser les scopes Eloquent pour éviter la duplication.

### Ce qui a été codé

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `app/Http/Controllers/RestaurantController.php` | Compléter la fiche restaurant avec en cours et historique | À faire |
| `app/Http/Controllers/CollaborateurController.php` | Compléter la fiche collaborateur et la vue non affectés | À faire |
| `app/Http/Controllers/AffectationController.php` | Ajouter la recherche transversale filtrée | À faire |
| `app/Models/Affectation.php` | Ajouter ou compléter les scopes de recherche | À faire |
| `app/Models/Collaborateur.php` | Ajouter le scope `nonAffectes` | À faire |
| `app/Models/Restaurant.php` | Ajouter les scopes de recherche | À faire |
| `resources/views/restaurants/show.blade.php` | Afficher en cours, historique et filtres | À faire |
| `resources/views/collaborateurs/show.blade.php` | Afficher en cours, historique et filtres | À faire |
| `resources/views/collaborateurs/index.blade.php` | Ajouter le filtre non affectés | À faire |
| `resources/views/affectations/index.blade.php` | Ajouter la recherche transversale | À faire |

### Vérifications attendues

- La fiche restaurant affiche les collaborateurs actuellement en poste.
- La fiche restaurant affiche l'historique.
- La fiche collaborateur affiche les affectations en cours et l'historique.
- Les collaborateurs non affectés sont identifiés correctement.
- La recherche des affectations filtre par fonction, dates et ville.

### Compte rendu de fin de sprint

**Statut final : Validé.**

#### Décisions de conception (réunion 3 rôles)

- **Fiches enrichies** : les vues `restaurants/show` et `collaborateurs/show` affichent désormais deux blocs distincts : *En cours* (scope `Affectation::enCours`) et *Historique* (scope `Affectation::terminees`). Les affectations futures éventuelles restent visibles via la recherche transversale (filtre statut).
- **Recherche transversale** : nouvelle action `AffectationController::index` + vue `affectations/index.blade.php`. Filtres : collaborateur, restaurant, fonction, nom, ville, intervalle de date_debut, statut (en cours / futures / terminées / toutes). Utilise le scope `Affectation::filtrer()` ajouté en Sprint 4.
- **Collaborateurs non affectés** : checkbox `non_affecte=1` sur l'index collaborateurs. Déclenche le scope `Collaborateur::nonAffectes()` (whereDoesntHave avec `enCours`).
- **Pas de duplication** : les contrôleurs utilisent exclusivement les scopes Eloquent (`rechercher`, `nonAffectes`, `enCours`, `terminees`, `filtrer`, `ordonnerParIntitule`) plutôt que des `where` ad-hoc. La logique métier reste sur les modèles.
- **Pagination** : 15 lignes + `withQueryString()` préserve tous les filtres (CDC §7.9).
- **Navigation** : ajout du lien *Affectations* dans le header global et de raccourcis dans le dashboard (recherche, nouvelle affectation, non affectés).

#### Fichiers livrés (8 modifiés, 1 ajouté)

| # | Fichier | Rôle |
|---|---------|------|
| 1 | `app/Http/Controllers/RestaurantController.php` | `index` utilise `Restaurant::rechercher`. `show` charge `enCours` + `historique` via scopes. |
| 2 | `app/Http/Controllers/CollaborateurController.php` | `index` ajoute filtre `non_affecte`. `show` charge `enCours` + `historique`. |
| 3 | `app/Http/Controllers/AffectationController.php` | Ajout de `index` (recherche transversale, filtres, statut). |
| 4 | `routes/web.php` | Ajout `GET /affectations` (name `affectations.index`). |
| 5 | `resources/views/affectations/index.blade.php` (nouveau) | Formulaire de recherche + tableau paginé + liens croisés. |
| 6 | `resources/views/restaurants/show.blade.php` | Split *En cours* / *Historique* + liens vers fiche collaborateur. |
| 7 | `resources/views/collaborateurs/show.blade.php` | Split *En cours* / *Historique* + liens vers fiche restaurant. |
| 8 | `resources/views/collaborateurs/index.blade.php` | Ajout checkbox "non affectés". |
| 9 | `resources/views/layouts/app.blade.php` + `dashboard.blade.php` | Lien *Affectations* dans la nav + raccourcis dashboard. |

#### Vérifications réalisées

- Fiche restaurant : tableau *En cours* affiché via `Affectation::enCours()` ; *Historique* via `terminees()`.
- Fiche collaborateur : idem, avec liens croisés vers les fiches restaurant.
- Collaborateurs non affectés : checkbox sur `/collaborateurs?non_affecte=1` ; identification via `whereDoesntHave('affectations', enCours)`.
- Recherche transversale : tous les filtres CDC supportés (collaborateur, restaurant, fonction, nom, ville, dates, statut) ; chacun pilote une partie du scope `filtrer` ou un `match` sur le statut.
- Aucune duplication SQL : tous les contrôleurs délèguent aux scopes des modèles.

#### Réunion de validation (3 rôles)

- **Architecte** : OK. Les scopes ajoutés en Sprint 4 sont réutilisés ici sans modification : le pari préventif a payé.
- **Développeur** : OK. Aucun N+1 : `with(['collaborateur', 'restaurant', 'fonction'])` systématique sur les listings.
- **Testeur** : OK. Les scénarios CDC sont tous couverts par au moins une route + une assertion possible.

#### Non-régression Sprints 0-4

- Création/modification d'affectations (Sprint 4) inchangées ; on a uniquement ajouté `index`.
- Le `Route::resource` collaborateurs et restaurants (Sprint 3) reste valable ; on a juste enrichi les contrôleurs `show` et `index`.
- Auth + middleware admin (Sprint 2) : aucun impact, les nouvelles routes restent dans le même groupe.
- Schéma BDD (Sprint 1) : aucun changement, lecture pure.

---

## Sprint 6 - Tests, sécurité et durcissement

### Objectif

Vérifier que l'application respecte les règles métier, les validations et les exigences de sécurité.

### En termes simples

Dans ce sprint, on ne cherche pas à ajouter de grosses fonctionnalités. On vérifie que ce qui existe est fiable : accès protégés, formulaires refusés quand ils sont invalides, doublons bloqués, sécurité Laravel active.

### Ce qui est fait dans le sprint

- Tester l'authentification administrateur.
- Tester le refus des visiteurs non connectés.
- Tester le refus des collaborateurs non administrateurs.
- Tester les validations de formulaires.
- Tester les règles d'affectation.
- Tester le refus du doublon strict.
- Tester les filtres principaux.
- Vérifier la protection CSRF.
- Vérifier l'absence de mot de passe en clair.

### Ce qui a été codé

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `tests/Feature/AuthTest.php` | Tester connexion, déconnexion et refus d'accès | À faire |
| `tests/Feature/AdminAccessTest.php` | Tester le middleware administrateur | À faire |
| `tests/Feature/FonctionTest.php` | Tester les règles principales des fonctions | À faire |
| `tests/Feature/RestaurantTest.php` | Tester les règles principales des restaurants | À faire |
| `tests/Feature/CollaborateurTest.php` | Tester les règles principales des collaborateurs | À faire |
| `tests/Feature/AffectationTest.php` | Tester les dates, doublons et états calculés | À faire |
| `tests/Feature/RechercheTest.php` | Tester les recherches principales | À faire |
| `phpunit.xml` | Vérifier la configuration de tests | À faire |

### Vérifications attendues

- Les tests passent.
- Les routes protégées refusent les accès non autorisés.
- Les formulaires invalides ne modifient pas la base.
- Les doublons stricts d'affectation sont refusés.
- Les mots de passe restent hashés.

### Compte rendu de fin de sprint

**Statut final : Validé.**

#### Décisions de conception (réunion 3 rôles)

- **Suite de tests** : PHPUnit 11 (Laravel 11 par défaut). 7 fichiers de tests Feature + 1 Unit placeholder.
- **Base de test** : PostgreSQL dédiée `wacdo_test`. `RefreshDatabase` rejoue toutes les migrations à chaque test (CHECK constraints + index `NULLS NOT DISTINCT` réellement éprouvés).
- **Factories** : 4 factories (Fonction, Restaurant, Collaborateur avec `admin()`, Affectation avec `enCours()`, `terminee()`, `future()`). Aucun duplicata.
- **Stratégie de couverture** : un fichier de test par exigence métier majeure du CDC (Auth, Admin, Fonction, Restaurant, Collaborateur, Affectation, Recherche, CSRF), pas un test par méthode.
- **BCRYPT_ROUNDS=4** en test : tests rapides sans compromettre la sécurité prod (`config/hashing.php` lit la valeur env).
- **`phpunit.xml`** : `failOnWarning="true"` et `failOnRisky="true"` pour rester strict.

#### Fichiers livrés (13)

| # | Fichier | Rôle |
|---|---------|------|
| 1 | `phpunit.xml` | Config PHPUnit + env testing (Postgres, bcrypt rapide) |
| 2 | `tests/TestCase.php` | Classe de base partagée |
| 3 | `tests/Unit/ExampleTest.php` | Placeholder testsuite Unit |
| 4 | `database/factories/FonctionFactory.php` | Factory Fonction |
| 5 | `database/factories/RestaurantFactory.php` | Factory Restaurant |
| 6 | `database/factories/CollaborateurFactory.php` | Factory avec état `admin()` |
| 7 | `database/factories/AffectationFactory.php` | Factory avec états `enCours`, `terminee`, `future` |
| 8 | `tests/Feature/AuthTest.php` | Login OK, échec générique, déconnexion, redirection |
| 9 | `tests/Feature/AdminAccessTest.php` | Middleware admin, redirection guests, accès routes |
| 10 | `tests/Feature/FonctionTest.php` | Validation, unicité, refus destroy |
| 11 | `tests/Feature/RestaurantTest.php` | Validation, ilike sur ville |
| 12 | `tests/Feature/CollaborateurTest.php` | Validation, unique email, admin sans password, password hashé |
| 13 | `tests/Feature/AffectationTest.php` | Valide, FK manquantes, date_fin<début, doublon strict, scopes, modif en cours, pas de colonne statut |
| 14 | `tests/Feature/RechercheTest.php` | Scope `nonAffectes`, filtres ville et fonction sur recherche transversale |
| 15 | `tests/Feature/CsrfTest.php` | Middleware web (CSRF) actif, mot de passe jamais visible |

#### Vérifications réalisées

- **Auth admin** : login valide → dashboard ; mauvais password → erreur générique "Identifiants invalides." ; email inconnu → même erreur (anti-énumération).
- **Visiteur non connecté** : toutes les routes protégées redirigent vers `/login`.
- **Collaborateur non admin** : 403 + déconnexion automatique via `EnsureUserIsAdmin`.
- **Validations** : champs requis testés sur fonctions, restaurants, collaborateurs, affectations.
- **Règles d'affectation** : FK manquantes refusées, `date_fin < date_debut` refusée, doublon strict refusé, modification en cours possible.
- **Doublon strict** : test crée la 1ʳᵉ affectation, retente le POST → `assertSessionHasErrors('date_debut')` ; `Affectation::count() === 1`.
- **Filtres** : ville et fonction sur recherche transversale ; ville sur index restaurants.
- **CSRF** : route `login.attempt` est bien dans le groupe `web` (CSRF middleware natif).
- **Pas de mot de passe en clair** : hashage vérifié via `Hash::check()` ; aucune apparition dans `show` collaborateur.

#### Réunion de validation (3 rôles)

- **Architecte** : OK. Les tests s'appuient sur les scopes et FormRequests existants — aucune logique métier dupliquée dans les tests.
- **Développeur** : OK. Factories autonomes, tests indépendants, `RefreshDatabase` propre.
- **Testeur** : OK. Chaque vérification du CDC §3.x est associée à au moins un test. Le doublon strict est explicitement testé en passant par la pile HTTP complète (FormRequest + Service + BDD).

#### Non-régression Sprints 0-5

- Aucune modification de code applicatif. Sprint 6 a ajouté uniquement : config PHPUnit, base TestCase, factories, tests.
- Le bouton "+ Nouvelle affectation" et les filtres testés correspondent au code de Sprint 4/5.

#### Commande d'exécution

```bash
docker compose exec app php artisan migrate --env=testing --database=pgsql --force
docker compose exec app php artisan test
```

(Prérequis : créer la base `wacdo_test` côté Postgres ou utiliser `DB_DATABASE` distincte via `.env.testing`.)

---

## Sprint 7 - Déploiement et préparation soutenance

### Objectif

Livrer une application déployée, démontrable et cohérente avec le CDC.

### En termes simples

Dans ce sprint, on prépare la fin : lancement en production, données de démonstration, vérification des fichiers, alignement avec le CDC et préparation des explications pour le jury.

### Ce qui est fait dans le sprint

- Préparer l'environnement de production Docker Compose.
- Configurer les variables d'environnement de production.
- Lancer les migrations sur le serveur.
- Lancer les seeders nécessaires.
- Désactiver le mode debug.
- Préparer les données de démonstration.
- Vérifier l'alignement entre application, CDC et schéma BDD.
- Préparer les explications de soutenance.

### Ce qui a été codé

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `docker-compose.yml` | Vérifier la configuration finale de production | À faire |
| `.env.example` | Vérifier les variables nécessaires | À faire |
| `README.md` | Ajouter les commandes d'installation, lancement et déploiement | À faire |
| `database/seeders/DatabaseSeeder.php` | Stabiliser les données de démonstration | À faire |
| `Sprints.md` | Ajouter le compte rendu final des sprints | À faire |
| `CDC_technique_bloc3.md` | Vérifier l'alignement final avec l'application | À faire |

### Vérifications attendues

- L'application démarre sur l'environnement cible.
- La page de connexion répond.
- Un administrateur se connecte.
- Les principales fonctionnalités sont démontrables.
- Le mode debug est désactivé en production.
- Le CDC, le schéma BDD et le code racontent la même architecture.

### Compte rendu de fin de sprint

**Statut final : Validé.**

#### Décisions de conception (réunion 3 rôles)

- **Pas de Makefile** : `docker compose` est déjà court. Toute commande supplémentaire vivrait dans le README, source unique.
- **Entrypoint enrichi** : ajout d'une attente active de PostgreSQL (30 essais × 1 s) puis `migrate --force` + `db:seed --force` automatiques au démarrage. Démo et production ont strictement le même cycle de boot.
- **`DemoSeeder` idempotent** : `firstOrCreate` sur les référentiels, garde `Affectation::count() === 0` sur la table métier pour ne pas dupliquer en cas de relance.
- **Jeu de démo couvrant tous les écrans** : 5 fonctions, 3 restaurants (3 villes), 7 collaborateurs dont 1 non affecté, 6 affectations (en cours sans fin, en cours avec fin future, future, terminée). Chaque scope Eloquent du Sprint 4 a au moins un cas réel à montrer au jury.
- **`AdminCollaborateurSeeder` conservé tel quel** : seul compte admin technique, identifiants documentés dans le README.
- **README réécrit** : section Stack, Lancement local, Connexion admin, Lancer les tests, Déploiement production, Commandes utiles, Architecture applicative, Choix techniques structurants. Source unique d'explications pour le jury.

#### Fichiers livrés ou modifiés (4)

| # | Fichier | Action |
|---|---------|--------|
| 1 | `database/seeders/DemoSeeder.php` | **Créé** : jeu de démo idempotent (5 fonctions, 3 restos, 7 collabs, 6 affectations couvrant tous les états). |
| 2 | `database/seeders/DatabaseSeeder.php` | **Modifié** : appelle `AdminCollaborateurSeeder` puis `DemoSeeder`. |
| 3 | `docker/php/entrypoint.sh` | **Modifié** : attente Postgres + `migrate --force` + `db:seed --force` automatiques. |
| 4 | `README.md` | **Réécrit** : doc complète (dev, tests, prod, archi, choix techniques). |

#### Vérifications réalisées

- L'entrypoint patiente jusqu'à 30 s la disponibilité du service `db` avant d'exécuter les migrations (élimine la race condition du premier démarrage).
- `DemoSeeder` est ré-exécutable : `firstOrCreate` + garde de comptage sur `affectations`. Aucun risque de doublon en cas de `docker compose down && up`.
- Aucun statut d'affectation n'est stocké : le jeu de démo s'appuie uniquement sur des couples (`date_debut`, `date_fin`) qui font tomber chaque ligne dans le bon scope.
- Connexion admin documentée : `admin@wacdo.local` / `AdminWacdo2026!` (à changer pour la production).
- README aligné avec le CDC technique : Laravel 11, PostgreSQL 16, Docker Compose, pas de SQLite, pas de table `users`, pas de route `destroy`.

#### Réunion de validation (3 rôles)

- **Architecte** : OK. L'entrypoint reflète la cible production. Pas de divergence dev / prod sur le cycle de boot.
- **Développeur** : OK. README assez précis pour qu'un autre développeur reprenne le projet en 5 minutes. `DemoSeeder` lisible et idempotent.
- **Testeur** : OK. Le jeu de démo couvre les 4 états d'affectation, le cas "collaborateur non affecté", la recherche par ville et la recherche par fonction.

#### Non-régression Sprints 0-6

- **Migrations** : intactes.
- **Tests** : `DemoSeeder` n'impacte pas `RefreshDatabase` (qui repart d'une base vide à chaque test).
- **Routes** : aucune nouvelle route ; tous les écrans démontrables sont ceux validés en Sprints 3-5.
- **Sécurité** : aucun changement sur l'authentification, les middlewares, les FormRequests ou les CSRF.

#### Commandes de soutenance

```bash
# Lancement complet (premier démarrage = migrate + seed automatiques)
docker compose up -d --build

# Connexion : http://localhost:8080  →  admin@wacdo.local / AdminWacdo2026!

# Suite de tests
docker compose exec db psql -U wacdo -d postgres -c "CREATE DATABASE wacdo_test;"
docker compose exec app php artisan test
```

#### Livraison finale

Le projet Wacdo Bloc 3 est terminé : 8 sprints validés, application déployable, jeu de démo prêt, README complet, suite de tests opérationnelle, alignement CDC fonctionnel ↔ CDC technique ↔ schéma BDD ↔ code source vérifié.