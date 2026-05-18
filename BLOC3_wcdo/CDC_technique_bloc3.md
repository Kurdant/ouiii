# CDC technique Bloc 3

## Sommaire

### 0 Analyse fonctionnelle rapide
### 1 MCD
### 2 Dictionnaire des données
### 3 MCT
### 4 Contexte technique
### 5 Choix techniques retenus
### 6 Architecture applicative
### 7 Persistance et base de données
### 8 Schéma BDD
### 9 Sécurité applicative
### 10 Environnement de développement
### 11 Déploiement
### 12 Conventions de développement
### 13 Plan de développement par sprints

---

## 0 Analyse fonctionnelle rapide

L'application Wacdo est une application web interne réservée aux collaborateurs administrateurs authentifiés. Elle permet de gérer les collaborateurs, les restaurants, les fonctions et les affectations. Le cœur métier est l'affectation d'un collaborateur à une fonction dans un restaurant sur une période donnée. Le système doit distinguer les affectations en cours, conserver l'historique, identifier les collaborateurs non affectés et permettre les recherches filtrées demandées.

## 1 MCD

Le modèle conceptuel de données retient quatre entités principales : `Collaborateur`, `Restaurant`, `Fonction` et `Affectation`. L'entité `Affectation` est l'entité centrale du modèle : elle porte le lien métier entre une personne, un restaurant, un poste et une période.

### 1.1 Entités principales

| Entité | Rôle métier |
|---|---|
| **Collaborateur** | Personne employée par Wacdo, enregistrée dans l'application, pouvant disposer ou non du droit administrateur. |
| **Restaurant** | Établissement Wacdo dans lequel des collaborateurs peuvent être affectés. |
| **Fonction** | Poste de travail existant chez Wacdo, utilisé comme référentiel lors des affectations. |
| **Affectation** | Association datée entre un collaborateur, un restaurant et une fonction. |

### 1.2 Relations et cardinalités

| Relation | Cardinalité | Lecture métier |
|---|---|---|
| Collaborateur — Affectation | Collaborateur `0,n` ; Affectation `1,1` | Un collaborateur peut ne jamais être affecté ou posséder plusieurs affectations. Une affectation concerne obligatoirement un seul collaborateur. |
| Restaurant — Affectation | Restaurant `0,n` ; Affectation `1,1` | Un restaurant peut n'avoir aucune affectation ou accueillir plusieurs affectations. Une affectation concerne obligatoirement un seul restaurant. |
| Fonction — Affectation | Fonction `0,n` ; Affectation `1,1` | Une fonction peut ne jamais être utilisée ou être utilisée dans plusieurs affectations. Une affectation concerne obligatoirement une seule fonction. |

### 1.3 Diagramme textuel

```text
COLLABORATEUR (0,n) ─── concerne ─── (1,1) AFFECTATION
RESTAURANT    (0,n) ─── accueille ─── (1,1) AFFECTATION
FONCTION      (0,n) ─── qualifie  ─── (1,1) AFFECTATION
```

### 1.4 Règles de lecture du MCD

- Une affectation relie obligatoirement un collaborateur, un restaurant et une fonction.
- Une affectation possède une date de début obligatoire.
- Une affectation possède une date de fin facultative.
- Une affectation en cours est une affectation active à la date de consultation : `date_debut <= date du jour` et (`date_fin` vide ou `date_fin >= date du jour`).
- L'historique des affectations est obtenu à partir des affectations enregistrées.
- Un collaborateur non affecté est un collaborateur ne possédant aucune affectation en cours.
- Le droit d'accès à l'application est porté par le collaborateur via l'indicateur `administrateur`.
- Aucune entité séparée `Utilisateur`, `Rôle`, `Permission`, `HistoriqueAffectation`, `Planning`, `Contrat`, `Paie`, `Congé` ou `Commande` n'est retenue dans ce périmètre.

## 2 Dictionnaire des données

Le dictionnaire des données ci-dessous reprend les données nécessaires au MCD et au futur schéma de base de données. Les types indiqués sont des types techniques cibles, adaptés à une base SQL et à une implémentation avec ORM.

| Entité | Attribut | Type de donnée | Contraintes / Notes |
|---|---|---|---|
| Collaborateur | `id` | INT | Clé primaire technique Laravel, auto-incrémentée. |
| Collaborateur | `nom` | VARCHAR(100) | Obligatoire, non vide. Contrôlé avant enregistrement. |
| Collaborateur | `prenom` | VARCHAR(100) | Obligatoire, non vide. Contrôlé avant enregistrement. |
| Collaborateur | `email` | VARCHAR(180) | Obligatoire, format email valide, unique. Sert d'identifiant de connexion. |
| Collaborateur | `telephone` | VARCHAR(20) | Optionnel. Format téléphone contrôlé si renseigné. Retenu pour couvrir l'exigence de validation du référentiel. |
| Collaborateur | `date_premiere_embauche` | DATE | Obligatoire. Date valide. |
| Collaborateur | `administrateur` | BOOLEAN | Obligatoire, valeur par défaut `false`. `true` autorise l'accès applicatif. |
| Collaborateur | `password` | VARCHAR(255) | Obligatoire si `administrateur = true`. Contient exclusivement le hash généré par Laravel, jamais le mot de passe en clair. |
| Restaurant | `id` | INT | Clé primaire technique Laravel, auto-incrémentée. |
| Restaurant | `nom` | VARCHAR(150) | Obligatoire, non vide. Utilisé pour la recherche. |
| Restaurant | `adresse` | VARCHAR(255) | Obligatoire, non vide. Contrôlée avant enregistrement. |
| Restaurant | `code_postal` | VARCHAR(10) | Obligatoire. Format code postal valide. Stocké en texte pour conserver les zéros initiaux. |
| Restaurant | `ville` | VARCHAR(100) | Obligatoire, non vide. Utilisée pour les filtres restaurant et affectation. |
| Fonction | `id` | INT | Clé primaire technique Laravel, auto-incrémentée. |
| Fonction | `intitule_poste` | VARCHAR(120) | Obligatoire, non vide, unique. Intitulé du poste existant chez Wacdo. |
| Affectation | `id` | INT | Clé primaire technique Laravel, auto-incrémentée. |
| Affectation | `collaborateur_id` | INT | Clé étrangère obligatoire vers `collaborateurs.id`. |
| Affectation | `restaurant_id` | INT | Clé étrangère obligatoire vers `restaurants.id`. |
| Affectation | `fonction_id` | INT | Clé étrangère obligatoire vers `fonctions.id`. |
| Affectation | `date_debut` | DATE | Obligatoire. Date de début de l'affectation. |
| Affectation | `date_fin` | DATE | Facultative. Vide si aucune date de fin n'est connue. Si renseignée, elle doit être supérieure ou égale à `date_debut`. |

### 2.1 Données déduites non stockées

| Donnée | Définition | Mode de détermination |
|---|---|---|
| `affectation_en_cours` | Affectation active à la date de consultation | `date_debut <= date du jour` et (`date_fin` vide ou `date_fin >= date du jour`) |
| `affectation_future` | Affectation déjà planifiée mais pas encore active | `date_debut > date du jour` |
| `affectation_terminee` | Affectation appartenant à l'historique | `date_fin` renseignée et `date_fin < date du jour` |
| `collaborateur_non_affecte` | Collaborateur sans poste actif à la date de consultation | Collaborateur sans affectation en cours |
| `historique_affectations_collaborateur` | Historique d'un collaborateur | Ensemble des affectations liées au collaborateur |
| `historique_affectations_restaurant` | Historique d'un restaurant | Ensemble des affectations liées au restaurant |
| `collaborateurs_en_poste` | Collaborateurs actuellement présents dans un restaurant | Affectations en cours du restaurant à la date de consultation |

### 2.2 Contraintes principales à porter dans le schéma BD

- `email` doit être unique pour identifier sans ambiguïté un collaborateur administrateur lors de la connexion.
- `intitule_poste` doit être unique pour éviter les doublons dans le référentiel des fonctions.
- Une affectation doit toujours référencer un collaborateur, un restaurant et une fonction existants.
- `date_fin` doit être vide ou supérieure ou égale à `date_debut`.
- Un doublon strict d'affectation est interdit : même collaborateur, même restaurant, même fonction, même date de début et même date de fin.
- Le modèle n'interdit pas plusieurs affectations en cours pour un même collaborateur, car le besoin parle de la ou les affectations en cours.

## 3 MCT

Le MCT décrit les traitements métier déclenchés par les actions de l'administrateur dans l'application. Il reste centré sur les opérations prévues par le référentiel : authentification, gestion des restaurants, gestion des collaborateurs, gestion des fonctions et recherche des affectations.

### 3.1 Règle transversale sur les affectations actives

Le calcul d'une affectation en cours repose sur la date de consultation. Une affectation est active si les deux conditions suivantes sont respectées :

```text
date_debut <= date du jour
ET
(date_fin est vide OU date_fin >= date du jour)
```

Conséquences métier :

- une affectation future ne rend pas le collaborateur en poste aujourd'hui ;
- une affectation passée reste consultable dans l'historique ;
- un collaborateur peut être enregistré sans affectation active ;
- un collaborateur avec uniquement des affectations passées ou futures est non affecté actuellement.

### 3.2 Événements déclencheurs

| ID | Événement déclencheur |
|---|---|
| E-01 | Soumission du formulaire de connexion |
| E-02 | Demande d'accès à une fonctionnalité protégée |
| E-03 | Demande de consultation ou de recherche des restaurants |
| E-04 | Sélection d'un restaurant |
| E-05 | Validation d'une création ou modification de restaurant |
| E-06 | Validation d'une affectation depuis la fiche restaurant |
| E-07 | Demande de consultation ou de recherche des collaborateurs |
| E-08 | Demande de recherche des collaborateurs non affectés |
| E-09 | Sélection d'un collaborateur |
| E-10 | Validation d'une création ou modification de collaborateur |
| E-11 | Validation d'une affectation depuis la fiche collaborateur |
| E-12 | Validation d'une modification d'affectation en cours |
| E-13 | Demande de consultation des fonctions |
| E-14 | Validation d'une création ou modification de fonction |
| E-15 | Demande de recherche des affectations |
| E-16 | Demande de déconnexion |

### 3.3 Opérations conceptuelles

