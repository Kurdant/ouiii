# Plan de sprints - Wacdo Bloc 3

Ce fichier sert de document de pilotage pour le développement du projet Wacdo Bloc 3. Il fixe l'ordre de réalisation, le contenu de chaque sprint, les fichiers attendus et le workflow de validation à trois agents.

Le projet est développé sprint par sprint. Aucun sprint suivant ne démarre tant que le sprint en cours n'est pas codé, vérifié, corrigé si nécessaire et documenté dans ce fichier.

## Sommaire des sprints

| Sprint | Titre | Objectif simple | Statut |
|---|---|---|---|
| [Sprint 0](#sprint-0---mise-en-place-technique) | Mise en place technique | Installer Laravel 11, Docker et la base de départ | À faire |
| [Sprint 1](#sprint-1---schema-bdd-et-modeles-eloquent) | Schéma BDD et modèles Eloquent | Créer les tables, contraintes et modèles | À faire |
| [Sprint 2](#sprint-2---authentification-et-controle-administrateur) | Authentification et contrôle administrateur | Sécuriser l'accès au back-office | À faire |
| [Sprint 3](#sprint-3---crud-des-referentiels) | CRUD des référentiels | Coder fonctions, restaurants et collaborateurs | À faire |
| [Sprint 4](#sprint-4---gestion-des-affectations) | Gestion des affectations | Coder le cœur métier des affectations | À faire |
| [Sprint 5](#sprint-5---recherches-details-et-historiques) | Recherches, détails et historiques | Rendre les données exploitables dans les écrans | À faire |
| [Sprint 6](#sprint-6---tests-securite-et-durcissement) | Tests, sécurité et durcissement | Vérifier les règles métier et la sécurité | À faire |
| [Sprint 7](#sprint-7---deploiement-et-preparation-soutenance) | Déploiement et préparation soutenance | Déployer et préparer la démonstration jury | À faire |

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

- Installer Laravel 11.
- Créer l'environnement Docker Compose.
- Configurer les services `app`, `web` et `db`.
- Préparer PostgreSQL 16.
- Préparer `.env.example` et `.env` local.
- Vérifier la connexion entre Laravel et PostgreSQL.
- Créer le seeder du premier collaborateur administrateur.
- Utiliser `Hash::make` pour le mot de passe administrateur.
- Obtenir une page de connexion accessible.

### Ce qui a été codé

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `composer.json` | Ajouter les dépendances Laravel du projet | À faire |
| `artisan` | Ajouter le point d'entrée CLI Laravel | À faire |
| `.env.example` | Définir les variables d'environnement attendues | À faire |
| `docker-compose.yml` | Déclarer les services `app`, `web` et `db` | À faire |
| `Dockerfile` | Construire l'image PHP 8.3-FPM de l'application | À faire |
| `docker/nginx/default.conf` | Configurer Nginx avec `public/` comme racine web | À faire |
| `config/database.php` | Vérifier la connexion PostgreSQL | À faire |
| `database/seeders/DatabaseSeeder.php` | Appeler le seeder initial | À faire |
| `database/seeders/AdminCollaborateurSeeder.php` | Créer le premier administrateur avec `Hash::make` | À faire |
| `routes/web.php` | Déclarer les premières routes de connexion | À faire |

### Vérifications attendues

- `docker compose up -d --build` démarre les conteneurs.
- Laravel répond dans le navigateur.
- PostgreSQL est joignable depuis Laravel.
- Le seeder crée un collaborateur administrateur.
- La page de connexion est accessible.

### Compte rendu de fin de sprint

À compléter après codage et réunion de validation.

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

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `database/migrations/create_collaborateurs_table.php` | Créer la table des collaborateurs | À faire |
| `database/migrations/create_restaurants_table.php` | Créer la table des restaurants | À faire |
| `database/migrations/create_fonctions_table.php` | Créer la table des fonctions | À faire |
| `database/migrations/create_affectations_table.php` | Créer la table des affectations, les FK et contraintes | À faire |
| `app/Models/Collaborateur.php` | Ajouter le modèle collaborateur et ses relations | À faire |
| `app/Models/Restaurant.php` | Ajouter le modèle restaurant et ses relations | À faire |
| `app/Models/Fonction.php` | Ajouter le modèle fonction et ses relations | À faire |
| `app/Models/Affectation.php` | Ajouter le modèle affectation, relations et casts | À faire |

### Vérifications attendues

- Les migrations s'exécutent sans erreur.
- Les tables existent dans PostgreSQL.
- Les clés étrangères bloquent les affectations sans collaborateur, restaurant ou fonction existant.
- Le doublon strict d'affectation est refusé.
- Les modèles Eloquent chargent leurs relations.

### Compte rendu de fin de sprint

À compléter après codage et réunion de validation.

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

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `app/Models/Collaborateur.php` | Ajouter les capacités d'authentification | À faire |
| `app/Http/Controllers/AuthController.php` | Gérer affichage login, connexion et déconnexion | À faire |
| `app/Http/Requests/LoginRequest.php` | Valider le formulaire de connexion | À faire |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Refuser les accès non administrateurs | À faire |
| `bootstrap/app.php` | Déclarer le middleware si nécessaire | À faire |
| `config/auth.php` | Configurer le provider `collaborateurs` | À faire |
| `routes/web.php` | Ajouter les routes login/logout et protéger le back-office | À faire |
| `resources/views/auth/login.blade.php` | Créer la vue de connexion | À faire |
| `resources/views/layouts/app.blade.php` | Préparer le layout authentifié | À faire |

### Vérifications attendues

- Un visiteur non connecté est redirigé vers `/login`.
- Un collaborateur non administrateur est refusé.
- Un administrateur se connecte.
- La déconnexion invalide la session.
- Aucun mot de passe en clair n'est stocké.

### Compte rendu de fin de sprint

À compléter après codage et réunion de validation.

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

Statut initial : rien n'a encore été codé pour ce sprint.

À compléter après réalisation :

| Fichier | Ce qui a été ajouté ou modifié | Statut |
|---|---|---|
| À renseigner après codage | À renseigner après codage | À faire |

### Fichiers ajoutés ou modifiés prévus

| Fichier | Action attendue | Statut |
|---|---|---|
| `app/Http/Controllers/FonctionController.php` | Ajouter le CRUD des fonctions | À faire |
| `app/Http/Controllers/RestaurantController.php` | Ajouter le CRUD des restaurants | À faire |
| `app/Http/Controllers/CollaborateurController.php` | Ajouter le CRUD des collaborateurs | À faire |
| `app/Http/Requests/StoreFonctionRequest.php` | Valider la création de fonction | À faire |
| `app/Http/Requests/UpdateFonctionRequest.php` | Valider la modification de fonction | À faire |
| `app/Http/Requests/StoreRestaurantRequest.php` | Valider la création de restaurant | À faire |
| `app/Http/Requests/UpdateRestaurantRequest.php` | Valider la modification de restaurant | À faire |
| `app/Http/Requests/StoreCollaborateurRequest.php` | Valider la création de collaborateur | À faire |
| `app/Http/Requests/UpdateCollaborateurRequest.php` | Valider la modification de collaborateur | À faire |
| `resources/views/fonctions/*.blade.php` | Ajouter les vues fonctions | À faire |
| `resources/views/restaurants/*.blade.php` | Ajouter les vues restaurants | À faire |
| `resources/views/collaborateurs/*.blade.php` | Ajouter les vues collaborateurs | À faire |
| `routes/web.php` | Ajouter les routes CRUD protégées | À faire |

### Vérifications attendues

- Les fonctions sont listées, créées et modifiées.
- Les restaurants sont listés, créés, affichés et modifiés.
- Les collaborateurs sont listés, créés, affichés et modifiés.
- Les validations serveur refusent les données incomplètes ou invalides.
- Les recherches simples fonctionnent.

### Compte rendu de fin de sprint

À compléter après codage et réunion de validation.

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

À compléter après codage et réunion de validation.

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

À compléter après codage et réunion de validation.

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

À compléter après codage et réunion de validation.

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

À compléter après codage et réunion de validation.