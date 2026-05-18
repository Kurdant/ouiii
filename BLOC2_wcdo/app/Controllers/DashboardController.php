<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;

/**
 * Page d'accueil après connexion.
 *
 * Chaque rôle voit la page la plus utile à son métier :
 *  - **Préparation** est redirigé vers `/commandes` (sa liste filtrée par
 *    `a_preparer` triée par heure de retrait croissante — CDC §3).
 *  - **Accueil** est redirigé vers `/commandes` (vue globale + actions de
 *    saisie/livraison accessibles via la sidebar).
 *  - **Administration** voit un tableau de bord avec des compteurs métier
 *    (commandes du jour, à préparer, prêtes à livrer) pour piloter
 *    l'exploitation.
 */
final class DashboardController extends BaseController
{
    public function index(array $args = []): void
    {
        $this->requireAuth();

        $role = $_SESSION['user']['role'] ?? '';

        // Les rôles opérationnels n'ont pas besoin d'un tableau de bord :
        // leur point d'entrée naturel est la liste des commandes.
        if ($role === 'Preparation' || $role === 'Accueil') {
            $this->redirect('/commandes');
        }

        // Administration : tableau de bord synthétique.
        $compteurs = $this->compteursAdmin();

        $this->view('dashboard/index', [
            'title'      => 'Tableau de bord',
            'compteurs'  => $compteurs,
            'flash'      => $this->getFlash(),
        ]);
    }

    /**
     * Calcule les compteurs affichés sur le tableau de bord Administration.
     * Une seule requête agrégée pour éviter de multiplier les allers-retours
     * en base.
     *
     * @return array{a_preparer: int, preparees: int, livrees_jour: int, total_jour: int}
     */
    private function compteursAdmin(): array
    {
        $pdo = Database::connection();
        $statement = $pdo->query(
            "SELECT
                COUNT(*) FILTER (WHERE statut = 'a_preparer')                                    AS a_preparer,
                COUNT(*) FILTER (WHERE statut = 'preparee')                                      AS preparees,
                COUNT(*) FILTER (WHERE statut = 'livree' AND DATE(date_commande) = CURRENT_DATE) AS livrees_jour,
                COUNT(*) FILTER (WHERE DATE(date_commande) = CURRENT_DATE)                      AS total_jour
             FROM commandes"
        );
        $row = $statement->fetch();

        return [
            'a_preparer'   => (int) ($row['a_preparer']   ?? 0),
            'preparees'    => (int) ($row['preparees']    ?? 0),
            'livrees_jour' => (int) ($row['livrees_jour'] ?? 0),
            'total_jour'   => (int) ($row['total_jour']   ?? 0),
        ];
    }
}