| Événement | Opération | Règles / conditions | Résultat |
|---|---|---|---|
| E-01 — Soumission du formulaire de connexion | Authentifier un administrateur | Le collaborateur existe, possède `administrateur = true`, dispose d'un mot de passe et les identifiants sont valides. | Session ouverte et accès au menu principal. En cas d'échec, accès refusé. |
| E-02 — Demande d'accès à une fonctionnalité protégée | Contrôler l'autorisation | L'utilisateur doit être authentifié et administrateur. | Accès autorisé à l'écran demandé ou refus sans traitement métier. |
| E-03 — Demande de consultation ou de recherche des restaurants | Rechercher les restaurants | Les critères utilisables sont le nom, le code postal et la ville. Les critères vides sont ignorés. | Liste filtrée des restaurants. |
| E-05 — Validation d'une création ou modification de restaurant | Enregistrer un restaurant | Nom, adresse, code postal et ville obligatoires. Données non vides et formats valides. | Restaurant créé ou modifié. Si les données sont invalides, enregistrement refusé. |
| E-04 — Sélection d'un restaurant | Afficher la fiche restaurant | Le restaurant existe. Les collaborateurs en poste sont calculés à partir des affectations actives à la date du jour. | Fiche restaurant affichée avec informations, collaborateurs en poste et historique des affectations. |
| E-06 — Validation d'une affectation depuis la fiche restaurant | Créer une affectation | Collaborateur, restaurant, fonction et date de début obligatoires. Date de fin vide ou supérieure ou égale à la date de début. Aucun doublon strict. | Affectation enregistrée. Son statut d'affichage est déduit de ses dates. |
| E-07 — Demande de consultation ou de recherche des collaborateurs | Rechercher les collaborateurs | Les critères utilisables sont le nom, le prénom et l'email. Les critères vides sont ignorés. | Liste filtrée des collaborateurs. |
| E-10 — Validation d'une création ou modification de collaborateur | Enregistrer un collaborateur | Nom, prénom, email, date de première embauche et indicateur administrateur obligatoires. Email valide et unique. Mot de passe obligatoire si administrateur. Téléphone contrôlé si présent. | Collaborateur créé ou modifié. Une affectation n'est pas obligatoire pour enregistrer le collaborateur. |
| E-08 — Demande de recherche des collaborateurs non affectés | Identifier les collaborateurs non affectés actuellement | Un collaborateur est non affecté s'il ne possède aucune affectation active à la date du jour. | Liste des collaborateurs enregistrés sans poste actif aujourd'hui. |
| E-09 — Sélection d'un collaborateur | Afficher la fiche collaborateur | Le collaborateur existe. Les affectations actives sont calculées à la date du jour. L'historique contient toutes ses affectations. | Fiche collaborateur affichée avec informations, affectations en cours, affectations futures ou passées et historique. |
| E-11 — Validation d'une affectation depuis la fiche collaborateur | Créer une affectation | Collaborateur, restaurant, fonction et date de début obligatoires. Date de fin vide ou supérieure ou égale à la date de début. Aucun doublon strict. | Affectation enregistrée. Son statut d'affichage est déduit de ses dates. |
| E-12 — Validation d'une modification d'affectation en cours | Modifier une affectation active | L'affectation existe et est active à la date du jour. Les nouvelles données restent cohérentes. | Affectation mise à jour. Si sa période ne couvre plus la date du jour, elle sort des affectations en cours et reste dans l'historique. |
| E-13 — Demande de consultation des fonctions | Consulter les fonctions | L'utilisateur est authentifié administrateur. | Liste des fonctions affichée. |
| E-14 — Validation d'une création ou modification de fonction | Enregistrer une fonction | L'intitulé du poste est obligatoire, non vide et unique. | Fonction créée ou modifiée. Si l'intitulé est invalide ou déjà existant, enregistrement refusé. |
| E-15 — Demande de recherche des affectations | Rechercher les affectations | Les critères utilisables sont la fonction, la date de début, la date de fin et la ville. La recherche porte sur toutes les affectations. | Liste filtrée des affectations, qu'elles soient passées, actives ou futures. |
| E-16 — Demande de déconnexion | Fermer la session | Une session utilisateur est ouverte. | Session fermée. Les pages protégées redeviennent inaccessibles sans authentification. |

### 3.4 Diagramme textuel du MCT

```text
[Connexion]
	-> Authentifier un administrateur
	-> Session ouverte / Accès refusé

[Accès fonctionnalité protégée]
	-> Contrôler l'autorisation
	-> Écran ouvert / Accès refusé

[Recherche restaurants]
	-> Rechercher les restaurants
	-> Liste filtrée

[Sélection restaurant]
	-> Afficher la fiche restaurant
	-> Détail + collaborateurs en poste aujourd'hui + historique

[Validation restaurant]
	-> Enregistrer un restaurant
	-> Restaurant créé ou modifié

[Recherche collaborateurs]
	-> Rechercher les collaborateurs
	-> Liste filtrée

[Recherche collaborateurs non affectés]
	-> Identifier les collaborateurs sans affectation active aujourd'hui
	-> Liste des collaborateurs non affectés actuellement

[Sélection collaborateur]
	-> Afficher la fiche collaborateur
	-> Détail + affectations actives aujourd'hui + historique

[Validation collaborateur]
	-> Enregistrer un collaborateur
	-> Collaborateur créé ou modifié

[Validation affectation]
	-> Créer une affectation
	-> Affectation enregistrée

[Modification affectation en cours]
	-> Modifier une affectation active
	-> Affectation mise à jour

[Consultation fonctions]
	-> Consulter les fonctions
	-> Liste des fonctions

[Validation fonction]
	-> Enregistrer une fonction
	-> Fonction créée ou modifiée

[Recherche affectations]
	-> Rechercher les affectations
	-> Liste filtrée des affectations

[Déconnexion]
	-> Fermer la session
	-> Session fermée
```

### 3.5 Traitements exclus du MCT

- Aucun traitement de paie, contrat, congé, absence, planning horaire ou recrutement n'est modélisé.
- Aucun traitement de commande, caisse, stock ou production restaurant n'est modélisé.
- Aucun traitement de rôle avancé ou de permission détaillée n'est modélisé.
- Aucun traitement d'historique séparé n'est modélisé : l'historique est obtenu à partir des affectations enregistrées.
- Aucun statut d'affectation n'est stocké : les états active, future et terminée sont déduits des dates.

## 4 Contexte technique

Le projet Wacdo Bloc 3 est réalisé sous la forme d'une application web interne de gestion. Elle est destinée exclusivement aux collaborateurs disposant du droit administrateur et doit être développée avec un framework back, conformément au cadrage du sujet. L'application couvre quatre domaines métier : les collaborateurs, les restaurants, les fonctions et les affectations.

Le contexte technique impose une architecture serveur structurée, orientée objets, capable de produire des écrans HTML pour le back-office et de s'appuyer sur une base de données relationnelle SQL. Le projet doit donc articuler proprement les couches suivantes : présentation, logique métier, accès aux données et sécurité applicative.

### 4.1 Nature de l'application

- L'application est un back-office web accessible après authentification.
- L'interface principale est rendue côté serveur pour répondre rapidement au besoin métier et limiter la complexité du projet.
- Le cœur fonctionnel repose sur la gestion datée des affectations, avec calcul d'états dérivés à la consultation.
- Le projet n'a pas de besoin API public, mobile, temps réel ou microservices dans ce périmètre.

### 4.2 Contraintes imposées par le sujet

- Le développement doit être réalisé avec un framework back.
- Le projet doit utiliser un langage serveur adapté à ce framework.
- Le rendu des vues doit passer par un moteur de templates.
- Les données doivent être persistées dans une base SQL.
- La gestion du modèle de données doit s'appuyer sur un ORM.
- L'application doit intégrer authentification et autorisation.
- Le livrable final doit être déployable et démontrable devant jury.

### 4.3 Enjeux techniques du projet

Les enjeux techniques sont simples mais structurants.

- Garantir un contrôle d'accès strict : aucune fonctionnalité métier ne doit être accessible sans session valide et sans droit administrateur.
- Assurer l'intégrité du modèle de données : une affectation référence toujours un collaborateur, un restaurant et une fonction existants.
- Gérer correctement la dimension temporelle des affectations : l'état actif, futur ou terminé est calculé à partir des dates et non stocké.
- Préserver une base lisible et défendable : quatre entités métier principales, sans sur-modélisation RH ni système de rôles complexe.
- Produire un code maintenable : séparation claire entre contrôleurs, services métier, entités et accès aux données.
- Faciliter les évolutions demandées par le jury : l'application doit supporter une modification ponctuelle sans remise à plat de l'architecture.

### 4.4 Contraintes d'exploitation et de qualité

- L'application est utilisée dans un contexte interne ; elle ne nécessite pas de montée en charge complexe ni de distribution multi-sites sophistiquée.
- La priorité porte sur la fiabilité fonctionnelle, la clarté du code, la sécurité des accès et la cohérence des données.
- Les formulaires doivent contrôler les données attendues par le référentiel avant toute persistance.
- Les mots de passe doivent être protégés par hachage ; ils ne sont jamais stockés en clair.
- Les erreurs métier doivent être bloquantes et compréhensibles pour l'utilisateur administrateur.

### 4.5 Frontières techniques retenues

- Le projet couvre uniquement le back-office de gestion des affectations.
- Il n'inclut ni paie, ni planning, ni gestion des absences, ni recrutement, ni gestion opérationnelle des restaurants.
- Il ne prévoit pas de synchronisation avec un SI externe dans ce périmètre.
- Il ne prévoit pas de mécanisme de workflow complexe, de moteur de règles ou de traitement batch nécessaire au métier.

### 4.6 Exigences de réalisation

- L'environnement de développement doit permettre une installation propre du framework et de ses dépendances.
- Le projet doit pouvoir être exécuté localement de manière reproductible.
- La structure technique choisie ensuite devra rester simple à expliquer : framework, routing, contrôleurs, vues, ORM, sécurité, base de données et déploiement.
- Les tests attendus par le sujet concernent au minimum les parcours fonctionnels critiques, les validations de formulaires et les contrôles d'accès.

## 5 Choix techniques retenus

Les choix techniques retenus sont volontairement simples, cohérents entre eux et directement défendables devant jury. L'objectif n'est pas de multiplier les outils, mais de s'appuyer sur une stack back standard, moderne et maintenable.

### 5.1 Framework back

Le framework retenu est **Laravel 11**.

Ce choix est retenu pour quatre raisons :

- Laravel 11 répond exactement au cadrage du sujet Bloc 3 orienté framework back.
- Il fournit nativement le routage, les contrôleurs, le moteur de templates, l'ORM, la validation, les middlewares et la gestion de session.
- Il permet de produire rapidement un back-office propre sans surcouche front inutile.
- Il reste simple à expliquer à l'oral : cycle requête, route, contrôleur, vue, modèle, validation, sécurité.

### 5.2 Langage serveur et dépendances

- Langage serveur retenu : **PHP 8.3**.
- Gestionnaire de dépendances retenu : **Composer**.

PHP 8.3 est retenu pour disposer d'une version récente, stable et performante, compatible avec Laravel et adaptée à un projet d'examen traité comme un vrai projet. Composer est retenu car il est l'outil standard de l'écosystème PHP pour installer Laravel et gérer proprement les dépendances applicatives.

### 5.3 Moteur de templates

Le moteur de templates retenu est **Blade**.

Blade est le meilleur choix dans ce contexte car il est natif dans Laravel, léger, lisible et parfaitement adapté à un back-office rendu côté serveur. Il évite d'ajouter Twig dans un projet Laravel, ce qui créerait une complexité inutile sans gain métier.

### 5.4 ORM et accès aux données

L'ORM retenu est **Eloquent ORM**.

Eloquent est retenu car il est natif dans Laravel, bien intégré aux modèles, aux relations et aux requêtes courantes. Il permet de manipuler proprement les entités `Collaborateur`, `Restaurant`, `Fonction` et `Affectation` sans écrire une couche d'abstraction supplémentaire.

