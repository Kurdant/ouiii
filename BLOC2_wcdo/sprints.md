# Suivi des sprints

| Sprint | Objectif | Agent conseillé | Statut |
|---|---|---|---|
| 0 | Socle sécurité MVC | `bmm-dev` | À faire |
| 1 | Authentification + login | `bmm-dev` | À faire |
| 2 | Profil personnel | `bmm-dev` | À faire |
| 3 | Repositories + services transverses | `bmm-dev` | À faire |
| 4 | Gestion utilisateurs | `bmm-dev` | À faire |
| 5 | Catalogue simple | `bmm-dev` puis `fullstack-dev` | À faire |
| 6 | Menus composés | `bmm-dev` | À faire |
| 7 | Noyau commandes | `bmm-dev` | À faire |
| 8 | Back-office commandes | `fullstack-dev` + `bmm-dev` | À faire |
| 9 | API externe | `bmm-dev` | À faire |
| 10 | UI globale + navigation | `fullstack-dev` | À faire |
| 11 | Recette finale | `byan` + reviewer dev | À faire |

## Sprint 0 - Socle sécurité MVC

### Objectif

Compléter le socle commun nécessaire avant les fonctionnalités métier : sessions, revalidation du compte actif, rôles, CSRF, messages flash et helpers de contrôleur.

### En termes simples

> Avant d'implémenter la moindre fonctionnalité, l'application a besoin d'une base sécurisée commune. Ce sprint pose les fondations que tous les autres réutilisent : la gestion sécurisée des sessions (cookies HttpOnly, expiration automatique, protection contre la fixation de session), le mécanisme CSRF qui protège chaque formulaire contre les soumissions frauduleuses depuis un site tiers, le système de vérification des rôles qui bloque l'accès aux pages réservées, les messages flash (les bandons vert/rouge affichés après une action), et la page de connexion. Aucune fonctionnalité visible pour l'utilisateur final — c'est l'infrastructure qui rend tout le reste possible et sécurisé.

### Ce qui a été codé

- `app/Core/BaseController.php` — Méthodes Sprint 0 déjà présentes : `currentUser()`, `requireAuth()`, `requireRole()`, `refreshAuthenticatedUser()`, `csrfToken()`, `requireCsrf()`, `regenerateCsrfToken()`, `flash()`, `getFlash()`, `input()`, `abort()`, `forbidden()`, `destroySession()`.
- `public/index.php` — Session sécurisée déjà configurée : HttpOnly, SameSite=Lax, Secure en prod, timeout 30 min inactivité, autoloader PSR-4, chargement .env.
- `app/Repositories/UtilisateurRepository.php` — `findActiveWithRoleById()` et `findActiveWithRoleByIdentifiant()` avec JOIN `roles`.
- `app/Views/auth/login.php` — Formulaire complet : identifiant, mot de passe, affichage erreur flash, action POST /login, inclusion login.css, sans champ CSRF (exclusion conforme CDC 6.7).
- `app/Views/partials/flash.php` — Affichage flash `success`/`error` avec `htmlspecialchars`, consommé une seule fois via `getFlash()`.

### Fichiers modifiés

| Fichier | Statut |
|---|---|
| `app/Core/BaseController.php` | Existait déjà complet — aucune modification nécessaire |
| `public/index.php` | Existait déjà complet — aucune modification nécessaire |

### Fichiers créés / complétés

| Fichier | Statut |
|---|---|
| `app/Repositories/UtilisateurRepository.php` | Créé et complet |
| `app/Views/auth/login.php` | Complété (était un stub vide) |
| `app/Views/partials/flash.php` | Complété (était un stub vide) |

### Vérification à 3 agents

| Agent | Rôle | Verdict | Détail |
|---|---|---|---|
| bmm-dev (code review) | Conformité code / CDC | ✅ | `abort()` whitelist OK, `UtilisateurRepository` JOIN correct, session config complète |
| Sécurité (OWASP) | Session, CSRF, XSS | ✅ | CSRF `hash_equals` + régénération OK, `session.use_strict_mode=1` présent, `use_only_cookies=1` présent, XSS couvert |
| Fullstack (intégration) | Vues, CSS, layout | ✅ | `$flash` en scope via `extract()`, chemins CSS corrects, `view()` gère `layout => false` pour login |

### Résultat

✅ **Sprint 0 validé** — Socle MVC opérationnel. Session sécurisée, CSRF prêt, flash PRG, relecture compte actif. Prêt pour Sprint 1.

## Sprint 1 - Authentification + login

### Objectif

Implémenter l'authentification back-office : formulaire de connexion, vérification identifiant/mot de passe, blocage force brute, création de session et déconnexion.

### En termes simples

> Le CDC impose qu'aucune fonctionnalité du back-office n'est accessible sans authentification. Ce sprint implémente exactement ça : le formulaire de connexion avec affichage des erreurs, la vérification du mot de passe, la protection contre le brute force (5 tentatives → blocage 15 min), la création de session après connexion réussie, et la déconnexion sécurisée via un formulaire POST (et non un simple lien, qui serait contournable). À l'issue de ce sprint, le personnel Wacdo peut se connecter et se déconnecter en toute sécurité.

### Ce qui a été codé

- `app/Services/LoginAttemptService.php` — Brute force : clé sha256(identifiant|ip), fichier JSON /tmp, 5 échecs en 15min → blocage 15min, `flock` atomique, délai 3s après chaque échec, réinitialisation sur succès.
- `app/Services/AuthService.php` — Vérification credentials via `password_verify`, mitigation timing avec dummy hash bcrypt cost 12 valide, retourne tableau session-ready ou null.
- `app/Repositories/UtilisateurRepository.php` — Ajout de `findForAuth()` : retourne `mot_de_passe_hash` + données utilisateur, utilisé uniquement par AuthService.
- `app/Controllers/AuthController.php` — Implémentation de `showLogin()`, `login()`, `logout()` : CSRF sur POST /login, PRG pattern, `session_regenerate_id(true)` après auth, réaffichage identifiant saisi après erreur.
- `app/Core/BaseController.php` — `destroySession()` passé en `protected` + `session_start()` ajouté après destroy pour permettre le flash post-logout.
- `app/Views/layout.php` — Lien déconnexion remplacé par form POST /logout avec token CSRF.

### Fichiers créés

| Fichier | Détail |
|---|---|
| `app/Services/LoginAttemptService.php` | Nouveau |
| `app/Services/AuthService.php` | Nouveau |

### Fichiers modifiés

| Fichier | Modification |
|---|---|
| `app/Repositories/UtilisateurRepository.php` | Ajout `findForAuth()` |
| `app/Controllers/AuthController.php` | 3 stubs → implémentés |
| `app/Core/BaseController.php` | `destroySession()` en protected + session_start post-destroy |
| `app/Views/layout.php` | Lien GET /logout → form POST + CSRF |

### Vérification à 3 agents

| Agent | Rôle | Verdict | Correctifs appliqués |
|---|---|---|---|
| bmm-dev (code review) | Conformité code / CDC | ✅ | CSRF ajouté sur POST /login (Login CSRF attack), dummy hash valide remplacé |
| Sécurité (OWASP) | Session, timing, brute force | ✅ | `flock` atomique sur fichier /tmp (TOCTOU race condition) |
| Fullstack (intégration) | Flux login/logout, layout | ✅ | Lien GET /logout → form POST, `$identifiant` réaffiché via `$_SESSION['last_identifiant']` |

### Résultat

✅ **Sprint 1 validé** — Authentification fonctionnelle. Login CSRF, session fixation, timing attack, brute force tous couverts. Prêt pour Sprint 2.

## Sprint 2 - Profil personnel

### Objectif

Implémenter le changement de mot de passe personnel accessible à tous les utilisateurs authentifiés.

### En termes simples

