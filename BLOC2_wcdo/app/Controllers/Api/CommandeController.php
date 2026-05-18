<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Database;
use App\Exceptions\CommandeValidationException;
use App\Repositories\CommandeRepository;
use App\Repositories\MenuRepository;
use App\Repositories\ProduitRepository;
use App\Repositories\SectionMenuRepository;
use App\Services\CommandeService;
use App\Services\TraceService;

/**
 * Endpoint `POST /api/commandes`.
 *
 * Reçoit une commande depuis le système externe (borne / app client), la
 * valide et la persiste via `CommandeService::creer($input, 'api')` — le même
 * service que celui utilisé par le back-office, ce qui garantit l'unicité des
 * règles métier (CDC §3).
 *
 * Les prix envoyés par le client externe sont **ignorés** : le service les
 * relit depuis la base avant calcul du total.
 *
 * Format attendu du body :
 *   {
 *     "type_service": "sur_place" | "a_emporter",
 *     "lignes": [
 *       { "type": "produit", "id": 12, "quantite": 2 },
 *       { "type": "menu",    "id":  3, "quantite": 1,
 *         "choix": [
 *           { "id_section_menu": 5, "id_produit": 42 },
 *           ...
 *         ]
 *       }
 *     ]
 *   }
 */
final class CommandeController extends ApiBaseController
{
    /**
     * Format strict d'un numéro de retrait : `R-` suivi de 6 chiffres.
     * Garantit qu'on ne laisse pas passer n'importe quelle chaîne dans le
     * paramètre de route avant d'interroger la base.
     */
    private const NUMERO_RETRAIT_PATTERN = '/^R-\d{6}$/';

    public function store(array $args = []): void
    {
        $this->requireApiKey();

        $input = $this->readJsonBody();

        // Le service attend exactement la même structure que pour le back-office :
        // type_service + lignes[]. On laisse le service rejeter les structures
        // invalides plutôt que de dupliquer la validation ici.
        try {
            $idCommande = $this->buildService()->creer($input, 'api');
        } catch (CommandeValidationException $e) {
            $this->jsonValidationErrors($e->getErrors(), 400);
        } catch (\InvalidArgumentException $e) {
            // Erreur de programmation côté appelant : on remonte un 400 lisible
            // sans détail interne.
            $this->jsonError('Requête invalide.', 400);
        } catch (\Throwable $e) {
            // Filet de sécurité : on ne laisse jamais fuiter une exception PDO
            // ou autre dans la réponse client. Le détail reste dans les logs PHP.
            error_log('[API /commandes] ' . $e->getMessage());
            $this->jsonError('Erreur interne.', 500);
        }

        // Re-charger la commande pour répondre avec les valeurs persistées
        // (numéro de retrait généré serveur, total calculé serveur, statut initial).
        $commande = (new CommandeRepository())->findById($idCommande);
        if ($commande === null) {
            // Cas théoriquement impossible : on vient de créer la commande
            error_log('[API /commandes] commande créée introuvable, id=' . $idCommande);
            $this->jsonError('Erreur interne.', 500);
        }

        $this->jsonSuccess([
            'id_commande'    => (int) $commande['id_commande'],
            'numero_retrait' => (string) $commande['numero_retrait'],
            'statut'         => (string) $commande['statut'],
            'total'          => (string) $commande['total'],
            'date_commande'  => (string) $commande['date_commande'],
        ], 201);
    }

    /**
     * `GET /api/commandes/{numero}` — suivi du statut d'une commande.
     *
     * Permet au système externe d'interroger l'état courant d'une commande
     * (transition `a_preparer → preparee → livree`) à partir du numéro de
     * retrait reçu lors du `POST`. Ne renvoie pas les lignes : le client les
     * possède déjà, on lui retourne uniquement ce qui change côté serveur.
     */
    public function show(array $args = []): void
    {
        $this->requireApiKey();

        $numero = (string) ($args['numero'] ?? '');
        if ($numero === '' || preg_match(self::NUMERO_RETRAIT_PATTERN, $numero) !== 1) {
            $this->jsonError('Numéro de retrait invalide. Format attendu : R-XXXXXX.', 400);
        }

        $commande = (new CommandeRepository())->findByNumeroRetrait($numero);
        if ($commande === null) {
            $this->jsonError('Commande introuvable.', 404);
        }

        $this->jsonSuccess([
            'id_commande'                => (int) $commande['id_commande'],
            'numero_retrait'             => (string) $commande['numero_retrait'],
            'statut'                     => (string) $commande['statut'],
            'type_service'               => (string) $commande['type_service'],
            'source'                     => (string) $commande['source'],
            'total'                      => (string) $commande['total'],
            'date_commande'              => (string) $commande['date_commande'],
            'date_heure_retrait_prevue'  => $commande['date_heure_retrait_prevue'] !== null
                ? (string) $commande['date_heure_retrait_prevue']
                : null,
        ]);
    }

    /**
     * Construit une instance prête à l'emploi de `CommandeService`.
     * Dupliqué volontairement avec le contrôleur back-office : chaque contrôleur
     * reste autonome (KISS, pas de container DI nécessaire pour ce projet).
     */
    private function buildService(): CommandeService
    {
        return new CommandeService(
            new CommandeRepository(),
            new ProduitRepository(),
            new MenuRepository(),
            new SectionMenuRepository(),
            new TraceService(Database::connection()),
        );
    }
}