Pour les cas de recherche plus spécifiques, notamment la recherche multi-critères sur les affectations, l'application pourra s'appuyer sur le Query Builder Laravel lorsque cela est plus lisible qu'une succession de relations Eloquent.

### 5.5 Migrations et structure de base

Le projet utilise **les migrations Laravel**.

Ce choix n'est pas accessoire. Il permet de versionner la structure de base de données, de reconstruire l'environnement local ou serveur de manière reproductible et de démontrer une démarche propre de gestion du schéma. Dans un projet conteneurisé et déployé sur VPS, ignorer les migrations compliquerait inutilement l'installation et la maintenance.

### 5.6 Base de données

Le SGBD retenu est **PostgreSQL**.

PostgreSQL est choisi pour sa robustesse, sa fiabilité transactionnelle, sa bonne gestion des contraintes et sa très bonne compatibilité avec Laravel. Il convient parfaitement à un modèle relationnel centré sur les affectations, les clés étrangères, les recherches filtrées et l'intégrité des données.

### 5.7 Serveur web et exécution applicative

Le serveur HTTP retenu est **Nginx**, avec exécution PHP via **PHP-FPM**.

Ce choix est retenu pour garder une architecture web standard, claire et proche d'un contexte de production réel. Il est plus cohérent de travailler dès le départ avec `Nginx + PHP-FPM` que de baser le projet sur le serveur de développement intégré si la cible finale est un déploiement Docker sur VPS.

### 5.8 Rendu front

Le rendu front repose sur :

- **Blade** pour les vues HTML ;
- **CSS maison** pour la mise en forme ;
- **JavaScript natif minimal** uniquement si un besoin d'interaction légère apparaît.

Le projet ne retient ni SPA, ni framework front, ni bibliothèque CSS lourde. Cette décision est volontaire : le besoin porte sur un back-office de gestion, pas sur une interface applicative complexe côté client. Un rendu serveur avec CSS classique est le meilleur compromis entre lisibilité, rapidité de développement et soutenabilité devant jury.

### 5.9 Authentification et autorisation

Le projet retient une **authentification par formulaire et session serveur Laravel**.

L'autorisation repose sur une règle simple : seul un collaborateur dont l'attribut `administrateur` vaut `true` peut accéder à l'application. Cette vérification est centralisée dans les middlewares et dans les contrôleurs protégés. Aucun système de rôles avancé ni table de permissions n'est retenu.

### 5.10 Conteneurisation et déploiement

Le déploiement cible repose sur :

- un **VPS Linux** ;
- une exécution via **Docker** ;
- une orchestration locale et serveur avec **Docker Compose**.

Ce choix permet d'aligner le développement local et la production sur une même logique d'exécution. Il simplifie l'installation, la reproductibilité de l'environnement, la configuration de PHP, Nginx et PostgreSQL, ainsi que la démonstration du déploiement.

### 5.11 Stack technique retenue


Symfony = plus explicite et plus composable.
Laravel = plus guidé et plus rapide à mettre en œuvre. blade intégré dedans, pas besoin d'intégrer un autre moteur de template
Blade = intégrer dans laravel 

La stack retenue est donc la suivante :

| Domaine | Choix retenu |
|---|---|
| Framework back | Laravel 11 |
| Langage serveur | PHP 8.3 |
| Gestionnaire de dépendances | Composer |
| Moteur de templates | Blade |
| ORM | Eloquent ORM |
| Migrations | Laravel Migrations |
| Base de données | PostgreSQL |
| Serveur web | Nginx |
| Exécution PHP | PHP-FPM |
| Rendu front | Blade + CSS maison + JavaScript natif minimal |
| Authentification | Formulaire + session serveur Laravel |
| Autorisation | Contrôle sur l'attribut `administrateur` |
| Conteneurisation | Docker |
| Orchestration | Docker Compose |
| Hébergement cible | VPS Linux |

### 5.12 Choix volontairement non retenus

Les technologies suivantes ne sont pas retenues, car elles surdimensionnent le projet par rapport au besoin réel :

- aucun framework front de type React, Vue ou Angular ;
- aucun moteur de templates externe à Laravel ;
- aucun ORM externe à Laravel ;
- aucune base MySQL retenue pour ce projet ;
- aucun système de rôles complexe ;
- aucune architecture microservices ;
- aucun orchestrateur complexe de type Kubernetes.

## 6 Architecture applicative

L'application Wacdo Bloc 3 est réalisée sous la forme d'un monolithe web Laravel. Toute la logique de gestion est portée par une seule application, avec rendu serveur via Blade et persistance relationnelle via Eloquent sur PostgreSQL. L'architecture retenue reste MVC, mais elle s'appuie exclusivement sur les briques natives du framework. Aucune couche technique redondante n'est ajoutée.

### 6.1 Principe général

Le cycle standard d'une requête suit l'enchaînement suivant :

1. l'utilisateur envoie une requête HTTP vers une route déclarée dans `routes/web.php` ;
2. Laravel applique les middlewares de session, d'authentification et d'autorisation ;
3. le contrôleur concerné reçoit la requête ;
4. un Form Request valide les données d'entrée lorsqu'il s'agit d'une création ou d'une modification ;
5. le contrôleur exécute directement le CRUD simple ou délègue au service métier lorsqu'une règle transverse doit être appliquée ;
6. les modèles Eloquent lisent ou écrivent les données dans PostgreSQL ;
7. le contrôleur renvoie soit une vue Blade, soit une redirection avec message flash.

Cette séquence est la référence de l'application. Elle implique les décisions suivantes :

- aucun routeur maison ;
- aucune couche repository dédiée ;
- aucune API REST séparée ;
- aucun framework front de type SPA ;
- aucun stockage d'un statut d'affectation en base.

L'état d'une affectation est calculé à la consultation à partir des dates. Une affectation est en cours lorsque `date_debut <= date du jour` et que `date_fin` est vide ou `>= date du jour`. Un collaborateur est non affecté lorsqu'il ne possède aucune affectation active à la date de consultation.

### 6.2 Flux type d'une requête

Le flux de création d'une affectation illustre l'architecture retenue :

1. l'administrateur ouvre la fiche d'un collaborateur ou d'un restaurant et déclenche l'action d'affectation ;
2. l'application affiche le formulaire standard de création d'affectation, éventuellement prérempli selon le contexte ;
3. la soumission du formulaire cible la route `POST /affectations` ;
4. les middlewares vérifient qu'une session authentifiée existe et que le collaborateur connecté possède `administrateur = true` ;
5. `StoreAffectationRequest` contrôle la présence du collaborateur, du restaurant, de la fonction, de la date de début, la validité des identifiants référencés et la cohérence chronologique des dates ;
6. `AffectationController` transmet les données validées à `AffectationService` ;
7. `AffectationService` applique les règles métier d'affectation : construction cohérente de la période, refus d'un doublon strictement identique et préparation de l'enregistrement ;
8. le modèle `Affectation` enregistre la ligne en base via Eloquent ;
9. le contrôleur redirige vers la fiche d'origine ou vers la liste des affectations avec un message de confirmation ;
10. la vue Blade recalcule ensuite l'affichage `en cours`, `terminée` ou `non affecté` à partir des dates disponibles.

Le flux d'une création ou modification simple de restaurant, de fonction ou de collaborateur est plus court : route, middleware, Form Request, contrôleur, modèle Eloquent, redirection. Aucun service spécifique n'est introduit pour ces cas, car Laravel couvre déjà toute la mécanique nécessaire.

### 6.3 Répartition des responsabilités

La répartition des responsabilités est figée comme suit. Pour chaque couche, il faut distinguer ce que Laravel prend déjà en charge et ce que le projet doit réellement coder.

| Couche | Géré par Laravel | À coder dans le projet |
|---|---|---|
| Routage | moteur de routage, résolution des verbes HTTP, paramètres dynamiques, groupement de middlewares, redirections | déclaration des routes du back-office dans `routes/web.php`, nommage des routes, choix des URLs et rattachement aux contrôleurs |
| Session et authentification | démarrage de session, persistance de l'utilisateur connecté, protection CSRF, helpers d'authentification, hachage des mots de passe | formulaire de connexion, logique de tentative de connexion sur `Collaborateur`, règle d'accès réservée aux administrateurs, déconnexion |
| Middlewares | pipeline d'exécution avant contrôleur, enchaînement des contrôles, refus d'accès et redirection | middleware vérifiant `administrateur = true` et rattachement des routes protégées à ce middleware |
| Contrôleurs | injection de la requête, injection des dépendances, aide au rendu des vues et aux redirections | actions `index`, `create`, `store`, `show`, `edit`, `update`, orchestration des filtres, chargement des données de fiche, choix de la vue de retour |
| Validation | cycle de validation des Form Requests, redirection automatique en cas d'erreur, stockage des messages d'erreur en session | règles métier et techniques des formulaires : champs obligatoires, unicité de l'email, cohérence des dates, existence des identifiants référencés |
| ORM et accès aux données | Eloquent, Query Builder, génération SQL courante, hydratation des objets, gestion des relations, pagination, timestamps | définition des modèles `Collaborateur`, `Restaurant`, `Fonction`, `Affectation`, relations métier, attributs autorisés, filtres de recherche, requêtes spécifiques pour détails, historiques et non affectés |
| Logique métier | aucune logique métier Wacdo n'est fournie par le framework | `AffectationService`, calcul et contrôle des règles d'affectation, refus des doublons stricts, centralisation des traitements métiers transverses |
| Vues | moteur Blade, héritage de layout, composants, échappement HTML par défaut, réaffichage des anciennes valeurs de formulaire | gabarit du back-office, écrans de liste, formulaires, fiches détail, tableaux d'historique, messages métier affichés à l'utilisateur |
| Base de données | moteur de migrations, exécution ordonnée des migrations, rollback, seeders, intégration PostgreSQL via configuration Laravel | écriture des migrations du schéma Wacdo, clés étrangères, contraintes d'unicité, index, seeders de données initiales |
| Réponse HTTP | gestion des réponses HTML, redirections, messages flash, codes de retour usuels | choix de la réponse adaptée à chaque cas métier : retour formulaire, redirection après succès, refus d'accès, message d'erreur compréhensible |

La conséquence directe est la suivante : Laravel fournit l'infrastructure technique générique, tandis que le projet code uniquement la logique propre à Wacdo.

Concrètement, cela signifie :

- Laravel gère déjà le routeur, la session, le cycle requête-réponse, la validation technique, le moteur de vues et l'accès standard à la base ;
- le projet doit coder les écrans, les règles métier, les critères de recherche, les relations entre entités et les contrôles spécifiques sur les affectations ;
- les CRUD simples de restaurants, fonctions et collaborateurs restent majoritairement dans les contrôleurs, les Form Requests et les modèles ;
- seule la gestion des affectations justifie une couche de service dédiée, car elle concentre plusieurs règles métier transverses.

Cette organisation évite deux erreurs classiques :

- déplacer artificiellement tout le CRUD simple dans des services sans valeur ajoutée ;
- reconstruire en code maison des briques déjà gérées par Laravel.

### 6.4 Organisation des dossiers

L'organisation cible du projet est la suivante :