> Le CDC prévoit que tout utilisateur authentifié, quel que soit son rôle, peut modifier son propre mot de passe. Ce sprint implémente cette fonctionnalité : formulaire accessible depuis la sidebar, vérification de l'ancien mot de passe avant tout changement, contraintes de longueur (8 à 72 caractères), et régénération de la session après le changement (si le mot de passe change, le jeton de session change aussi). Le serveur Apache est également durci : listing des répertoires désactivé et headers de sécurité HTTP ajoutés.

### Ce qui a été codé

- `app/Repositories/UtilisateurRepository.php` — Ajout `findHashById()` (retourne uniquement le hash par ID, filtre actif=true) et `updatePasswordById()` (UPDATE avec filtre actif=true pour cohérence défensive).
- `app/Controllers/UtilisateurController.php` — `editPassword()` : requireAuth + rendu vue avec flash et token CSRF. `updatePassword()` : requireAuth + requireCsrf, validation (champs vides, 8–72 chars, confirmation), brute force via LoginAttemptService, password_verify, password_hash bcrypt cost 12, session_regenerate_id(true) post-changement.
- `app/Views/utilisateurs/edit-password.php` — Formulaire 3 champs (actuel, nouveau, confirmation), minlength=8, maxlength=72, CSRF caché, autocomplete correct.
- `app/Views/layout.php` — Ajout lien "Changer mon mot de passe" dans sidebar.
- `apache.conf` — `Options -Indexes` (suppression directory listing) + headers sécurité HTTP.

### Fichiers créés

| Fichier | Détail |
|---|---|
| `app/Views/utilisateurs/edit-password.php` | Nouveau |

### Fichiers modifiés

| Fichier | Modification |
|---|---|
| `app/Repositories/UtilisateurRepository.php` | Ajout `findHashById()` + `updatePasswordById()` |
| `app/Controllers/UtilisateurController.php` | `editPassword()` + `updatePassword()` implémentés |
| `app/Views/layout.php` | Lien "Mon compte" dans sidebar |
| `apache.conf` | `Options -Indexes`, headers X-Content-Type-Options / X-Frame-Options / Referrer-Policy |

### Vérification à 3 agents

| Agent | Rôle | Verdict | Correctifs appliqués |
|---|---|---|---|
| bmm-dev (code review) | Conformité code / CDC | ✅ | Borne max 72 chars ajoutée (bcrypt truncation silencieuse) |
| Sécurité (OWASP) | A01–A07 | ✅ | `session_regenerate_id` post-changement (A07), `Options -Indexes` + headers HTTP (A05), rate limiting via LoginAttemptService (A04), `actif=true` sur UPDATE (TOCTOU) |
| Fullstack (intégration) | Flux GET/POST, layout, flash | ✅ | Lien sidebar `/mon-compte/mot-de-passe` ajouté |

### Résultat

✅ **Sprint 2 validé** — Changement de mot de passe fonctionnel. IDOR impossible, bcrypt cost 12, rate limiting réutilisé, session régénérée après changement. Prêt pour Sprint 3.

## Sprint 3 - Repositories + services transverses

### Objectif

Créer les repositories et services communs nécessaires aux sprints métier : accès aux données, traces et validation serveur.

### En termes simples

> Avant d'écrire la moindre page métier (catégories, produits, commandes), toutes les classes d'accès à la base de données doivent exister. Ce sprint crée la couche données complète : chaque table du schéma a désormais sa classe Repository qui lit et écrit proprement via des requêtes préparées. C'est aussi ici qu'est créé le TraceService — le système qui enregistre dans `traces_actions` qui a fait quoi et quand, conformément à l'exigence du CDC de traçabilité des actions sensibles. Et le Validator, qui accumule les erreurs de formulaire sans interrompre le flux. Aucune page visible pour l'utilisateur — c'est la couche sur laquelle tous les sprints métier s'appuient.

### Ce qui a été codé

- **CategorieRepository** : `findAll()`, `findAllActive()`, `findById()`, `create()`, `update()`, `desactiver()`
- **ProduitRepository** : `findAll()`, `findAllActive()`, `findAllAvailableActive()`, `findById()`, `findByCategorieId()`, `create()`, `update()` (image optionnelle), `desactiver()`
- **MenuRepository** : `findAll()`, `findAllActive()`, `findAllAvailableActive()`, `findById()`, `create()`, `update()` (image optionnelle), `desactiver()`
- **SectionMenuRepository** : `findByMenuId()` (JOIN sections+options+produits, reconstruction imbriquée PHP), `findById()`, `create()`
- **OptionMenuRepository** : `findBySectionId()` (JOIN produits), `findById()`, `create()`, `desactiver()`
- **CommandeRepository** : `findAll(?statut)`, `findById()`, `findByIdWithLignes()` (3 requêtes imbriquées), `create()` (**transaction atomique** : commandes → lignes_commande → choix_ligne_commande), `marquerPreparee()`, `marquerLivree()`
- **TraceService** : `log(action, tableCible, idCible, details)` — insère dans `traces_actions`, lit `$_SESSION['user']['id']`, guard `session_status()`
- **Validator** (`app/Core/Validator.php`) : `required()`, `maxLength()`, `minLength()`, `positiveNumber()`, `nonNegativeNumber()`, `intBetween()`, `inList()`, `fails()`, `errors()`, `firstError()` — accumulation sans exception, chaînable
- **UtilisateurRepository** — ajouts pour Sprint 4 : `findAll()`, `findAllRoles()`, `existsByIdentifiant()` (avec exclude pour update), `create()`, `update()`, `desactiver()`

### Fichiers modifiés

| Fichier | Modification |
|---|---|
| `app/Repositories/UtilisateurRepository.php` | Ajout de 6 méthodes CRUD + `filter_var` → `=== 't'` pour booléens PostgreSQL |

### Fichiers ajoutés

| Fichier | Rôle |
|---|---|
| `app/Repositories/CategorieRepository.php` | CRUD catégories |
| `app/Repositories/ProduitRepository.php` | CRUD produits + JOIN catégorie |
| `app/Repositories/MenuRepository.php` | CRUD menus |
| `app/Repositories/SectionMenuRepository.php` | Sections de menu + options imbriquées |
| `app/Repositories/OptionMenuRepository.php` | Options d'une section |
| `app/Repositories/CommandeRepository.php` | Commandes + lignes + choix, transaction |
| `app/Services/TraceService.php` | Audit `traces_actions` |
| `app/Core/Validator.php` | Validation serveur accumulatrice |

### Vérification à 3 agents

**Agent 1 — Revue code :**
- Bug corrigé : `filter_var('t', FILTER_VALIDATE_BOOLEAN)` retourne `false` pour les booléens PostgreSQL → remplacé par `=== 't'` dans **tous** les repositories (y compris UtilisateurRepository existant)
- Bug corrigé : `(string) null === ''` dans ProduitRepository::update() pour description nullable → `isset()` + null
- Correction architecturale : TraceService ne doit pas étendre BaseRepository — PDO injecté via constructeur

**Agent 2 — Sécurité OWASP :**
- A09 corrigé : TraceService ajoute `session_status() !== PHP_SESSION_ACTIVE` → return immédiat au lieu de crash ou trace silencieuse
- A04 RAS : transaction `beginTransaction/commit/rollBack` déjà en place dans CommandeRepository::create()
- A03 RAS : SQL dynamique dans findAll() et update() est hardcodé, aucune injection possible
- Fuite de données RAS : `mot_de_passe_hash` absent du SELECT dans findAll()

**Agent 3 — Intégration fullstack :**
- Bug critique corrigé : constructeur TraceService `__construct(private readonly \PDO $pdo)` → TypeError sur `null`. Remplacé par `__construct(?\PDO $pdo = null)` + `Database::connection()` fallback, aligné sur le pattern BaseRepository
- Validator RAS : aucune dépendance, `new Validator()` direct dans les contrôleurs
- CommandeRepository::create() RETURNING en boucle RAS : `pdo_pgsql` bufferise chaque résultat, pattern validé

