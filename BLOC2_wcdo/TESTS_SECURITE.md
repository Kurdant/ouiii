# Grille de Tests de Sécurité — Bloc 2 WACDO

## Principes

Tous les tests de sécurité listés ci-dessous sont conformes à la **section 10.2 et 10.3 du CDC technique**. Ils couvrent :
- Authentification et contrôle d'accès
- Protection contre les attaques courantes (CSRF, injection, force brute)
- Intégrité des données
- Validation des uploads

**Outils** : Navigateur (Firefox/Chrome), `curl`, requêtes SQL.

---

## Scénarios critiques (section 10.3)

| # | Scénario | Outil | Procédure | Résultat attendu | Résultat obtenu | Date | Validé |
|---|---|---|---|---|---|---|---|
| 1 | Connexion administrateur valide | Navigateur | Login `admin` / `wacdo2026` | Session ouverte, accès back-office | ⬜ | | |
| 2 | Mot de passe invalide | Navigateur | Login `admin` / `mauvais_pwd` | Erreur générique, pas de fuite "identifiant correct" | ⬜ | | |
| 3 | Compte désactivé (`actif=false`) | Navigateur | Login compte désactivé (voir seed_dev.sql) | Refus connexion, aucune session | ⬜ | | |
| 4 | Blocage après N tentatives | Navigateur | 5 tentatives échouées consécutives | Compte bloqué 15 min, tentative 6 refusée | ⬜ | | |
| 5 | Accès URL admin depuis rôle `Accueil` | Navigateur | Login `accueil`, accès direct `/produits` | Redirect login ou 403 | ⬜ | | |
| 6 | POST sans token CSRF | curl | POST `/produits/1/desactiver` sans `_csrf` | 403 Forbidden | ⬜ | | |
| 7 | CSRF token replay | curl | POST avec ancien token (après 1ère utilisation) | 403 Forbidden (rotation appliquée) | ⬜ | | |
| 8 | Upload image valide (JPEG) | Navigateur | Ajouter produit, upload `image.jpg` | ✅ Enregistré, stocké en `/storage/uploads` | ⬜ | | |
| 9 | Upload image valide (PNG) | Navigateur | Ajouter produit, upload `image.png` | ✅ Enregistré, stocké en `/storage/uploads` | ⬜ | | |
| 10 | Upload fichier interdit (.php) | Navigateur | Ajouter produit, upload `shell.php` | ❌ Refusé, msg erreur, rien en disque | ⬜ | | |
| 11 | Upload fichier interdit (.exe) | Navigateur | Ajouter produit, upload `virus.exe` | ❌ Refusé, msg erreur, rien en disque | ⬜ | | |
| 12 | Upload taille excessive | Navigateur | Ajouter produit, upload fichier > 5 MB | ❌ Refusé, msg erreur | ⬜ | | |
| 13 | API sans clé (`GET /api/catalogue`) | curl | `curl http://localhost:8080/api/catalogue` | 401 Unauthorized | ⬜ | | |
| 14 | API clé invalide | curl | `curl -H "X-API-Key: badkey" .../api/catalogue` | 401 Unauthorized | ⬜ | | |
| 15 | API clé valide | curl | `curl -H "X-API-Key: <clé_env>" .../api/catalogue` | 200 OK, JSON catalogue | ⬜ | | |
| 16 | API POST commande valide | curl | POST `/api/commandes` avec payload valide | 201 Created, commande sauvegardée | ⬜ | | |
| 17 | API POST commande produit inexistant | curl | POST `/api/commandes` avec `id_produit` invalide | 400 Bad Request | ⬜ | | |
| 18 | API POST commande menu incomplet | curl | POST `/api/commandes` avec menu sans section obligatoire | 400 Bad Request | ⬜ | | |
| 19 | Injection SQL (tentative) | Navigateur / curl | Recherche / création avec `' OR '1'='1` | Échappé, traité comme texte, pas d'injection | ⬜ | | |
| 20 | XSS stocké (tentative) | Navigateur | Ajouter catégorie avec `<script>alert(1)</script>` | Échappé en HTML, affichage sûr | ⬜ | | |

---

## Tests complémentaires (section 10.2)