```text
app/
	Http/
		Controllers/
			AuthController.php
			DashboardController.php
			RestaurantController.php
			FonctionController.php
			CollaborateurController.php
			AffectationController.php
		Middleware/
			EnsureUserIsAdmin.php
		Requests/
			LoginRequest.php
			StoreRestaurantRequest.php
			UpdateRestaurantRequest.php
			StoreFonctionRequest.php
			UpdateFonctionRequest.php
			StoreCollaborateurRequest.php
			UpdateCollaborateurRequest.php
			StoreAffectationRequest.php
			UpdateAffectationRequest.php
	Models/
		Restaurant.php
		Fonction.php
		Collaborateur.php
		Affectation.php
	Services/
		AffectationService.php
bootstrap/
config/
database/
	migrations/
	seeders/
public/
	index.php
	css/
		app.css
	js/
		app.js
resources/
	views/
		layouts/
		auth/
		dashboard/
		restaurants/
		fonctions/
		collaborateurs/
		affectations/
		components/
routes/
	web.php
storage/
tests/
```

Cette arborescence reprend les conventions Laravel sans les détourner. Le projet ne crée ni dossier `Repositories`, ni routeur custom, ni moteur de templates additionnel.

### 6.5 Détail des vues et des assets front

Le rendu HTML repose exclusivement sur Blade. La structure des vues est figée comme suit :

```text
resources/views/
	layouts/
		app.blade.php
	auth/
		login.blade.php
	dashboard/
		index.blade.php
	restaurants/
		index.blade.php
		create.blade.php
		show.blade.php
		edit.blade.php
	fonctions/
		index.blade.php
		create.blade.php
		edit.blade.php
	collaborateurs/
		index.blade.php
		create.blade.php
		show.blade.php
		edit.blade.php
	affectations/
		index.blade.php
		create.blade.php
		edit.blade.php
	components/
		flash.blade.php
		filters.blade.php
		pagination.blade.php
```

Les principes front sont les suivants :

- `layouts/app.blade.php` constitue le gabarit commun de toutes les pages authentifiées ;
- `auth/login.blade.php` est la seule vue publique ;
- `dashboard/index.blade.php` constitue une page d'orientation après connexion, avec des boutons d'accès direct vers les modules principaux ;
- les vues de liste intègrent systématiquement les formulaires de recherche et de filtrage ;
- les vues `restaurants/show.blade.php` et `collaborateurs/show.blade.php` affichent les données principales, les affectations en cours et l'historique ;
- la création d'une affectation utilise un formulaire unique `affectations/create.blade.php`, accessible depuis la fiche d'un restaurant ou d'un collaborateur, avec préremplissage éventuel par paramètre ;
- les composants partagés regroupent les messages flash, les zones de filtres et la pagination.

Les assets front sont volontairement limités à :

- `public/css/app.css` pour la mise en forme globale ;
- `public/js/app.js` pour les interactions légères strictement nécessaires ;
- aucun framework JavaScript ;
- aucun composant front externe lourd.

### 6.6 Pages de l'interface graphique

L'interface graphique retenue comporte les pages suivantes :

| Domaine | Page | Finalité |
|---|---|---|
| Authentification | Connexion | Permettre l'accès sécurisé à l'application |
| Tableau de bord | Accueil back-office | Fournir une page d'orientation avec des boutons d'accès direct vers les modules principaux |
| Restaurants | Liste des restaurants | Rechercher et filtrer par nom, code postal et ville |
| Restaurants | Création d'un restaurant | Enregistrer un nouveau restaurant |
| Restaurants | Détail d'un restaurant | Consulter ses informations, les affectations en cours et l'historique |
| Restaurants | Modification d'un restaurant | Mettre à jour les données du restaurant |
| Fonctions | Liste des fonctions | Consulter le référentiel des postes |
| Fonctions | Création d'une fonction | Ajouter une fonction au référentiel |
| Fonctions | Modification d'une fonction | Mettre à jour une fonction existante |
| Collaborateurs | Liste des collaborateurs | Rechercher et filtrer par nom, prénom et email |
| Collaborateurs | Vue `non affectés` | Afficher uniquement les collaborateurs sans affectation active |
| Collaborateurs | Création d'un collaborateur | Enregistrer un nouveau collaborateur |
| Collaborateurs | Détail d'un collaborateur | Consulter ses données, ses affectations en cours et son historique |
| Collaborateurs | Modification d'un collaborateur | Mettre à jour ses informations |
| Affectations | Liste transversale des affectations | Rechercher et filtrer par fonction, dates et ville |
| Affectations | Création d'une affectation | Créer une nouvelle affectation depuis une fiche ou depuis la recherche |
| Affectations | Modification d'une affectation en cours | Mettre à jour une affectation encore active |

Le tableau de bord n'est pas conçu comme un écran d'indicateurs complexes. Il sert de point d'entrée pratique après authentification et propose des boutons de navigation vers les écrans principaux : restaurants, collaborateurs, fonctions et affectations.

La vue `non affectés` n'introduit pas un écran technique distinct. Elle correspond au même écran de liste avec un filtre métier activé.

### 6.7 Routes back-office figées

Les routes back-office sont figées ci-dessous. Sauf mention contraire, elles sont protégées par les middlewares d'authentification et d'autorisation administrateur.

| Méthode | Route | Contrôleur | Finalité |
|---|---|---|---|
| GET | `/login` | `AuthController@create` | Afficher le formulaire de connexion |
| POST | `/login` | `AuthController@store` | Authentifier l'utilisateur |
| POST | `/logout` | `AuthController@destroy` | Déconnecter l'utilisateur |
| GET | `/` | `DashboardController@index` | Afficher le tableau de bord de navigation |
| GET | `/restaurants` | `RestaurantController@index` | Lister et filtrer les restaurants |
| GET | `/restaurants/creer` | `RestaurantController@create` | Afficher le formulaire de création |
| POST | `/restaurants` | `RestaurantController@store` | Enregistrer un restaurant |
| GET | `/restaurants/{restaurant}` | `RestaurantController@show` | Afficher la fiche restaurant |
| GET | `/restaurants/{restaurant}/modifier` | `RestaurantController@edit` | Afficher le formulaire de modification |
| PUT | `/restaurants/{restaurant}` | `RestaurantController@update` | Mettre à jour un restaurant |
| GET | `/fonctions` | `FonctionController@index` | Lister les fonctions |
| GET | `/fonctions/creer` | `FonctionController@create` | Afficher le formulaire de création |
| POST | `/fonctions` | `FonctionController@store` | Enregistrer une fonction |
| GET | `/fonctions/{fonction}/modifier` | `FonctionController@edit` | Afficher le formulaire de modification |
| PUT | `/fonctions/{fonction}` | `FonctionController@update` | Mettre à jour une fonction |
| GET | `/collaborateurs` | `CollaborateurController@index` | Lister, rechercher et filtrer les collaborateurs |
| GET | `/collaborateurs/creer` | `CollaborateurController@create` | Afficher le formulaire de création |
| POST | `/collaborateurs` | `CollaborateurController@store` | Enregistrer un collaborateur |
| GET | `/collaborateurs/{collaborateur}` | `CollaborateurController@show` | Afficher la fiche collaborateur |
| GET | `/collaborateurs/{collaborateur}/modifier` | `CollaborateurController@edit` | Afficher le formulaire de modification |
| PUT | `/collaborateurs/{collaborateur}` | `CollaborateurController@update` | Mettre à jour un collaborateur |
| GET | `/affectations` | `AffectationController@index` | Lister et filtrer les affectations |
| GET | `/affectations/creer` | `AffectationController@create` | Afficher le formulaire de création |
| POST | `/affectations` | `AffectationController@store` | Enregistrer une affectation |
| GET | `/affectations/{affectation}/modifier` | `AffectationController@edit` | Afficher le formulaire de modification |
| PUT | `/affectations/{affectation}` | `AffectationController@update` | Mettre à jour une affectation en cours |

Les filtres métier sont portés par les paramètres de requête sur les routes de liste :

- `/restaurants?nom=&code_postal=&ville=`
- `/collaborateurs?nom=&prenom=&email=`
- `/collaborateurs?non_affectes=1`
- `/affectations?fonction=&date_debut=&date_fin=&ville=`

### 6.8 Contrôle d'accès

Le contrôle d'accès est volontairement simple et strict.

- la route de connexion est publique ;
- toutes les autres routes nécessitent une session authentifiée ;
- toutes les fonctionnalités de gestion nécessitent `administrateur = true` sur le collaborateur connecté ;
- un utilisateur authentifié mais non administrateur reçoit un refus d'accès côté serveur ;
- aucune autorisation n'est déléguée au front ;
- aucune table de rôles ni table de permissions n'est introduite.

Le contrôle d'accès est donc assuré à deux niveaux complémentaires :

- authentification par formulaire et session Laravel ;
- autorisation métier par middleware dédié fondé sur l'attribut `administrateur`.

Cette approche répond exactement au besoin métier du projet sans complexité artificielle.

## 7 Persistance et base de données

La persistance du projet repose sur PostgreSQL et sur Eloquent ORM. La base de données stocke uniquement les données durables nécessaires au périmètre Wacdo Bloc 3 : les collaborateurs, les restaurants, les fonctions et les affectations. Elle ne stocke ni rôles avancés, ni permissions, ni planning RH, ni table d'historique séparée.

L'objectif de cette section est de fixer les décisions de stockage : quelles tables existent, quelles relations sont garanties, quelles contraintes protègent les données et comment l'historique des affectations est conservé.

### 7.1 Principes de persistance

PostgreSQL est la base relationnelle de référence du projet. Les accès aux données sont réalisés avec Eloquent ORM à travers les modèles Laravel `Collaborateur`, `Restaurant`, `Fonction` et `Affectation`.

La base conserve les faits métier. Elle ne conserve pas les états calculés. Les états suivants ne sont donc pas stockés dans des colonnes dédiées :

- affectation en cours ;
- affectation future ;
- affectation terminée ;
- collaborateur non affecté.

Ces états sont calculés à la consultation à partir de `date_debut`, `date_fin` et de la date du jour.

```text
Affectation en cours :
date_debut <= date du jour
ET
(date_fin est vide OU date_fin >= date du jour)
```

Cette décision évite les incohérences entre un statut stocké et les dates réelles. Une affectation dont la date de fin change n'a pas besoin de mettre à jour un champ `statut` : son état d'affichage est recalculé automatiquement par la requête ou par le modèle applicatif.

### 7.2 Traduction du modèle métier en tables

Le MCD est traduit en quatre tables métier principales.

| Entité MCD | Table PostgreSQL | Rôle de persistance |
|---|---|---|
| Collaborateur | `collaborateurs` | Stocke les personnes enregistrées, leurs coordonnées, leur droit administrateur et leur mot de passe haché lorsqu'elles disposent d'un accès applicatif. |
| Restaurant | `restaurants` | Stocke les établissements Wacdo dans lesquels les collaborateurs sont affectés. |
| Fonction | `fonctions` | Stocke le référentiel des postes disponibles. |
| Affectation | `affectations` | Stocke le lien daté entre un collaborateur, un restaurant et une fonction. |

