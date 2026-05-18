# Wacdo Back-office

Application de gestion de commandes pour borne de commande Wacdo. Back-office PHP 8.2 MVC, PostgreSQL 16, Docker.

## Démarrage rapide

### 1. Cloner le projet
```bash
git clone <repo-url> && cd BLOC2_wcdo
```

### 2. Configurer l'environnement
```bash
cp .env.example .env
```
Éditer `.env` si nécessaire (secrets de production, domaine, etc.). Les valeurs par défaut conviennent pour le développement local.

### 3. Démarrer les conteneurs
```bash
docker compose up -d
```

### 4. Initialiser la base de données
```bash
# Créer le schéma
docker exec -i wacdo_db psql -U wacdo -d wacdo_dev < database/schema.sql

# Charger les données de test
docker exec -i wacdo_db psql -U wacdo -d wacdo_dev < database/seed_dev.sql
```

### 5. Accéder à l'application
- **Back-office** : http://localhost:8080
- **API** : http://localhost:8080/api/catalogue

## Comptes de test

| Identifiant | Mot de passe | Rôle |
|---|---|---|
| `admin` | `wacdo2026` | Administration |
| `prepa` | `wacdo2026` | Preparation |
| `accueil` | `wacdo2026` | Accueil |

## Structure du projet

```
app/
  Controllers/      # Contrôleurs (héritent de BaseController)
  Core/            # BaseController, Router, Database
  Repositories/    # Accès données (requêtes préparées, normalisation)
  Services/        # Métier (TraceService, LoginAttemptService, etc.)
  Views/           # Templates PHP
public/
  index.php        # Point d'entrée, session, routeur
  css/             # Feuilles de style
  js/              # JavaScript vanilla
  uploads/         # Fichiers serveur (images produits)
database/
  schema.sql       # Schéma PostgreSQL
  seed_dev.sql     # Données de développement
docker-compose.yml  # Services PostgreSQL + Apache/PHP
```

## Architecture

- **MVC** : Router → Controller → Service/Repository → View
- **Authentification** : Session PHP, token CSRF, rechargement utilisateur à chaque requête
- **API externe** : Endpoints `/api/catalogue` (GET) et `/api/commandes` (POST), protégés par `X-API-Key`
- **Base de données** : PostgreSQL 16, requêtes préparées, soft delete (`actif` boolean)
- **Sécurité** : Hachage argon2id (cost 12), protection CSRF, blocage force brute (après 5 tentatives échouées)

## Outils utiles

### Lint PHP
```bash
docker exec wacdo_app php -l app/Controllers/CommandeController.php
```

### Accès BDD
```bash
docker exec -it wacdo_db psql -U wacdo -d wacdo_dev
```

### Logs applicatifs
```bash
docker compose logs -f wacdo_app
```

### Arrêter
```bash
docker compose down
```

## Tests de sécurité

Voir `TESTS_SECURITE.md` pour la grille complète. Tests manuels : navigateur, curl, SQL.

## Déploiement

1. ✅ Vérifier que tous les tests critiques passent (voir section 10.3 du CDC technique)
2. ✅ Configurer les secrets (`DB_PASSWORD`, `API_KEY`) en variables d'environnement
3. ✅ Activer `APP_DEBUG=false`
4. ✅ Configurer un reverse proxy SSL
5. ✅ Vérifier les logs après mise en production

## Support

- **CDC technique** : `CDC_technique_bloc2.md`
- **CDC fonctionnel** : `CDC_fonctionnel_bloc2.md`
- **Référentiel RNCP** : https://www.francecompetences.fr/recherche/rncp/37805

---

*Bloc 2 WACDO — Développement back-end from scratch, PHP 8.2 MVC, PostgreSQL 16*