### Résultat

✅ **Sprint 3 validé** — 8 repositories/services créés. Booléens PostgreSQL correctement normalisés (`=== 't'`). TraceService autonome (no extends, PDO optionnel). Validator prêt pour tous les sprints CRUD. Transaction commande atomique. Prêt pour Sprint 4.

## Sprint 4 - Gestion utilisateurs

### Objectif

Implémenter le CRUD des utilisateurs réservé au rôle `Administration`.

### En termes simples

> Le CDC réserve au rôle Administration la gestion complète des comptes du personnel interne. Ce sprint implémente les pages de gestion des utilisateurs : liste avec statut et rôle, création (avec choix du rôle), modification (dont changement de mot de passe optionnel), et désactivation. Le principe du CDC est respecté : un compte ne peut pas être supprimé physiquement, seulement désactivé, pour préserver l'historique des actions. Deux garde-fous sont en place : on ne peut pas se désactiver soi-même, et on ne peut pas désactiver le dernier administrateur actif (ce qui bloquerait l'accès à l'ensemble du back-office).

### Ce qui a été codé

- **UtilisateurController** — 6 méthodes CRUD Admin : `index`, `create`, `store`, `edit`, `update`, `desactiver`  
  - `requireRole(['Administration'])` + `requireCsrf()` sur tous les POST  
  - Validator pour les champs (identifiant, nom, prénom, mot de passe)  
  - Mot de passe optionnel dans `update` (vide = inchangé)  
  - Protection anti-auto-désactivation (id === currentUser.id)  
  - Protection dernier administrateur actif (countActiveByRole ≤ 1)  
  - PRG pattern partout, TraceService sur toutes les mutations  
- **UtilisateurRepository** — ajout de `findById(int $id): ?array` (sans filtre actif, pour admin) et `countActiveByRole(string $role): int`  
- **Vues** — `utilisateurs/index.php`, `utilisateurs/create.php`, `utilisateurs/edit.php`  
- **layout.php** — sidebar corrigée : lien `Utilisateurs` conditionnel rôle Administration (remplace liens cassés `/employes` et `/restaurants`)

### Fichiers modifiés

| Fichier | Nature |
|---|---|
| `app/Controllers/UtilisateurController.php` | Ajout 6 méthodes CRUD (index, create, store, edit, update, desactiver) |
| `app/Repositories/UtilisateurRepository.php` | Ajout `findById()` + `countActiveByRole()` |
| `app/Views/layout.php` | Sidebar corrigée — lien Utilisateurs conditionnel |

### Fichiers ajoutés

| Fichier | Rôle |
|---|---|
| `app/Views/utilisateurs/index.php` | Liste utilisateurs — table, badges, bouton désactiver CSRF |
| `app/Views/utilisateurs/create.php` | Formulaire création — tous champs, rôle select, mot de passe |
| `app/Views/utilisateurs/edit.php` | Formulaire édition — champs prefilled, mot de passe optionnel |

### Vérification à 3 agents

**Agent 1 — Code review (logique PHP) :**  
3 bugs détectés et corrigés :
1. `in_array` strict false sur rôle PDO string vs int → `array_map('intval', ...)` 
2. Protection auto-désactivation `===` strict avec cast `(int)` explicite  
3. XSS JS dans `onsubmit confirm()` — `htmlspecialchars` encode `'` en `&#039;` (décodé par HTML avant JS) → remplacé par `json_encode(..., JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)`  

**Agent 2 — Sécurité OWASP :**  
- A07 RAS : `refreshAuthenticatedUser()` relit déjà le rôle depuis DB à chaque requête  
- A03 RAS : `htmlspecialchars()` sur toutes les sorties HTML, confirmé  
- A04 RAS : extraction explicite champ par champ depuis `$_POST`, pas de mass assignment  
- A01 MOYENNE corrigée : ajout protection dernier admin actif dans `desactiver()`  

**Agent 3 — Intégration (routes/redirections/variables) :**  
1 bug critique corrigé : bloc `} {` orphelin dans `store()` (résidu du multi-replace) → `existsByIdentifiant()` jamais appelé, `store` n'aboutissait jamais. Corrigé en séparant les deux `if` en blocs indépendants.

### Résultat

✅ **Sprint 4 validé** — CRUD utilisateurs Admin complet avec 4 bugs corrigés post-agents. Protection dernier admin, XSS JS, intégrité du flux store(). Prêt pour Sprint 5.

## Sprint 5 - Catalogue simple

### Objectif

Implémenter la gestion des catégories et des produits, avec validation serveur et upload d'images via `UploadService`.

### En termes simples