La table `affectations` est la table centrale du modèle relationnel. Chaque ligne représente une période d'affectation. Elle répond à la question métier : quel collaborateur travaille dans quel restaurant, sur quelle fonction, et sur quelle période.

Aucune table `utilisateurs` séparée n'est créée. Le collaborateur porte lui-même les informations nécessaires à la connexion : `email`, `password` et `administrateur`. Le champ `password` suit la convention Laravel et contient le hash généré par le framework, jamais le mot de passe en clair.

Aucune table `roles`, `permissions`, `historique_affectations` ou `statuts_affectation` n'est retenue. Ces tables ajouteraient une complexité non demandée par le besoin.

### 7.3 Clés, relations et intégrité référentielle

Chaque table possède une clé primaire technique conforme aux conventions Laravel. La clé primaire est nommée `id` dans chaque table.

| Table | Clé primaire |
|---|---|
| `collaborateurs` | `id` |
| `restaurants` | `id` |
| `fonctions` | `id` |
| `affectations` | `id` |

Cette décision est volontaire. Le MCD conserve des entités métier nommées clairement, mais l'implémentation Laravel utilise les clés techniques standards du framework. Cela évite de configurer inutilement les clés primaires dans chaque modèle Eloquent et rend les relations, les migrations, les factories, les seeders et le route model binding plus simples.

La table `affectations` porte trois clés étrangères obligatoires :

| Colonne | Référence | Règle |
|---|---|---|
| `collaborateur_id` | `collaborateurs.id` | Une affectation concerne un collaborateur existant. |
| `restaurant_id` | `restaurants.id` | Une affectation concerne un restaurant existant. |
| `fonction_id` | `fonctions.id` | Une affectation concerne une fonction existante. |

Ces clés étrangères traduisent les cardinalités du MCD : un collaborateur, un restaurant ou une fonction possède zéro, une ou plusieurs affectations ; une affectation référence obligatoirement un seul collaborateur, un seul restaurant et une seule fonction.

Les clés étrangères utilisent une règle de suppression restrictive. Une donnée déjà utilisée dans une affectation ne doit pas être supprimée physiquement, car elle participe à l'historique métier.

```text
ON DELETE RESTRICT
```

Cette règle empêche la suppression en cascade d'un collaborateur, d'un restaurant ou d'une fonction déjà lié à une affectation.

### 7.4 Contraintes de validité des données

La base de données protège les invariants structurels. Les Form Requests Laravel protègent les formats et les erreurs de saisie avant l'enregistrement.

Les contraintes principales retenues sont les suivantes :

| Table | Contrainte retenue |
|---|---|
| `collaborateurs` | `nom`, `prenom`, `email`, `date_premiere_embauche` et `administrateur` obligatoires. |
| `collaborateurs` | `email` unique pour tous les collaborateurs. |
| `collaborateurs` | `administrateur` vaut `false` par défaut. |
| `collaborateurs` | `password` obligatoire lorsque `administrateur = true`. Le hash est produit par Laravel avec `Hash::make`. |
| `restaurants` | `nom`, `adresse`, `code_postal` et `ville` obligatoires. |
| `fonctions` | `intitule_poste` obligatoire et unique. |
| `affectations` | `collaborateur_id`, `restaurant_id`, `fonction_id` et `date_debut` obligatoires. |
| `affectations` | `date_fin` facultative. |
| `affectations` | `date_fin` supérieure ou égale à `date_debut` lorsqu'elle est renseignée. |
| `affectations` | Doublon strict interdit sur collaborateur, restaurant, fonction, date de début et date de fin. |

La cohérence chronologique d'une affectation est protégée par une contrainte de type `CHECK`.

```sql
CHECK (date_fin IS NULL OR date_fin >= date_debut)
```

Le mot de passe d'un administrateur est également protégé par une contrainte de cohérence.

```sql
CHECK (administrateur = false OR password IS NOT NULL)
```

Le doublon strict d'affectation doit couvrir le cas où `date_fin` est vide. En PostgreSQL, cette règle est portée par un index unique utilisant `NULLS NOT DISTINCT` afin que deux valeurs `NULL` soient considérées comme identiques pour cette contrainte métier.

```sql
CREATE UNIQUE INDEX affectations_unique_strict
ON affectations (
	collaborateur_id,
	restaurant_id,
	fonction_id,
	date_debut,
	date_fin
)
NULLS NOT DISTINCT;
```

Le modèle n'interdit pas plusieurs affectations en cours pour un même collaborateur. Cette décision respecte le besoin fonctionnel qui mentionne les affectations en cours au pluriel. Aucune contrainte d'unicité temporelle par collaborateur n'est ajoutée.

### 7.5 Conservation de l'historique

L'historique des affectations est conservé directement dans la table `affectations`. Une affectation terminée reste une ligne de cette table avec une date de fin renseignée et passée.

Cette approche assure l'affichage de l'historique depuis :

- la fiche d'un collaborateur ;
- la fiche d'un restaurant ;
- la recherche transversale des affectations.

Aucune table `historique_affectations` n'est créée. L'historique demandé par le besoin correspond aux périodes d'affectation enregistrées, pas à un journal technique de toutes les modifications de formulaire.

Les colonnes techniques Laravel de création et de mise à jour d'une ligne ne remplacent pas l'historique métier. L'historique métier reste porté par `date_debut` et `date_fin`.

### 7.6 Règles de modification et de suppression

Les collaborateurs, restaurants et fonctions sont modifiables sans recréer leurs affectations. Une modification met à jour les informations de référence tout en conservant les liens existants avec les affectations.

Les affectations en cours sont modifiables conformément au besoin fonctionnel. Après modification, si la période ne couvre plus la date de consultation, l'affectation sort des affectations en cours et reste consultable dans l'historique.

La suppression métier n'est pas exposée dans le périmètre de l'application. Aucune route `DELETE`, aucun bouton de suppression et aucun mécanisme de suppression logique `deleted_at` ne sont retenus.

Une affectation ne se supprime pas pour signaler la fin d'un poste. Elle se termine par renseignement de `date_fin`.

Les clés étrangères restrictives bloquent la suppression physique d'une donnée référencée par une affectation. Cette règle protège la cohérence de l'historique sans ajouter de mécanisme d'audit ou de table supplémentaire.

### 7.7 Index et performances de recherche

Les index retenus servent les besoins de recherche, de jointure et d'intégrité identifiés dans le CDC fonctionnel. Le projet ne retient pas de moteur de recherche externe, de vue matérialisée ou d'index avancé hors périmètre.

| Index | Justification |
|---|---|
| Clé primaire de chaque table | Accès direct aux lignes et relations Eloquent. |
| Index unique sur `collaborateurs.email` | Authentification et unicité métier. |
| Index unique sur `fonctions.intitule_poste` | Référentiel de postes sans doublon. |
| Index unique strict sur `affectations` | Refus des doublons stricts d'affectation. |
| Index sur `affectations.collaborateur_id` | Affichage des affectations d'un collaborateur. |
| Index sur `affectations.restaurant_id` | Affichage des affectations d'un restaurant. |
| Index sur `affectations.fonction_id` | Filtrage des affectations par fonction. |
| Index sur `affectations.date_debut` et `affectations.date_fin` | Recherche des affectations en cours, futures ou terminées. |
| Index sur `restaurants.ville` et `restaurants.code_postal` | Recherche des restaurants et recherche des affectations par ville. |
| Index sur `collaborateurs.nom`, `collaborateurs.prenom` et `restaurants.nom` | Recherche simple dans les listes de gestion. |

Les index non retenus sont également fixés pour éviter la sur-optimisation :

- aucun index sur `password`, car ce champ n'est jamais recherché directement ;
- aucun index sur `telephone`, car aucun filtre métier ne l'utilise ;
- aucun index sur un statut d'affectation, car aucun statut n'est stocké ;
- aucun index full-text ou trigram, car les recherches attendues restent simples.

### 7.8 Migrations et reproductibilité

La structure de la base est créée et maintenue par les migrations Laravel. Les migrations décrivent les tables, les clés primaires, les clés étrangères, les contraintes d'unicité, les contraintes de dates et les index.

L'ordre de création des tables est figé :

1. `collaborateurs` ;
2. `restaurants` ;
3. `fonctions` ;
4. `affectations`.

Cet ordre garantit que les tables référencées existent avant la création des clés étrangères de la table `affectations`.

Les migrations constituent la référence technique du schéma. Aucune modification manuelle de la base n'est retenue comme procédure normale. Le même schéma est reconstruit localement, en environnement Docker et sur le serveur de déploiement.

Les seeders Laravel servent uniquement à alimenter des données de démonstration nécessaires au développement et à la soutenance. Ils ne remplacent pas les migrations et ne définissent pas la structure de la base.

Un seeder initial crée le premier collaborateur administrateur afin de permettre la première connexion après installation. Ce seeder utilise obligatoirement `Hash::make` pour produire le hash du mot de passe. Aucun mot de passe déjà haché manuellement n'est écrit en dur dans la base ou dans les migrations.

### 7.9 Scopes et requêtes Eloquent à prévoir

Les modèles Eloquent doivent porter les relations et les requêtes réutilisables afin d'éviter de dupliquer les mêmes conditions dans plusieurs contrôleurs.

| Modèle | Scope ou requête | Usage |
|---|---|---|
| `Affectation` | `enCours($date)` | Retourner les affectations actives à une date donnée. |
| `Affectation` | `futures($date)` | Retourner les affectations dont la date de début est postérieure à la date donnée. |
| `Affectation` | `terminees($date)` | Retourner les affectations dont la date de fin est passée. |
| `Affectation` | `filtrer($fonction, $dateDebut, $dateFin, $ville)` | Alimenter la recherche transversale des affectations. |
| `Collaborateur` | `rechercher($nom, $prenom, $email)` | Alimenter la liste filtrée des collaborateurs. |
| `Collaborateur` | `nonAffectes($date)` | Retourner les collaborateurs sans affectation active à la date donnée. |
| `Restaurant` | `rechercher($nom, $codePostal, $ville)` | Alimenter la liste filtrée des restaurants. |
| `Fonction` | `ordonnerParIntitule()` | Afficher le référentiel des fonctions dans un ordre stable. |

Les fiches détail utilisent les relations Eloquent avec chargement explicite des données nécessaires : un restaurant charge ses affectations avec le collaborateur et la fonction ; un collaborateur charge ses affectations avec le restaurant et la fonction. Cette règle évite les requêtes répétées inutiles dans les vues Blade.

## 8 Schéma BDD

Le schéma BDD du projet est matérialisé dans le fichier `SCHEMA_BDD_bloc3_wacdo.drawio`. Il représente la traduction relationnelle du modèle métier retenu pour Bloc 3. Son objectif est de montrer de manière lisible les tables réellement stockées, leurs relations et les contraintes métier essentielles visibles à l'échelle du schéma.

Le diagramme ne remplace pas la section 7. La section 7 décrit les décisions techniques de persistance, les contraintes PostgreSQL, les index et les migrations. La section 8 présente la structure relationnelle finale de façon synthétique et soutenable devant jury.

### 8.1 Structure générale du schéma

Le schéma BDD retient quatre tables métier :

- `collaborateurs` ;
- `restaurants` ;
- `fonctions` ;
- `affectations`.