| # | Domaine | Outil | Procédure | Résultat attendu | Résultat obtenu | Date | Validé |
|---|---|---|---|---|---|---|---|
| 21 | Création produit (Admin) | Navigateur | Créer produit test | ✅ Enregistré | ⬜ | | |
| 22 | Modification produit (Admin) | Navigateur | Modifier produit test | ✅ Mis à jour | ⬜ | | |
| 23 | Désactivation produit (Admin) | Navigateur | Désactiver produit test | ✅ `actif=false` | ⬜ | | |
| 24 | Création produit (Prépa) | Navigateur | Login `prepa`, créer produit | ❌ Accès refusé | ⬜ | | |
| 25 | Création commande (Accueil) | Navigateur | Login `accueil`, créer commande | ✅ Autorisé | ⬜ | | |
| 26 | Remise commande (Accueil) | Navigateur | Login `accueil`, passer commande à "livrée" | ✅ Autorisé | ⬜ | | |
| 27 | Remise commande (Prépa) | Navigateur | Login `prepa`, tenter remise commande | ❌ Accès refusé | ⬜ | | |
| 28 | Statut commande (workflow) | Navigateur | Commande : `a_preparer` → `preparee` → `livree` | ✅ Transitions correctes | ⬜ | | |
| 29 | Intégrité BDD (commande sans lignes) | SQL | SELECT commandes sans lignes_commande | ❌ Aucune ligne | ⬜ | | |
| 30 | Intégrité BDD (total = somme) | SQL | Vérifier `total_commande = SUM(lignes.prix)` | ✅ Cohérent | ⬜ | | |

---

## Procédures curl (pour copy-paste)

### Test 6 — CSRF absent
```bash
# Obtenir un PHPSESSID valide d'abord (navigateur ou curl GET)
curl -c cookies.txt http://localhost:8080/login
curl -b cookies.txt -c cookies.txt -X POST \
  -d "identifiant=admin&mot_de_passe=wacdo2026" \
  http://localhost:8080/login

# Puis POST sans _csrf
curl -b cookies.txt -w "\nHTTP Status: %{http_code}\n" -X POST \
  http://localhost:8080/produits/1/desactiver
# Attendu : 403
```

### Test 13 — API sans clé
```bash
curl -w "\nHTTP Status: %{http_code}\n" \
  http://localhost:8080/api/catalogue
# Attendu : 401
```

### Test 15 — API avec clé valide
```bash
API_KEY=$(grep API_KEY .env | cut -d= -f2)
curl -w "\nHTTP Status: %{http_code}\n" \
  -H "X-API-Key: $API_KEY" \
  http://localhost:8080/api/catalogue | jq .
# Attendu : 200 OK + JSON
```

### Test 16 — API POST commande
```bash
API_KEY=$(grep API_KEY .env | cut -d= -f2)
curl -w "\nHTTP Status: %{http_code}\n" \
  -X POST http://localhost:8080/api/commandes \
  -H "X-API-Key: $API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "numero_retrait": "TEST001",
    "lignes": [
      {"type": "produit", "id_produit": 1, "quantite": 2}
    ]
  }' | jq .
# Attendu : 201 Created
```

---

## Tests SQL (post-scénarios critiques)

```sql
-- Aucune commande partielle
SELECT id_commande FROM commandes c
WHERE NOT EXISTS (
  SELECT 1 FROM lignes_commande lc WHERE lc.id_commande = c.id_commande
);
-- Résultat attendu : 0 ligne

-- Total cohérent
SELECT 
  c.id_commande, 
  c.total,
  SUM(lc.prix_unitaire * lc.quantite) as calculated_total
FROM commandes c
JOIN lignes_commande lc ON lc.id_commande = c.id_commande
GROUP BY c.id_commande, c.total
HAVING c.total != SUM(lc.prix_unitaire * lc.quantite);
-- Résultat attendu : 0 ligne

-- Traces d'actions créées
SELECT COUNT(*) FROM traces_actions WHERE action IN ('LOGIN_SUCCESS', 'LOGIN_FAILED', 'COMMANDE_CREATED');
-- Résultat attendu : > 0
```

---

## Synthèse de validation

| Catégorie | Scénarios | ✅ Validés | ❌ Échoués | Bloquants |
|---|---|---|---|---|
| **Authentification** | 1–4 | | | |
| **Contrôle d'accès** | 5, 24–27 | | | |
| **Protection CSRF** | 6–7 | | | |
| **Uploads** | 8–12 | | | |
| **API** | 13–18 | | | |
| **Injection/XSS** | 19–20 | | | |
| **Intégrité données** | 21–30 | | | |
| **TOTAL** | 30 | | | |

**Mise en production autorisée si :** Tous les scénarios critiques (1–20) sont validés et aucun scénario n'est bloquant.

**Date de validation finale :** _______________

**Validé par :** _______________

---

*Bloc 2 WACDO — Tests conformes au CDC technique section 10*