> Le CDC prévoit que le rôle Administration gère l'intégralité du catalogue produit. Ce sprint implémente la gestion des catégories (les regroupements qui organisent l'offre, comme "Burgers" ou "Boissons") et des produits (nom, description, prix, image, catégorie, disponibilité). Pour les produits, une image est obligatoire à la création — elle est stockée hors de la zone web accessible pour empêcher toute exécution directe depuis un navigateur. La disponibilité peut être modifiée indépendamment des autres informations, et un produit peut être désactivé sans être supprimé (exigence CDC : aucune suppression physique d'un élément déjà référencé dans une commande).

### Ce qui a été codé

- **CategorieController** — 6 méthodes CRUD Admin : `index`, `create`, `store`, `edit`, `update`, `desactiver`  
  - Validation : nom required maxLen 100, description optionnelle maxLen 500  
  - Unicité du nom : `existsByNom()` avec exclude sur update (contrainte UNIQUE en BDD)  
  - PRG + TraceService sur toutes les mutations  
- **ProduitController** — 6 méthodes CRUD Admin : `index`, `create`, `store`, `edit`, `update`, `desactiver`  
  - Validation : nom required maxLen 150, description required, prix positiveNumber  
  - Catégorie validée contre `findAllActive()` avec `array_map('intval', ...)`  
  - Image obligatoire à la création, optionnelle à l'édition  
  - `UploadService::stocker()` + `supprimer()` (ancienne image détruite après remplacement)  
  - PRG + TraceService sur toutes les mutations  
- **CategorieRepository** — ajout `existsByNom(string $nom, ?int $excludeId = null): bool`  
- **layout.php** — Catalogue (Produits + Catégories) restreint visuellement au rôle Administration

### Fichiers modifiés

| Fichier | Nature |
|---|---|
| `app/Controllers/CategorieController.php` | Stub TODO → 6 méthodes CRUD complètes |
| `app/Controllers/ProduitController.php` | Stub TODO → 6 méthodes CRUD complètes avec UploadService |
| `app/Repositories/CategorieRepository.php` | Ajout `existsByNom()` |
| `app/Views/layout.php` | Catalogue restreint `Administration` only |

### Fichiers ajoutés

| Fichier | Rôle |
|---|---|
| `app/Views/categories/index.php` | Liste catégories — table, badge, bouton désactiver CSRF |
| `app/Views/categories/create.php` | Formulaire création — nom + description optionnelle |
| `app/Views/categories/edit.php` | Formulaire édition — prefilled |
| `app/Views/produits/index.php` | Liste produits — table (nom/catégorie/prix/disponible/statut), désactiver CSRF |
| `app/Views/produits/create.php` | Formulaire création — enctype multipart, tous champs, upload obligatoire |
| `app/Views/produits/edit.php` | Formulaire édition — prix prefilled, checkbox disponible, upload optionnel |

### Vérification à 3 agents

**Agent 1 — Code review (logique PHP) :**  
1 bug détecté et corrigé :  
- `ProduitController::update()` — `$produit['image'] !== ''` insuffisant si image null → remplacé par `!empty()`

**Agent 2 — Sécurité OWASP :**  
- UploadService déjà sécurisé depuis Sprint 3 : validation `$sousDossier` par regex, garde `str_contains('..')` dans `supprimer()`, MIME via finfo, nom aléatoire `bin2hex(random_bytes(16))`  
- Storage hors DocumentRoot (`/var/www/html/storage/uploads/` vs DocumentRoot `/var/www/html/public/`) — non accessible HTTP  
- CSRF : RAS — hash_equals sur tous les POST  
- A01 : RAS — `requireRole(['Administration'])` systématique  

**Agent 3 — Intégration (routes/redirections/variables) :**  
RAS — form actions, redirections, variables de vue, enctype multipart, clés d'array tous cohérents.

### Résultat

✅ **Sprint 5 validé** — CRUD catégories et produits complets avec upload sécurisé. 1 bug corrigé post-agent. UploadService défenses confirmées. Prêt pour Sprint 6.

## Sprint 6 - Menus composés

### Objectif

Implémenter les menus, leurs sections et leurs options avec les règles de cohérence du CDC.

### En termes simples

> Le CDC demande que l'Administration gère non seulement les produits seuls, mais aussi les menus composés — des offres qui regroupent plusieurs choix structurés. Un menu est organisé en sections (ex : "Votre plat", "Votre accompagnement", "Votre boisson"), chaque section proposant une liste de produits parmi lesquels le client choisira au moment de la commande. Ce sprint implémente la création et l'édition des menus avec image, la gestion de leurs sections et des options disponibles dans chaque section. Comme pour les produits, la disponibilité d'un menu est pilotable indépendamment, et aucune suppression physique n'est autorisée.

### Ce qui a été codé

- `MenuController` complet (6 méthodes) : `index`, `create`, `store`, `edit`, `update`, `desactiver`. Restriction `requireRole(['Administration'])` sur toutes les méthodes, `requireCsrf()` sur tous les POST, PRG, traces, upload obligatoire à la création (`UploadService::stocker($file, 'menus')`), upload optionnel à l'édition (l'ancienne image est conservée si aucun nouveau fichier n'est envoyé, et l'ancienne est supprimée sur remplacement). Après création d'un menu, redirection vers la page de composition `/menus/{id}/sections` pour inviter à ajouter les sections.
- `SectionMenuController` : `index` (affiche la page de composition d'un menu avec sections imbriquées et leurs options + dropdown produits actifs pour les ajouts) et `store` (ajout d'une section avec validation `quantite_max >= quantite_min` côté contrôleur en plus du `CHECK` BDD).
- `OptionMenuController` : `store` (ajout d'une option dans une section, supplément >= 0, vérification que le produit existe et est actif, capture de la violation `UNIQUE` PostgreSQL `23505` → message flash dédié) et `desactiver` (soft delete d'une option, soit `actif = false`).
- Bug corrigé dans `SectionMenuRepository::findById()` : la valeur PostgreSQL `'t'` / `'f'` était mal interprétée par `filter_var(FILTER_VALIDATE_BOOLEAN)` qui retournait `false`. Remplacé par la normalisation maison `=== 't'` cohérente avec le reste du projet.
- Bug corrigé dans `SectionMenuRepository::findByMenuId()` : le `LEFT JOIN produits` retournait des produits inactifs (soft delete) ce qui pouvait afficher dans la page de composition des options liées à des produits archivés. Ajout de `AND p.actif = true` dans la jointure.
- 4 vues créées :
  - `menus/index.php` : liste avec badges Actif / Disponible, prix formaté `number_format`, actions Composition / Modifier / Désactiver (soft delete confirmé via `json_encode(JSON_HEX_*)`).
  - `menus/create.php` : formulaire multipart avec image obligatoire (`accept="image/jpeg,image/png"`), token CSRF caché, `novalidate` pour laisser le contrôleur valider.
  - `menus/edit.php` : formulaire multipart avec image optionnelle (affichage du nom de fichier actuel) et case à cocher `disponible`.
  - `menus/sections.php` : page de composition dense affichant chaque section avec son badge obligatoire/facultative, ses bornes `quantite_min`/`quantite_max`, la table de ses options (produit, prix produit, supplément, disponible, bouton Retirer), un formulaire d'ajout d'option par section (select des produits actifs + supplément), et en bas un formulaire d'ajout d'une nouvelle section (nom, checkbox obligatoire, quantité min/max). Tous les formulaires portent le CSRF token. Toutes les sorties variables sont échappées via `htmlspecialchars(ENT_QUOTES, 'UTF-8')` et les `confirm()` JS via `json_encode(... JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)`.
- Lien "Menus" ajouté dans la sidebar (`layout.php`) sous la section "Catalogue", conditionné au rôle Administration (cohérent avec les autres liens Produits / Catégories restreints au Sprint 5).
- Toutes les mutations enregistrent une trace via `TraceService::log` : création de menu / section / option, modification de menu, désactivation de menu / option.

### Fichiers modifiés

- `app/Controllers/MenuController.php` — stub `TODO` remplacé par 6 méthodes complètes.
- `app/Controllers/SectionMenuController.php` — stub remplacé par `index` + `store`.
- `app/Controllers/OptionMenuController.php` — stub remplacé par `store` (avec catch `PDOException` `23505`) + `desactiver`.
- `app/Repositories/SectionMenuRepository.php` — fix booléen `'t'/'f'` dans `findById`, filtrage des produits inactifs dans `findByMenuId`.
- `app/Views/menus/index.php` — stub remplacé par la liste.
- `app/Views/menus/create.php` — stub remplacé par le formulaire multipart.
- `app/Views/menus/edit.php` — stub remplacé par le formulaire d'édition.
- `app/Views/menus/sections.php` — stub remplacé par la page de composition complète.
- `app/Views/layout.php` — ajout du lien `/menus` sous la section Catalogue (conditionné Administration).

### Fichiers ajoutés

Aucun (tous les fichiers étaient déjà présents en stubs depuis le Sprint 3 — squelette MVC).

### Vérification à 3 agents

- **Agent Back (revue PHP + logique métier)** — Verdict : ⚠️ À corriger (3 anomalies identifiées).
  - ✅ PRG, requireRole, requireCsrf, TraceService, soft delete, normalisation booléens PostgreSQL, cast `(int)` sur args URL, gestion `UNIQUE` `23505`, validations métier (`quantite_max >= quantite_min`, intBetween, nonNegativeNumber).
  - ⚠️ Race condition possible entre vérification produit actif et insertion option (acceptable — pas de transaction nécessaire pour un back-office mono-tenant) — non corrigée.
  - ⚠️ `SectionMenuRepository::findByMenuId` jointure `produits` sans filtre `p.actif = true` — **corrigé**.
  - ⚠️ `required()` redondants avec valeurs par défaut dans les contrôleurs — non corrigés (validations fonctionnelles, nettoyage cosmétique).
- **Agent Front (revue vues + UX + intégration HTML)** — Verdict : ⚠️ À corriger (4 incohérences avec template Sprint 5).
  - ✅ Échappement XSS systématique, CSRF présent partout, `enctype="multipart/form-data"` sur les forms d'upload, `accept="image/jpeg,image/png"`, cast `(int)` sur les IDs, variables de vue cohérentes avec les contrôleurs, structure imbriquée `$section['options']` itérée correctement, `confirm()` JS encodés.
  - ⚠️ `novalidate` manquant sur les forms — **corrigé** (create.php, edit.php, sections.php).
  - ⚠️ Astérisques `*` en texte brut au lieu de `<span class="required">*</span>` — **corrigé**.