La table `affectations` est placée au centre du schéma. Elle constitue la table pivot du modèle relationnel. Les tables `collaborateurs`, `restaurants` et `fonctions` sont des tables de référence reliées à `affectations` par des clés étrangères obligatoires.

Cette organisation correspond exactement au besoin métier : une affectation relie un collaborateur, un restaurant et une fonction sur une période donnée.

### 8.2 Tables représentées

Le diagramme affiche les attributs métier réellement stockés dans la base, sans surcharger la lecture avec les détails d'implémentation.

| Table | Attributs visibles dans le schéma |
|---|---|
| `collaborateurs` | `id`, `nom`, `prenom`, `email`, `telephone`, `date_premiere_embauche`, `administrateur`, `password` |
| `restaurants` | `id`, `nom`, `adresse`, `code_postal`, `ville` |
| `fonctions` | `id`, `intitule_poste` |
| `affectations` | `id`, `collaborateur_id`, `restaurant_id`, `fonction_id`, `date_debut`, `date_fin` |

Le schéma affiche également les marqueurs suivants lorsque cela est utile à la lecture :

- `PK` pour les clés primaires ;
- `FK` pour les clés étrangères ;
- `UQ` pour les contraintes d'unicité ;
- `NULL` pour les champs facultatifs.

Le diagramme n'affiche pas les colonnes techniques Laravel de type `created_at`, `updated_at` ou `deleted_at`, car elles n'apportent aucune information métier utile à la lecture du modèle.

### 8.3 Relations représentées

Trois relations seulement sont représentées dans le schéma :

| Relation | Cardinalité affichée | Lecture métier |
|---|---|---|
| `collaborateurs` -> `affectations` | `0,n` -> `1,1` | Un collaborateur peut posséder zéro, une ou plusieurs affectations. Une affectation référence obligatoirement un seul collaborateur. |
| `restaurants` -> `affectations` | `0,n` -> `1,1` | Un restaurant peut être lié à zéro, une ou plusieurs affectations. Une affectation référence obligatoirement un seul restaurant. |
| `fonctions` -> `affectations` | `0,n` -> `1,1` | Une fonction peut être utilisée dans zéro, une ou plusieurs affectations. Une affectation référence obligatoirement une seule fonction. |

Aucune relation directe n'est dessinée entre `collaborateurs` et `restaurants`, entre `collaborateurs` et `fonctions`, ni entre `restaurants` et `fonctions`. Toute relation métier transite par la table `affectations`.

### 8.4 Contraintes visibles sur le schéma

Le diagramme met en évidence les contraintes structurantes suivantes :

- unicité de `email` dans `collaborateurs` ;
- unicité de `intitule_poste` dans `fonctions` ;
- caractère facultatif de `date_fin` dans `affectations` ;
- cohérence chronologique d'une affectation : `date_fin` vide ou supérieure ou égale à `date_debut` ;
- interdiction d'un doublon strict d'affectation ;
- absence de colonne de statut stocké pour les affectations.

Le schéma rappelle également deux décisions métier importantes :

- l'historique métier est porté directement par la table `affectations` ;
- plusieurs affectations en cours pour un même collaborateur restent autorisées par le modèle.

### 8.5 Choix de représentation

Le diagramme a été volontairement limité à un niveau d'information intermédiaire : suffisamment détaillé pour montrer la vraie structure de la base, mais sans devenir un script SQL dessiné.

Les éléments suivants sont donc exclus du schéma graphique et restent documentés dans la section 7 :

- le détail des index ;
- la syntaxe exacte des contraintes PostgreSQL ;
- les règles de migration Laravel ;
- les choix d'implémentation Eloquent ;
- les statuts calculés `en cours`, `future` et `terminée` comme colonnes de base.

Ce choix rend le schéma plus lisible à l'oral. Il permet de défendre clairement la logique métier suivante : le modèle comporte quatre tables, trois relations, une table pivot centrale et aucune table additionnelle inutile.

## 9 Sécurité applicative

La sécurité applicative du projet repose sur deux niveaux complémentaires :

- les protections génériques déjà fournies par Laravel ;
- les règles de sécurité spécifiques au besoin Wacdo.

Laravel apporte le socle technique du projet : sessions serveur, hachage des mots de passe, protection CSRF, échappement Blade, middlewares, validation serveur et requêtes paramétrées via Eloquent ou Query Builder. Le projet doit ensuite configurer et compléter ce socle pour respecter la règle métier centrale suivante : seule une personne identifiée comme collaborateur administrateur peut accéder à l'application.

### 9.1 Authentification et gestion de session

L'authentification repose sur un formulaire de connexion serveur Laravel. L'identifiant retenu est l'adresse email du collaborateur.

Une connexion n'est acceptée que si les quatre conditions suivantes sont réunies :

- le collaborateur existe ;
- l'email correspond à un compte enregistré ;
- le mot de passe fourni correspond au hachage stocké ;
- l'attribut `administrateur` vaut `true`.

Après une connexion réussie, Laravel ouvre une session serveur et l'identifiant de session est immédiatement régénéré. Cette régénération limite le risque de fixation de session.

Après une déconnexion, la session est invalidée et le jeton CSRF est régénéré.

Le périmètre public est réduit au strict minimum :

- `GET /login` ;
- `POST /login`.

Toutes les autres routes sont protégées par les middlewares du groupe web, par le middleware d'authentification et par le contrôle administrateur.

Le cookie de session est configuré avec les attributs suivants :

- `HttpOnly` ;
- `SameSite=Lax` ;
- `Secure` en production.

Le projet ne retient pas de connexion persistante de type `remember me`.

### 9.2 Autorisation et contrôle d'accès

L'autorisation ne repose pas sur une matrice de rôles complexe. Elle repose sur une règle métier unique : un collaborateur authentifié n'accède à l'application que si `administrateur = true`.

Cette règle est appliquée côté serveur par un middleware dédié de type `EnsureUserIsAdmin`, placé sur l'ensemble des routes métier.

Les conséquences sont les suivantes :

- un visiteur non authentifié est redirigé vers la page de connexion ;
- un collaborateur authentifié mais non administrateur reçoit un refus d'accès ;
- un administrateur authentifié peut accéder aux écrans du back-office.

Le contrôle d'accès ne repose jamais sur le seul affichage du menu ou sur le masquage d'un bouton. Même si une URL est appelée directement, le refus doit être appliqué côté serveur.

### 9.3 Protection des mots de passe et du formulaire de connexion

Les mots de passe ne sont jamais stockés en clair. Ils sont hachés via les mécanismes natifs de Laravel, au moment de leur création ou de leur modification.

Le champ technique retenu est `password`, conformément aux conventions d'authentification Laravel. Il est obligatoire pour un collaborateur administrateur. Il reste vide pour un collaborateur sans accès applicatif. Sa valeur est toujours produite par `Hash::make` ou par le mécanisme équivalent fourni par Laravel.

La politique minimale retenue pour le mot de passe administrateur est la suivante :

- mot de passe obligatoire ;
- longueur minimale de 12 caractères ;
- stockage exclusif sous forme hachée.

Le formulaire de connexion est durci par une limitation des tentatives. La route de connexion utilise un throttling applicatif fondé sur l'adresse IP et l'email saisi. La règle retenue est de 5 tentatives maximum par minute, au-delà desquelles la connexion est temporairement bloquée.

Les messages d'erreur de connexion restent volontairement génériques. L'application ne doit jamais indiquer si l'email existe ou si seul le mot de passe est incorrect.

### 9.4 Protection des formulaires et validation des entrées

Tous les formulaires du back-office sont protégés par jeton CSRF. Aucune écriture métier n'est acceptée sans ce contrôle.

Toutes les validations sont réalisées côté serveur via des Form Requests Laravel. Les contrôles du navigateur ne servent que d'aide à la saisie et ne remplacent jamais la validation serveur.

Les validations applicatives portent notamment sur :

- les noms et prénoms ;
- l'email ;
- le téléphone ;
- l'adresse ;
- le code postal ;
- les dates d'affectation ;
- les identifiants de collaborateur, restaurant et fonction ;
- l'unicité de l'email ;
- l'unicité de l'intitulé de fonction ;
- la cohérence `date_fin >= date_debut`.

Les données textuelles sont normalisées avant persistance :

- suppression des espaces parasites ;
- email enregistré en minuscules ;
- valeurs vides non conservées comme données métier utiles.

Les opérations d'écriture restent limitées aux méthodes HTTP prévues à cet effet. Aucune création, modification ou suppression logique n'est portée par une route `GET`.

Les modèles Laravel définissent explicitement les champs modifiables afin d'éviter une affectation massive indésirable sur des champs sensibles comme `administrateur` ou `password`.

### 9.5 Protection contre les attaques web courantes

La protection contre les attaques web courantes repose sur les pratiques suivantes.

**Contre les injections SQL**

- accès aux données uniquement via Eloquent ORM ou Query Builder ;
- aucune requête SQL brute construite à partir d'entrées utilisateur ;
- usage des paramètres liés lorsque du SQL ciblé devient nécessaire.

**Contre les attaques XSS**

- affichage des données via Blade avec échappement par défaut ;
- interdiction d'utiliser du HTML brut pour afficher des données saisies par les utilisateurs ;
- absence de besoin métier justifiant l'usage de contenu HTML fourni par l'utilisateur.

**Contre les falsifications de requête**

- jeton CSRF sur tous les formulaires du back-office ;
- session Laravel requise pour toute action d'écriture.

**Contre les contournements d'URL**

- middleware d'authentification ;
- middleware administrateur ;
- vérification serveur des ressources chargées et des identifiants fournis.

### 9.6 Intégrité des données comme mesure de sécurité

La sécurité applicative ne se limite pas à la connexion. L'intégrité des données constitue également une mesure de sécurité, car elle empêche l'enregistrement d'états incohérents ou manipulés.

Le projet s'appuie donc sur les contraintes suivantes déjà fixées dans la section 7 :

- unicité de l'email collaborateur ;
- unicité de l'intitulé de fonction ;
- clés étrangères obligatoires dans `affectations` ;
- cohérence chronologique des dates ;
- interdiction des doublons stricts d'affectation ;
- suppression physique non exposée pour préserver l'historique.

Ces contraintes protègent l'application contre des écritures incohérentes, qu'elles proviennent d'une erreur de saisie, d'une mauvaise implémentation ou d'une tentative de contournement métier.

### 9.7 Sécurisation de la configuration et de la production

La configuration de production doit rester cohérente avec le niveau de sécurité attendu d'une application interne.

Les règles suivantes sont retenues :

- `APP_DEBUG=false` en production ;
- utilisation obligatoire du HTTPS sur le serveur déployé ;
- stockage des secrets d'environnement hors du code source ;
- aucun mot de passe ni donnée sensible stocké en clair dans les logs ;
- exposition minimale des messages d'erreur techniques à l'utilisateur final.

Le framework ne suffit pas à lui seul sur ce point. Ces protections relèvent de la configuration réelle de l'environnement Nginx, PHP-FPM et Laravel au moment du déploiement.

### 9.8 Tests de sécurité à prévoir

Le référentiel exige des tests de sécurité avant déploiement. Les contrôles minimaux retenus pour le projet sont les suivants :

- vérifier qu'un utilisateur non authentifié ne peut accéder à aucune route métier ;
- vérifier qu'un collaborateur non administrateur ne peut pas accéder au back-office ;
- vérifier qu'un administrateur authentifié accède aux écrans autorisés ;
- vérifier qu'une requête sans jeton CSRF valide est rejetée ;
- vérifier qu'un formulaire invalide est refusé côté serveur ;
- vérifier qu'un mot de passe n'est jamais stocké en clair ;
- vérifier qu'un doublon strict d'affectation est refusé ;
- vérifier que la limitation des tentatives de connexion fonctionne.

Ces tests couvrent le niveau de sécurité réellement attendu sur ce projet. Ils sont suffisants pour démontrer une application correctement protégée sans introduire une complexité de sécurité disproportionnée.

### 9.9 Mesures volontairement non retenues

Les mécanismes suivants ne sont pas retenus, car ils surdimensionnent le projet par rapport au besoin réel :

- aucun système de rôles et permissions complexe ;
- aucun JWT ni token API applicatif ;
- aucune authentification multi-facteurs ;
- aucun SSO, LDAP ou OAuth ;
- aucun moteur de sécurité orienté API publique.

Le projet retient une sécurité simple, lisible et rigoureuse, adaptée à un back-office interne Laravel centré sur l'authentification, le contrôle administrateur, la validation serveur et l'intégrité des données.

## 10 Environnement de développement

L'environnement de développement local est standardisé avec Docker afin de lancer le projet de manière identique sur plusieurs postes sans imposer une installation manuelle de PHP, Composer, Nginx ou PostgreSQL sur la machine hôte. Docker ne suffit cependant pas, à lui seul, à décrire l'environnement de développement : il faut aussi préciser les services exécutés, la configuration locale, les volumes de travail et la procédure de démarrage Laravel.

### 10.1 Objectifs

- homogénéiser l'environnement de développement entre les postes ;
- isoler les dépendances techniques du système hôte ;
- reproduire localement l'architecture cible `Nginx + PHP-FPM + PostgreSQL` ;
- simplifier l'initialisation Laravel avec Composer, Artisan, migrations et seeders ;
- limiter les écarts entre l'environnement local et l'environnement de déploiement.

### 10.2 Composition minimale retenue

L'environnement local est orchestré avec Docker Compose. La composition minimale retenue comprend les trois services suivants :

| Service | Rôle |
|---|---|
| `app` | Conteneur PHP 8.3 exécutant Laravel 11 via PHP-FPM et Composer |
| `web` | Conteneur Nginx servant le dossier `public` et relayant les scripts PHP vers `app` |
| `db` | Conteneur PostgreSQL hébergeant la base de données locale du projet |

Cette composition est suffisante pour le Bloc 3. Aucun autre service n'est retenu dans l'environnement minimal.

Le conteneur `app` embarque PHP 8.3, Composer et les extensions nécessaires au projet Laravel, en particulier `pdo_pgsql`, `mbstring`, `ctype`, `json`, `tokenizer`, `xml`, `fileinfo` et `session`. Les images Docker utilisées sont figées sur une version explicite et n'utilisent pas le tag `latest`.

Le service `web` repose sur Nginx avec le dossier `public` comme racine web. Il ne sert jamais la racine du projet afin d'éviter l'exposition du code source, du fichier `.env`, du répertoire `database/` et des fichiers internes Laravel.

Le code source du projet est monté en volume dans les conteneurs `app` et `web` afin que les modifications locales soient prises en compte sans reconstruction systématique de l'image.

Le conteneur `db` héberge PostgreSQL 16. Les données sont stockées dans un volume dédié afin de conserver la base entre deux redémarrages des conteneurs. Depuis Laravel, la base est jointe par le nom du service Docker `db`, et non par `localhost`.

Docker Compose inclut une vérification de disponibilité de PostgreSQL afin d'éviter que l'application n'exécute les migrations avant le démarrage complet du moteur.

### 10.3 Configuration locale

La configuration locale sépare clairement les éléments versionnés et les secrets propres à chaque poste.

- les fichiers de code source, la configuration Docker et les fichiers de configuration applicative génériques sont versionnés ;
- les secrets techniques et les valeurs spécifiques à un poste ne sont pas versionnés ;
- un fichier `.env.example` est versionné avec des valeurs factices ;
- un fichier `.env` local, ignoré par Git, fournit les variables d'environnement réelles nécessaires au lancement.

Variables retenues pour l'environnement local :

| Variable | Valeur retenue ou règle locale | Rôle |
|---|---|---|
| `APP_NAME` | `Wacdo` | Nom applicatif Laravel |
| `APP_ENV` | `local` | Mode d'exécution local |
| `APP_KEY` | secret généré localement | Clé d'application Laravel |
| `APP_DEBUG` | `true` | Affichage des erreurs en environnement local |
| `APP_URL` | `http://localhost:8080` | URL locale d'accès au back-office |
| `APP_PORT` | `8080` | Port HTTP local exposé par Docker |
| `DB_CONNECTION` | `pgsql` | Driver Laravel retenu |
| `DB_HOST` | `db` | Hôte PostgreSQL utilisé par l'application |
| `DB_PORT` | `5432` | Port PostgreSQL interne au réseau Docker |
| `DB_DATABASE` | `wacdo_dev` | Nom de la base de données locale |
| `DB_USERNAME` | `wacdo` | Compte PostgreSQL utilisé par l'application |
| `DB_PASSWORD` | secret local non versionné | Mot de passe du compte PostgreSQL |
| `SESSION_DRIVER` | `file` | Stockage local des sessions Laravel |

La variable `APP_KEY` est générée par Laravel lors de l'initialisation locale. Elle ne doit jamais être versionnée.

### 10.4 Volumes, ports et données de travail

Le code source du projet est partagé entre l'hôte et les conteneurs applicatifs par montage de volume. Les données PostgreSQL sont conservées dans un volume nommé dédié au service `db`.

L'application web est exposée sur le port HTTP local `8080` afin de permettre l'accès au back-office depuis un navigateur. PostgreSQL n'est pas exposé sur la machine hôte. Il reste accessible uniquement sur le réseau Docker interne.

Les répertoires Laravel `storage/` et `bootstrap/cache/` doivent rester accessibles en écriture par le conteneur `app`, car ils portent les logs, les caches et les fichiers temporaires nécessaires au framework.

Les fichiers `.env`, les dumps de base, les données locales de démonstration et les éventuels secrets ne doivent pas être suivis par Git. Les données de production ne doivent pas être utilisées dans l'environnement local.

### 10.5 Initialisation et démarrage

Le démarrage local suit la logique suivante :

1. préparer le fichier `.env` local ;
2. démarrer les services Docker Compose ;
3. installer les dépendances PHP avec Composer dans le conteneur `app` ;
4. générer la clé d'application Laravel ;
5. exécuter les migrations ;
6. créer le premier collaborateur administrateur via un seeder utilisant `Hash::make` ;
7. charger les données minimales de démonstration via les seeders ;
8. vérifier l'accès au back-office dans le navigateur.