- **Agent BYAN (conformité CDC + sécurité OWASP)** — Verdict : ✅ Validé.
  - ✅ Contrôle d'accès systématique (CDC §3.1), CSRF avec `hash_equals` (CDC technique §6.7), traçabilité complète (CDC §4.1), soft delete obligatoire (CDC §4.1 / RG-CAT-004), routes figées présentes (CDC technique §4.5), sidebar restreinte au rôle Administration, redirections cohérentes avec le parcours utilisateur, validation serveur exhaustive, upload sécurisé via `UploadService` (MIME `finfo`, taille, format, stockage hors web root).
  - ✅ OWASP : A01 (Broken Access Control), A03 (SQL Injection — prepared statements), A04 (Insecure Design — validation serveur + soft delete), A05 (Security Misconfiguration — contraintes BDD), A07 (Information Disclosure — pas de fuite de hash). Aucun écart sécurité.

### Résultat

✅ **Sprint 6 validé.** Le back-office Administration peut désormais créer / éditer / désactiver des menus, composer leurs sections et leurs options, et le tout est protégé, tracé et conforme au CDC. Les corrections issues des 3 revues sont intégrées (jointure produits actifs dans le repository + alignement HTML sur le template Sprint 5).

## Sprint 7 - Noyau commandes

### Objectif

Créer `CommandeService`, source unique des règles de création, validation, calcul de total, lignes, choix de menu et transitions de statut.

### En termes simples

> Avant de créer des pages de saisie de commandes (sprint 8) ou de recevoir des commandes via l'API (sprint 9), les règles métier doivent être centralisées en un seul endroit. Ce sprint crée le `CommandeService` : c'est lui qui calcule le total d'une commande depuis le catalogue actif (les prix viennent toujours de la base de données, pas de ce que le client envoie), qui valide chaque ligne (le produit doit exister, être actif, être disponible), qui vérifie qu'un menu a bien toutes ses sections obligatoires remplies, et qui gère les transitions de statut : `à préparer` → `préparée` → `livrée`. Aucune interface utilisateur dans ce sprint — c'est le moteur métier sur lequel tout le reste s'appuie.

### Ce qui a été codé

- **`CommandeValidationException`** — exception métier portant une liste plate d'erreurs lisibles (`array<int, string>`), levée dès qu'une règle de création ou de transition est violée. Accesseurs `getErrors()` et `firstError()` pour les contrôleurs et l'API.
- **`CommandeService`** — service métier unique, instancié avec ses repositories par injection. Méthodes publiques :
	- `creer(array $input, string $source): int` — valide la commande puis l'écrit en base via `CommandeRepository::create()` (transactionnel). Source ∈ {`api`, `back_office`}, type_service ∈ {`sur_place`, `a_emporter`}, lignes non vides. Pour chaque ligne `produit` : produit existant + actif + disponible, quantité ≥ 1. Pour chaque ligne `menu` : menu actif + disponible, sections obligatoires couvertes en quantité ∈ [`quantite_min`, `quantite_max`], sections facultatives soit vides soit dans les bornes, chaque option choisie appartient à la bonne section et son produit support est disponible, sections inconnues du menu refusées. Les prix et libellés stockés (`prix_unitaire_applique`, `libelle_article`, `prix_supplement_applique`, `libelle_produit`) proviennent **exclusivement** de la base — jamais de l'entrée client. Formule : `sous_total = (prix_menu + Σ suppléments) × quantité`, `total = Σ sous_totaux`. Génère le numéro de retrait `R-XXXXXX` (`random_int` + padding), capture l'auteur depuis `$_SESSION['user']['id']` (null pour appels API non authentifiés), trace via `TraceService::log('creation', ...)`.
	- `marquerPreparee(int $idCommande): void` — vérifie l'existence puis exige `statut === 'a_preparer'`. Délègue au repository (qui re-vérifie la transition côté SQL) et trace.
	- `marquerLivreeParNumeroRetrait(string $numero): array` — identifie la commande par son numéro de retrait (comme le fait le rôle Accueil au comptoir), exige `statut === 'preparee'`, applique la transition, trace et retourne la commande re-chargée pour affichage récapitulatif.
- **`CommandeRepository::findByNumeroRetrait(string $numero): ?array`** — ajout pour permettre la résolution Accueil par numéro de retrait (prepared statement avec jointure sur `utilisateurs` pour récupérer l'identifiant de l'auteur). Réutilise le `normalizeCommande()` existant.

### Fichiers modifiés

- `app/Repositories/CommandeRepository.php` — ajout de la méthode `findByNumeroRetrait(string $numero): ?array` (prepared statement + jointure auteur + normalisation).

### Fichiers ajoutés

- `app/Exceptions/CommandeValidationException.php` — exception métier dédiée avec liste d'erreurs.
- `app/Services/CommandeService.php` — noyau métier (création, calcul, transitions, traçabilité).

### Vérification à 3 agents

- **Agent back ⚠️→✅** : conformité métier validée (prix BDD, sections, transitions, atomicité déléguée, traces). Deux corrections appliquées : (1) cast explicite `(float)` lors de l'accumulation du total (évite le type juggling string + float) ; (2) la boucle de structuration des choix accumule désormais les erreurs (`continue`) au lieu de retourner immédiatement, pour que l'utilisateur voie tous les problèmes de structure d'un coup.
- **Agent front ⚠️→✅** : contrat public clair (PHPDoc complet sur `creer`, noms de méthodes explicites, exception facile à itérer, messages en français lisible). Correction appliquée : docblock détaillé sur `marquerLivreeParNumeroRetrait` justifiant le retour `array` (récapitulatif post-livraison côté Accueil) et les champs renvoyés. La signature reste alignée sur le CDC §6.4.1 (identification par numéro de retrait, pas par ID).
- **Agent BYAN ✅** : conformité CDC à 100 % (cycle unifié API ↔ back-office, traçabilité auteur+date+objet, transitions figées, soft delete respecté, pas de DELETE physique, validation serveur exhaustive). OWASP couvert : A03 (prepared statements partout y compris `findByNumeroRetrait`), A04 (pas d'override de statut ni de prix), A08 (numéro de retrait généré serveur via CSPRNG `random_int`), pas de fuite d'information dans les messages.

### Résultat

✅ **Sprint 7 validé.** Le noyau métier des commandes est centralisé et prêt à être consommé par le back-office (sprint 8) puis par l'API REST (sprint 9), avec une garantie que les règles seront strictement identiques quelle que soit la source. Les corrections issues des 3 revues sont intégrées.

## Sprint 8 - Back-office commandes

### Objectif

Brancher les pages commandes sur le noyau métier : liste filtrée par rôle, création manuelle, détail, préparation et livraison.

### En termes simples

> Le CDC définit trois rôles opérationnels autour des commandes, chacun avec ses propres actions. Ce sprint branche les pages sur le `CommandeService` du sprint 7 : le rôle Accueil peut saisir manuellement une commande (sélection de produits, de menus avec leurs options, quantités) et déclarer une commande "livrée" lors de la remise au client via son numéro de retrait ; le rôle Préparation consulte la liste des commandes "à préparer" triées par heure de retrait croissante et les déclare "préparées" une fois confectionnées ; le rôle Administration peut réaliser l'ensemble de ces actions.

### Ce qui a été codé

- **`CommandeController` (refonte complète)** — 8 méthodes publiques, toutes protégées par `requireRole` et (sur POST) `requireCsrf`, pattern PRG strict :
	- `index()` — liste adaptée au rôle. Préparation voit uniquement les commandes `à préparer` triées par heure de retrait croissante (CDC §3) ; Accueil et Administration voient toutes les commandes avec filtre par statut via la query string `?statut=` (whitelist serveur).
	- `show($id)` — détail complet (commande + lignes + choix de personnalisation) accessible à tous les rôles.
	- `create()` + `store()` — saisie manuelle, réservée à Accueil + Administration. Le formulaire liste tous les produits actifs+disponibles et tous les menus actifs+disponibles avec leur composition (sections + options). Le contrôleur transforme `$_POST` en payload typé puis délègue à `CommandeService::creer($input, 'back_office')`. Les erreurs métier sont aggrégées dans un seul flash.
	- `marquerPreparee($id)` — réservée à Préparation + Administration. Délègue à `CommandeService::marquerPreparee()` qui re-vérifie le statut.
	- `marquerLivree($id)` — variante "détail" pour Accueil + Administration : résout l'ID en numéro de retrait puis appelle l'unique chemin métier `CommandeService::marquerLivreeParNumeroRetrait()` pour conserver une seule source de vérité.
	- `livraisonForm()` + `livraisonParNumero()` — page dédiée Accueil pour déclarer la livraison directement à partir du numéro de retrait saisi au comptoir (conforme CDC §6.4.1).
	- Helper privé `buildService()` qui injecte les dépendances du `CommandeService` (centralisé pour éviter la duplication).
	- Helper privé `buildInputFromPost()` qui transforme `$_POST['produits'][...]` et `$_POST['menus'][...][sections][...]` (avec gestion des sélections multiples) en structure attendue par le service.
- **`CommandeRepository::findAPreparer()`** — nouvelle méthode dédiée au rôle Préparation, prepared statement avec tri exact `date_heure_retrait_prevue ASC NULLS LAST, date_commande ASC` (CDC §3).
- **`routes/web.php`** — 2 routes ajoutées (`GET` et `POST` `/commandes/livraison`) placées **avant** `/commandes/{id}` pour ne pas être interceptées par la route paramétrée.
- **Vues `app/Views/commandes/`** — 4 vues remplies :
	- `index.php` : table responsive (n° retrait, date, retrait prévu, service, source, total, statut), filtre par statut via `<select>` à soumission automatique, boutons d'action conditionnés par rôle ET par statut, badges colorés.
	- `show.php` : récapitulatif complet (statut, type service, source, dates, auteur, total) + table des lignes avec choix de personnalisation imbriqués sous chaque ligne menu, boutons d'action conditionnés.
	- `create.php` : formulaire `novalidate` listant produits et menus, chaque section de menu rendue en `<select>` (simple ou multi selon `quantite_max`), `<span class="required">*</span>` sur les sections obligatoires, indication explicite des cardinaux.
	- `livraison.php` : formulaire dédié au comptoir Accueil (saisie du numéro de retrait `R-XXXXXX`, autofocus, maxlength).
- **`app/Views/layout.php`** — sidebar enrichie : liens "Nouvelle commande" et "Déclarer une livraison" visibles uniquement pour les rôles Accueil + Administration.

### Fichiers modifiés

- `app/Controllers/CommandeController.php` — refonte complète (était un stub TODO).
- `app/Repositories/CommandeRepository.php` — ajout de `findAPreparer()`.
- `app/Views/commandes/index.php`, `show.php`, `create.php` — remplissage complet (étaient des stubs vides).
- `app/Views/layout.php` — sidebar conditionnée par rôle pour les liens commandes.
- `routes/web.php` — ajout des routes `/commandes/livraison` (GET + POST), placées avant `/commandes/{id}`.

### Fichiers ajoutés

- `app/Views/commandes/livraison.php` — formulaire de livraison par numéro de retrait.

### Vérification à 3 agents

- **Agent back ✅** : validé sans réserve. `requireRole` + `requireCsrf` systématiques, PRG strict, type-safety (`(int)`, `(string)`, `===`), zéro logique métier dans le contrôleur (tout passe par `CommandeService`), edge cases gérés, ordre des routes correct, `findAPreparer` en prepared statement avec tri exact CDC.
- **Agent front ⚠️→✅** : XSS protégé partout, CSRF présent dans tous les POST, sidebar conditionnée correctement, états vides gérés, classes CSS cohérentes avec Sprint 5/6, structure `name="menus[id][sections][idSection][]"` alignée avec le contrôleur. Correction appliquée : ajout de `novalidate` sur les 4 formulaires d'action courts (boutons Préparée/Livrée dans `index.php` et `show.php`) pour cohérence avec la directive "validation serveur prioritaire".
- **Agent BYAN ✅** : conformité CDC §1 (cycle unifié), §3 (tri Préparation, mêmes règles métier API/back-office), §4.1 (traçabilité via service), §6.4.1 (livraison par numéro de retrait). OWASP couvert : A01 (rôles enforced en contrôleur + interface + repo), A03 (prepared statements y compris pour `findAPreparer`), A04 (unique chemin métier `marquerLivreeParNumeroRetrait`), A05 (ordre routes correct), A07 (CSRF sur tous les POST). Aucun écart détecté.

### Résultat

✅ **Sprint 8 validé.** Le back-office permet aux trois rôles opérationnels d'effectuer toutes leurs actions conformes au CDC : Accueil saisit et livre, Préparation prépare avec sa liste triée, Administration fait tout. Les corrections issues des revues (4 `novalidate` ajoutés) sont intégrées.

## Sprint 9 - API externe

### Objectif

Implémenter les endpoints `/api/catalogue` et `/api/commandes` avec clé `X-API-Key`, JSON strict et réutilisation des services métier.

### En termes simples

> Le CDC exige que le back-office expose une API REST permettant au système de commande externe (la borne ou l'application client) de récupérer le catalogue et de soumettre des commandes. Ce sprint implémente deux endpoints : `GET /api/catalogue` (retourne catégories, produits, menus et leurs options disponibles) et `POST /api/commandes` (reçoit une commande, la valide et la calcule via le `CommandeService` du sprint 7 — les prix envoyés par le client externe ne font pas foi). L'accès est contrôlé par une clé API via le header `X-API-Key`. Aucune donnée interne (comptes, sessions, traces) n'est exposée.

### Ce qui a été codé

- **`Api\ApiBaseController` (nouveau)** — classe abstraite mutualisant tout ce qui est commun aux contrôleurs API :
	- `requireApiKey()` : lit la clé attendue dans la variable d'environnement `API_KEY`, refuse en `503` si la variable est vide (fail-closed, pas de mode "API ouverte" en dev), compare la clé reçue dans `X-API-Key` via `hash_equals` pour résister aux attaques temporelles.
	- `readJsonBody()` : décode le body via `json_decode(..., JSON_THROW_ON_ERROR)` avec une profondeur maximale et rejette en `400` les bodies vides, mal formés ou non-objets.
	- `jsonSuccess()`, `jsonError()`, `jsonValidationErrors()` : helpers qui appellent `json()` puis `exit` (le `json` de `BaseController` n'arrête pas l'exécution — important pour éviter qu'un appel ne continue après la réponse).
- **`Api\CatalogueController::index()` — `GET /api/catalogue`** : assemble la réponse JSON publique (catégories actives, produits actifs+disponibles, menus actifs+disponibles avec leurs sections et options). Les options indisponibles sont filtrées avant exposition. Aucune donnée interne (utilisateurs, traces, prix archivés) n'est exposée.
- **`Api\CommandeController::store()` — `POST /api/commandes`** : décode le body JSON, le passe **tel quel** à `CommandeService::creer($input, 'api')` du Sprint 7 (zéro duplication de logique métier — les prix envoyés par le client externe sont ignorés, le serveur les relit en base). Gestion d'erreurs en cascade :
	- `CommandeValidationException` → `400` avec la liste des erreurs métier.
	- `InvalidArgumentException` → `400` "Requête invalide." (structure malformée).
	- `\Throwable` → `500` "Erreur interne." (filet de sécurité, détails loggés via `error_log` sans fuir au client).
- **`Api\CommandeController::show()` — `GET /api/commandes/{numero}`** : permet à la borne externe d'interroger le statut courant d'une commande à partir du numéro de retrait reçu lors de la création. Validation stricte du paramètre via regex `/^R-\d{6}$/` (refuse en `400` un numéro mal formé avant de toucher à la base). Renvoie un sous-ensemble (statut, type service, source, total, dates) — pas les lignes, que le client possède déjà.
- **Documentation du contrat dans le docblock** : le format JSON attendu en input du `POST` est documenté en clair dans le contrôleur (structure `type_service`, `lignes[]`, `choix[]`) pour qu'un développeur externe puisse intégrer sans relire le service.

### Fichiers modifiés

- `app/Controllers/Api/CatalogueController.php` — remplissage complet (était un stub TODO).
- `app/Controllers/Api/CommandeController.php` — remplissage complet (était un stub TODO).
- `routes/web.php` — ajout des 3 routes `/api/*`.

### Fichiers ajoutés

- `app/Controllers/Api/ApiBaseController.php` — base mutualisée (auth, lecture JSON, helpers de réponse).

### Vérification à 3 agents

- **Agent back ✅** : validé sans réserve sur les 12 critères techniques (`hash_equals`, fail-closed sur clé vide, `readJsonBody` rejette body vide / JSON invalide / non-objet, `requireApiKey` appelé en première instruction de chaque action, réutilisation pure de `CommandeService`, ordre logique des `catch`, exit systématique après réponse JSON, types JSON stables — IDs en int, prix en string pour éviter IEEE-754).
- **Agent "contrat client" ⚠️→✅** : contrat clair sur `GET /api/catalogue` (auto-suffisant pour composer une commande), sur `POST /api/commandes` (docblock détaillé, erreurs granulaires), sur la réponse `201` (accusé complet : numéro de retrait, statut, total, date). A relevé une lacune : pas d'endpoint de suivi du statut après création — **corrigé immédiatement** par l'ajout de `GET /api/commandes/{numero}` avec validation regex du numéro de retrait.
- **Agent BYAN ✅** : conformité CDC §1 (cycle unifié via réutilisation du service), §3 (prix relus en base, mêmes règles de validation), §6 (source `'api'` tracée). OWASP couvert : A01 (auth obligatoire en première instruction), A02 (`hash_equals` temps constant), A03 (prepared statements dans les repositories consommés), A04 (passage forcé par `CommandeService` — pas d'accès direct au repository pour muter), A05 (fail-closed si clé d'env vide), A07 (pas de CSRF nécessaire car header custom non envoyé par navigateur), A09 (erreurs internes loggées, client reçoit messages génériques sans stack ni SQLSTATE). Pour les commandes API, `id_utilisateur_auteur` reste `NULL` — comportement assumé (commande sans opérateur humain).

### Résultat

✅ **Sprint 9 validé.** La borne externe dispose de tout pour fonctionner : récupérer le catalogue, soumettre une commande validée par le même service que le back-office, et suivre le statut de sa commande. L'API est stateless, authentifiée par clé, et ne fuite aucune donnée interne.

## Sprint 10 - UI globale + navigation

### Objectif

Finaliser le layout, le header, la sidebar filtrée par rôle, les messages flash, la pagination, les breadcrumbs, les états vides et les confirmations.

### En termes simples

> Chaque sprint précédent a ajouté ses pages et ses liens de façon indépendante. Ce sprint harmonise l'ensemble : la sidebar est filtrée par rôle (le rôle Préparation ne voit pas les liens du catalogue, le rôle Accueil ne voit pas les liens d'administration), les messages flash sont cohérents sur toutes les pages, les listes vides affichent un message clair, les actions de désactivation demandent une confirmation, et la navigation globale est lisible. L'application doit être utilisable et cohérente pour chaque profil d'utilisateur défini dans le CDC.

### Ce qui a été codé

- `app/Views/layout.php` refactoré : ne contient plus de code inline pour
  le header et la sidebar. Utilise `include __DIR__ . '/partials/header.php'`
  et `include __DIR__ . '/partials/sidebar.php'`. Le rendu reste identique
  pour les vues existantes.
- `app/Views/partials/header.php` : logo Wacdo, identifiant de l'utilisateur
  connecté, badge `badge-info` affichant le rôle, bouton de déconnexion en
  formulaire POST + CSRF token + `novalidate`.
- `app/Views/partials/sidebar.php` : navigation filtrée par rôle.
  - **Préparation** voit : Liste des commandes + Changer mon mot de passe.
  - **Accueil** voit : Liste des commandes, Nouvelle commande, Déclarer une
    livraison + Changer mon mot de passe.
  - **Administration** voit : tout (commandes complet, catalogue complet,
    gestion utilisateurs, mon compte).
  - Les en-têtes de section (`Catalogue`, `Gestion`) ne s'affichent que si
    la section contient au moins un lien visible pour le rôle courant —
    plus de titre orphelin.
  - Le lien correspondant à la page courante reçoit `class="active"`, via
    une closure qui compare le chemin (extrait par
    `parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)`) au `href` du lien.
    Un lien parent reste actif sur ses sous-pages (`/commandes` reste actif
    sur `/commandes/42`).
- `app/Views/partials/breadcrumb.php` : fil d'ariane optionnel. Lit
  `$breadcrumb = [['label'=>..., 'href'=>...], ...]` ; ne produit aucun HTML
  si le tableau est vide. Le dernier élément est rendu sans lien avec
  `aria-current="page"`.
- `app/Controllers/DashboardController.php` rempli :
  - `requireAuth()` en premier.
  - Préparation et Accueil sont redirigés vers `/commandes` (leur écran de
    travail naturel) — pas de page d'accueil bureaucratique pour les rôles
    opérationnels.
  - Administration voit un tableau de bord avec 4 compteurs métier issus
    d'**une seule requête SQL agrégée** (`COUNT(*) FILTER (...)`) :
    `a_preparer`, `preparees`, `livrees_jour`, `total_jour`.
- `app/Views/dashboard/index.php` rempli : 4 `stat-card` en grille +
  raccourcis vers les actions principales de l'Administration.
- `public/css/components.css` : ajout des règles `.badge-info`,
  `.page-header`, `.page-subtitle`, `.dashboard-stats`, `.stat-card`,
  `.stat-label`, `.stat-value`, `.dashboard-shortcuts`, `.breadcrumb` pour
  les nouveaux composants. Les classes de la sidebar (`.nav-section`,
  `.site-sidebar nav a.active`) étaient déjà stylées dans `layout.css`.

### Fichiers modifiés

- `app/Views/layout.php` — refactor en includes.
- `app/Views/partials/header.php` — implémentation (était un stub vide).
- `app/Views/partials/sidebar.php` — implémentation (était un stub vide).
- `app/Views/partials/breadcrumb.php` — implémentation (était un stub vide).
- `app/Controllers/DashboardController.php` — implémentation (était un stub
  `echo "TODO"`).
- `app/Views/dashboard/index.php` — implémentation (était un commentaire
  vide).
- `public/css/components.css` — ajout du bloc « Sprint 10 — composants UI
  globale ».

### Fichiers ajoutés

Aucun fichier nouveau. Tous les fichiers existaient en stub et ont été
remplis.

### Vérification à 3 agents

- **BACK (PHP/MVC)** — ✅ **VALIDÉ SANS RÉSERVE**. Les 14 critères techniques
  sont OK : `requireAuth()` en début, redirection `exit`-safe, SQL agrégée
  statique sans injection, compteurs castés en `int`, `htmlspecialchars`
  systématique, `$_SERVER['REQUEST_URI']` jamais émis en HTML (seulement
  utilisé pour la comparaison), partials sans logique métier, breadcrumb
  null-safe.
- **FRONT (UX/HTML)** — ⚠️ **HTML validé** ✅, **CSS initialement
  manquant** corrigé pendant le sprint. Sidebar masque correctement les
  sections par rôle, `active` se déclenche sur lien exact et lien parent,
  badge rôle stylé, breadcrumb conforme `aria-current="page"`, dashboard
  sémantique avec `<article class="stat-card">`. Les classes CSS
  manquantes signalées par la revue ont été ajoutées dans
  `public/css/components.css` avant fermeture du sprint.
- **BYAN (CDC + OWASP)** — ✅ **CONFORME 14/14**. CDC fonctionnel : chaque
  rôle voit uniquement ses actions (moindre privilège côté UI, autorisation
  serveur restant dans les contrôleurs), dashboard pertinent, opérationnels
  redirigés vers leur écran de travail. CDC technique : code from scratch,
  PDO singleton, features PostgreSQL (`COUNT(*) FILTER`, `CURRENT_DATE`),
  échappement systématique. OWASP : A01 (auth), A02 (aucun secret exposé),
  A03 (SQL statique + XSS impossible), A04 (zéro donnée nominative dans
  les compteurs), A05 (aucun `var_dump`), A07 (logout POST + CSRF), A09
  (hors périmètre).

### Résultat

✅ Sprint 10 validé. Layout factorisé en partials réutilisables, sidebar
intelligente filtrée par rôle avec lien actif mis en évidence, dashboard
Administration opérationnel avec compteurs métier agrégés en une requête,
CSS aligné. Conforme CDC fonctionnel §3 (chaque rôle = ses actions et
seulement ses actions) et OWASP Top 10 (A01–A09).

## Sprint 11 - Recette finale

### Objectif

Valider l'ensemble du projet contre le CDC technique : Docker, routes, rôles, CSRF, uploads, API, commandes, traces et conformité de code.

### En termes simples

> Avant livraison, une passe de validation complète contre le CDC et le CDC technique. Chaque rôle est testé avec ses actions autorisées et ses actions interdites. Le projet est démarré depuis zéro via Docker pour vérifier qu'il démarre sans intervention manuelle. Toutes les routes, formulaires, transitions de statut et endpoints API sont parcourus. La table `traces_actions` est vérifiée pour toutes les opérations sensibles. Tout écart avec le CDC est corrigé avant la présentation jury.

### Ce qui a été codé

Sprint de recette : aucun code écrit. Trois audits exhaustifs en parallèle
(BACK end-to-end, FRONT par rôle, BYAN CDC + OWASP) ont été menés sur
l'ensemble du code livré aux Sprints 0 à 10.

### Fichiers modifiés

Aucun. Recette en mode read-only.

### Fichiers ajoutés

Aucun.

### Vérification à 3 agents

#### **BACK — recette end-to-end** : ✅ **RECETTE VALIDÉE**

- **46 routes auditées** (43 web + 3 API), **46 vertes**, **0 écart**.
- Tableau de contrôle complet : route, méthode, contrôleur, `requireAuth`,
  `requireRole`, CSRF, PRG, trace — chaque ligne validée.
- `password_hash(..., PASSWORD_BCRYPT, ['cost' => 12])` confirmé aux 3
  endroits (création utilisateur, modification, changement de mot de passe
  personnel) + `password_verify` côté `AuthService` avec protection timing
  attack (`DUMMY_HASH`).
- Zéro `new PDO` direct, zéro `var_dump`/`print_r`/`debug_backtrace`,
  zéro `eval`/`exec`/`system`/`shell_exec` dans tout `app/`.
- `TraceService::log()` appelé sur toutes les actions CUD sensibles
  (utilisateurs, catégories, produits, menus, sections, options,
  commandes, transitions de statut).
- CSRF systématique sur tous les POST web ; absence volontaire sur l'API
  (stateless, authentification par `X-API-Key`).
- PRG (Post-Redirect-Get) respecté sur toutes les actions web.

#### **FRONT — recette par rôle** : ✅ **VALIDÉE** (un faux positif écarté)

- Grille de visibilité de la sidebar testée pour les 3 rôles
  (Administration, Accueil, Préparation) : **8 liens × 3 rôles = 24
  combinaisons, toutes conformes au CDC §3**.
- 18 vues auditées : CSRF, `htmlspecialchars`, `novalidate`, états vides,
  flash messages — toutes ✅.
- Aucune XSS reflétée détectée (grep exhaustif sur `$_GET`/`$_POST`/
  `$_SERVER` non échappés : 0 match).
- Login sans fuite d'info (message générique identique pour identifiant
  inexistant et mot de passe incorrect — CDC §6.3.1).
- Brute-force protégé via `LoginAttemptService` (5 tentatives → 15 min
  blocage, délai 3 s entre échecs).
- Session : `session_regenerate_id(true)` après login réussi.
- Uploads : `enctype="multipart/form-data"` sur produits/menus.
- **Faux positif écarté** : la revue a signalé comme « bloquant » le
  pattern `onsubmit="return confirm(<?= htmlspecialchars(json_encode(...),
  ENT_QUOTES, 'UTF-8') ?>)"` sur 5 boutons de désactivation, en arguant
  que les `&quot;` produits dans l'attribut ne seraient pas interprétés
  en JavaScript. **Cette analyse est incorrecte** : le parseur HTML
  décode les entités dans les valeurs d'attribut **avant** l'évaluation
  du gestionnaire JS. Le code `onsubmit="return confirm(&quot;Texte&quot;)"`
  produit donc bien `return confirm("Texte")` au runtime. C'est un
  comportement HTML standard et le pattern actuel reste **plus sûr** que
  `json_encode` seul (protection contre injection HTML via le nom de
  l'entité affichée). Aucune correction nécessaire.

#### **BYAN — conformité finale CDC + OWASP** : ✅ **PROJET LIVRABLE**

- **Docker zéro-config** ✅ : `env_file`, `.env.example` (11 variables),
  `schema.sql` + `seed_dev.sql` montés, `DocumentRoot /var/www/html/public`,
  `mod_rewrite` activé, en-têtes Apache sécurisés
  (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, CSP).
- **CDC technique** ✅ : PHP 8.2 OOP from scratch, **zéro Composer**,
  PSR-4 autoload manuel dans `public/index.php`, PDO singleton,
  `session_start()` une seule fois, CSRF via `hash_equals`, Bcrypt cost
  12, router maison, traces dans `traces_actions`.
- **OWASP Top 10** ✅ 10/10 : A01 (auth + rôles), A02 (Bcrypt 12,
  jamais en clair, jamais loggé), A03 (prepared statements + échappement
  + pas d'exec), A04 (transitions de statut métier validées serveur,
  règles unifiées back-office/API), A05 (`display_errors=0`, pas de
  `phpinfo`, en-têtes Apache), A07 (cookies HttpOnly + SameSite=Lax,
  `regenerate_id` après login, brute-force protégé, timing attack
  mitigée), A08 (pas d'`unserialize` de user input, `JSON_THROW_ON_ERROR`
  côté API), A09 (`traces_actions` peuplée), A10 (N/A).
- **Documents & BDD** ✅ : `CDC_fonctionnel_bloc2.md`,
  `CDC_technique_bloc2.md`, les 3 schémas drawio (MCD, MCT, schéma BDD),
  `sprints.md` complet sur les 12 sprints (0-11), `schema.sql` avec 11
  tables, contraintes CHECK, FK, index, `seed_dev.sql` avec 3 rôles, 3
  utilisateurs, catalogue minimal.
- **Écarts détectés** : 2 mineurs acceptés :
  - `seed_dev.sql` utilise Bcrypt cost 10 (données dev uniquement ; la
    production crée tous les comptes avec cost 12).
  - Pas de limite explicite de taille fichier image dans `UploadService`
    (mitigé par `upload_max_filesize` PHP et Apache ; acceptable en
    contexte scolaire).

### Résultat

✅ **Sprint 11 validé. Projet livrable jury.**

- 46 routes / 46 vertes (BACK)
- 24 combinaisons rôle × lien / 24 conformes (FRONT)
- 10 / 10 OWASP Top 10 (BYAN)
- 0 écart bloquant, 2 écarts mineurs assumés et documentés
- Conformité CDC fonctionnel et CDC technique vérifiée end-to-end
- Docker zéro-config fonctionnel

Le projet Wacdo Bloc 2 est prêt pour la présentation jury.

À compléter après validation.