Procédure de lancement retenue :

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose logs -f app
```

L'initialisation de la base est réalisée par les migrations Laravel. Les données de démonstration sont chargées par les seeders Laravel. Le premier compte administrateur est créé par un seeder applicatif afin de rendre la connexion possible dès la fin du Sprint 0. Aucune initialisation manuelle par script SQL n'est retenue dans le Bloc 3.

La réinitialisation locale s'effectue par suppression du volume PostgreSQL local puis par réexécution des migrations et des seeders.

### 10.6 Vérifications locales minimales

Après démarrage de l'environnement, les vérifications minimales sont les suivantes :

- le back-office est accessible depuis le navigateur via l'URL locale ;
- la page de connexion s'affiche sans erreur ;
- la connexion avec un compte administrateur de développement fonctionne ;
- PostgreSQL est accessible depuis Laravel ;
- les migrations ont bien créé les quatre tables métier ;
- les listes restaurants, collaborateurs, fonctions et affectations sont atteignables après authentification.

Ces vérifications ne remplacent pas la stratégie de tests détaillée dans le projet. Elles servent uniquement à confirmer que l'environnement local est correctement lancé.

### 10.7 Outils complémentaires

En complément de Docker, l'environnement de développement suppose l'usage des outils suivants :

- Docker Engine et Docker Compose ;
- Git pour le versionnement du code source ;
- Visual Studio Code ;
- un navigateur web moderne pour tester le back-office ;
- un terminal shell pour lancer Docker, Composer et Artisan.

## 11 Déploiement

Le déploiement cible du projet est un déploiement sur VPS Linux, fondé sur Docker et Docker Compose. Aucune installation manuelle de PHP, Nginx ou PostgreSQL sur l'hôte n'est requise : tous les services s'exécutent dans des conteneurs.

### 11.1 Prérequis système

Le déploiement nécessite les composants suivants sur la machine hôte :

| Composant | Version minimale | Rôle |
|---|---|---|
| Docker Engine | 24.x | Moteur de conteneurisation |
| Docker Compose | 2.x | Orchestration des services `app`, `web` et `db` |
| Git | version récente | Récupération du code source |
| `curl` | présent nativement sur Linux | Vérification HTTP post-déploiement |

L'installation de PHP, de Composer, de Nginx ou de PostgreSQL sur la machine hôte n'est pas requise. Ces dépendances sont encapsulées dans les images Docker définies dans `docker-compose.yml`.

### 11.2 Composition des conteneurs de production

Le service `app` repose sur une image PHP 8.3-FPM contenant Laravel, Composer et les extensions PHP nécessaires.

Le service `web` repose sur une image Nginx configurée pour :

- servir uniquement le dossier `public/` ;
- transmettre les scripts PHP au service `app` ;
- ne jamais exposer le code source ou le fichier `.env`.

Le service `db` repose sur l'image officielle PostgreSQL 16. Les données sont conservées dans un volume Docker dédié.

Le projet ne retient aucun service Redis, Node, queue worker séparé ou service de mail dédié, car ils ne sont pas nécessaires au périmètre Bloc 3.

### 11.3 Procédure de déploiement

La procédure complète de premier déploiement est la suivante. Elle s'applique sur un serveur Linux recevant le projet.

**Étape 1 — Récupérer le code source**

```bash
git clone <url-du-depot> wacdo-bloc3
cd wacdo-bloc3
```

**Étape 2 — Configurer les variables d'environnement**

```bash
cp .env.example .env
```

Le fichier `.env` est ensuite renseigné avec les valeurs de production, notamment :

- `APP_ENV=production` ;
- `APP_DEBUG=false` ;
- l'URL réelle de l'application ;
- les identifiants PostgreSQL ;
- la configuration de session.

**Étape 3 — Démarrer les conteneurs**

```bash
docker compose up -d --build
```

**Étape 4 — Installer les dépendances applicatives**

```bash
docker compose exec app composer install --no-dev --optimize-autoloader
```

**Étape 5 — Générer la clé d'application**

```bash
docker compose exec app php artisan key:generate --force
```

**Étape 6 — Exécuter les migrations**

```bash
docker compose exec app php artisan migrate --force
```

**Étape 7 — Charger le premier administrateur et les données minimales si nécessaire**

```bash
docker compose exec app php artisan db:seed --force
```

Le seeding de production reste limité aux données nécessaires à la démonstration et au premier accès administrateur. Le mot de passe du compte administrateur est haché par Laravel au moment de l'insertion.

**Étape 8 — Optimiser le framework pour la production**

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

Le déploiement retient les migrations Laravel comme procédure normale d'évolution du schéma. Aucune exécution manuelle de scripts SQL n'est retenue.

### 11.4 Variables d'environnement de déploiement

Variables côté application Laravel :

| Variable | Valeur attendue | Obligatoire |
|---|---|---|
| `APP_ENV` | `production` sur le serveur | Oui |
| `APP_KEY` | secret d'application généré | Oui |
| `APP_DEBUG` | `false` en production | Oui |
| `APP_URL` | URL réelle d'accès au back-office | Oui |
| `APP_PORT` | port HTTP exposé par Docker | Oui |
| `DB_CONNECTION` | `pgsql` | Oui |
| `DB_HOST` | `db` | Oui |
| `DB_PORT` | `5432` | Oui |
| `DB_DATABASE` | base utilisée en production | Oui |
| `DB_USERNAME` | compte PostgreSQL applicatif | Oui |
| `DB_PASSWORD` | secret non versionné | Oui |
| `SESSION_DRIVER` | `file` | Oui |

Variables côté conteneur PostgreSQL :

| Variable Docker | Valeur | Obligatoire |
|---|---|---|
| `POSTGRES_DB` | base créée au premier démarrage | Oui |
| `POSTGRES_USER` | compte PostgreSQL applicatif | Oui |
| `POSTGRES_PASSWORD` | mot de passe du compte | Oui |

Le fichier `.env` n'est jamais versionné. Seul `.env.example` sert de référence de configuration.

### 11.5 Contrôle post-déploiement

Après chaque déploiement, les vérifications minimales suivantes sont effectuées :

```bash
docker compose ps
docker compose exec app php artisan migrate:status
curl -s -o /dev/null -w "%{http_code}" http://localhost:${APP_PORT:-8080}/login
```

Le déploiement est considéré comme valide lorsque :

- les services `app`, `web` et `db` sont démarrés ;
- les migrations sont appliquées sans erreur ;
- la page de connexion répond avec un code HTTP 200 ;
- un compte administrateur de test peut se connecter ;
- aucune page de debug ou erreur technique sensible n'est exposée.

### 11.6 Réinitialisation et mise à jour du schéma

En cas de réinitialisation complète, la base peut être recréée en supprimant le volume PostgreSQL puis en réexécutant la procédure de migration et de seeding.

En cours de développement ou lors d'une évolution normale du schéma, la méthode retenue n'est pas la recréation systématique par SQL brut, mais l'application des migrations Laravel incrémentales. Ce point distingue clairement le Bloc 3 du Bloc 2.

## 12 Conventions de développement

Le développement du projet suit des conventions simples afin de garder un code lisible, cohérent et facile à maintenir pendant la réalisation et lors de la soutenance.

### 12.1 Principes généraux

- le projet est développé sous Laravel 11 avec une architecture MVC monolithique ;
- les conventions natives du framework sont privilégiées avant toute surcouche personnalisée ;
- chaque couche conserve une responsabilité claire ;
- le code reste simple, lisible et limité aux besoins du Bloc 3 ;
- toute duplication de logique doit être évitée ou factorisée ;
- une couche de service n'est introduite que lorsqu'une vraie logique métier transverse le justifie, en l'occurrence `AffectationService`.

### 12.2 Règles de nommage

- les classes sont nommées en PascalCase ;
- les contrôleurs se terminent par `Controller` ;
- les Form Requests se terminent par `Request` ;
- les services se terminent par `Service` ;
- les modèles Eloquent portent un nom métier au singulier ;
- les méthodes et les variables sont nommées en camelCase ;
- les constantes sont nommées en majuscules avec underscore ;
- les vues Blade utilisent des noms explicites par ressource et par action ;
- les migrations suivent la convention Laravel `create_xxx_table` ou `add_xxx_to_xxx_table`.
- les clés primaires des tables sont nommées `id` ;
- les clés étrangères suivent la convention Laravel `collaborateur_id`, `restaurant_id` et `fonction_id`.

### 12.3 Répartition des responsabilités

- les contrôleurs reçoivent la requête, déclenchent le traitement adapté et préparent la réponse ;
- les Form Requests portent la validation des formulaires ;
- `AffectationService` porte la logique métier transverse propre aux affectations ;
- les modèles Eloquent centralisent les relations et l'accès aux données ;
- les middlewares gèrent l'authentification et le contrôle administrateur ;
- les vues Blade se limitent à l'affichage HTML ;
- aucune requête SQL n'est écrite dans les vues ;
- aucune couche repository supplémentaire n'est retenue.

### 12.4 Règles de qualité et de sécurité

- toute donnée reçue est validée côté serveur ;
- les accès aux données passent par Eloquent ou Query Builder ;
- aucun secret n'est écrit en dur dans le code source ;
- les messages d'erreur techniques ne sont pas affichés à l'utilisateur final en production ;
- les contrôles d'autorisation sont systématiquement réalisés côté serveur ;
- les champs sensibles sont protégés contre l'affectation massive ;
- les règles métier d'affectation restent centralisées et ne sont pas dupliquées dans plusieurs contrôleurs.

### 12.5 Formatage minimal

- l'indentation retenue est de 4 espaces ;
- l'encodage des fichiers texte est UTF-8 ;
- le code PHP suit l'esprit de PSR-12 ;
- les commentaires sont ajoutés uniquement lorsqu'ils apportent une information utile ;
- les méthodes restent courtes et centrées sur une seule responsabilité ;
- le code inutilisé est supprimé avant livraison.

## 13 Plan de développement par sprints

Le développement est découpé en sprints courts et progressifs. Le Sprint 0 sert à installer et stabiliser le socle technique. Les sprints suivants ajoutent les fonctionnalités dans l'ordre le plus simple à tester : base de données, authentification, référentiels, affectations, recherches, puis livraison.

### Sprint 0 — Mise en place technique

Objectif : disposer d'un projet Laravel 11 exécutable localement.

Travaux à réaliser :

- installer Laravel 11 ;
- mettre en place Docker Compose avec les services `app`, `web` et `db` ;
- configurer PHP 8.3, Nginx, PHP-FPM et PostgreSQL ;
- créer le fichier `.env.example` et préparer le `.env` local ;
- vérifier la connexion Laravel vers PostgreSQL ;
- préparer la structure des dossiers applicatifs ;
- créer le seeder du premier collaborateur administrateur avec `Hash::make` ;
- lancer une première commande de migration et de seeding.

Critère de fin : le projet démarre en local, la page de connexion est accessible et un compte administrateur de développement peut être créé par seeder.

### Sprint 1 — Schéma BDD et modèles Eloquent

Objectif : créer la structure relationnelle stable du projet.

Travaux à réaliser :

- écrire les migrations des tables `collaborateurs`, `restaurants`, `fonctions` et `affectations` ;
- utiliser les clés primaires `id` et les clés étrangères `collaborateur_id`, `restaurant_id`, `fonction_id` ;
- ajouter les contraintes d'unicité sur `email` et `intitule_poste` ;
- ajouter la contrainte `date_fin IS NULL OR date_fin >= date_debut` ;
- ajouter l'index unique strict PostgreSQL avec `NULLS NOT DISTINCT` ;
- créer les modèles Eloquent et leurs relations ;
- ajouter les casts nécessaires sur les dates et les booléens.

Critère de fin : les migrations s'exécutent sans erreur, les tables sont créées et les relations Eloquent sont prêtes.

### Sprint 2 — Authentification et contrôle administrateur

Objectif : sécuriser l'accès à l'application avant de coder les écrans métier.

Travaux à réaliser :

- créer le modèle `Collaborateur` authentifiable ;
- coder le formulaire de connexion ;
- utiliser Laravel pour vérifier le mot de passe haché ;
- coder la déconnexion ;
- créer le middleware `EnsureUserIsAdmin` ;
- protéger toutes les routes métier ;
- tester le refus des visiteurs non authentifiés et des collaborateurs non administrateurs.

Critère de fin : seul un collaborateur administrateur authentifié accède au back-office.

### Sprint 3 — CRUD des référentiels

Objectif : développer les écrans simples avant les affectations.

Travaux à réaliser :

- coder la gestion des fonctions ;
- coder la gestion des restaurants ;
- coder la gestion des collaborateurs ;
- créer les Form Requests de validation ;
- mettre en place les vues Blade de liste, création, détail et modification ;
- ajouter les recherches simples sur restaurants et collaborateurs.

Critère de fin : les trois référentiels sont consultables, créables, modifiables et validés côté serveur.

### Sprint 4 — Gestion des affectations

Objectif : coder le cœur métier du projet.

Travaux à réaliser :

- créer le formulaire unique de création d'affectation ;
- permettre la création depuis une fiche collaborateur ou restaurant ;
- coder `AffectationService` ;
- refuser les doublons stricts ;
- vérifier la cohérence des dates ;
- permettre la modification d'une affectation en cours ;
- calculer les états en cours, future et terminée sans les stocker en base.

Critère de fin : une affectation peut être créée et modifiée selon les règles métier, sans doublon strict ni incohérence de dates.

### Sprint 5 — Recherches, détails et historiques

Objectif : rendre l'application réellement exploitable par l'administrateur.

Travaux à réaliser :

- coder la recherche transversale des affectations ;
- afficher les affectations en cours sur les fiches restaurant et collaborateur ;
- afficher l'historique des affectations ;
- ajouter les filtres par fonction, nom, ville et dates ;
- coder la vue des collaborateurs non affectés ;
- utiliser les scopes Eloquent prévus pour éviter la duplication de requêtes.

Critère de fin : toutes les recherches demandées par le référentiel sont disponibles dans le back-office.

### Sprint 6 — Tests, sécurité et durcissement

Objectif : vérifier que l'application respecte les règles métier et les exigences de sécurité.

Travaux à réaliser :

- tester l'authentification administrateur ;
- tester le refus des accès non autorisés ;
- tester les validations de formulaires ;
- tester les règles d'affectation ;
- tester le refus d'un doublon strict ;
- tester les filtres principaux ;
- vérifier la protection CSRF et l'absence de mot de passe en clair.

Critère de fin : les parcours critiques sont couverts par des tests fonctionnels et de sécurité.

### Sprint 7 — Déploiement et préparation soutenance

Objectif : livrer une application démontrable et défendable devant jury.

Travaux à réaliser :

- préparer l'environnement de production Docker Compose ;
- exécuter les migrations et seeders sur le serveur ;
- vérifier les variables d'environnement de production ;
- désactiver le mode debug ;
- préparer les données de démonstration ;
- contrôler l'alignement entre l'application, le CDC et le schéma BDD ;
- préparer les explications sur Laravel, MVC, Eloquent, Blade, sécurité et migrations.

Critère de fin : l'application est déployée, testable, cohérente avec le CDC et prête pour la soutenance.