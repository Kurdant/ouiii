<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Validator;
use App\Repositories\OptionMenuRepository;
use App\Repositories\ProduitRepository;
use App\Repositories\SectionMenuRepository;
use App\Services\TraceService;

/**
 * Gestion des options d'une section de menu.
 * Une option lie un produit actif à une section, avec un supplément de prix éventuel.
 * Réservé au rôle Administration.
 */
final class OptionMenuController extends BaseController
{
    /**
     * Ajoute une option (produit + supplément) à une section.
     * Validation :
     *  - produit existe et est actif
     *  - supplement_prix >= 0
     *  - le couple (section, produit) n'existe pas déjà (contrainte UNIQUE)
     */
    public function store(array $args = []): void
    {
        $this->requireRole(['Administration']);
        $this->requireCsrf();

        $idSection   = (int) ($args['id'] ?? 0);
        $sectionRepo = new SectionMenuRepository();
        $section     = $sectionRepo->findById($idSection);

        if ($section === null) {
            $this->abort(404);
            return;
        }

        $idMenu        = (int) $section['id_menu'];
        $idProduit     = (int) ($_POST['id_produit']       ?? 0);
        $supplementRaw = trim((string) ($_POST['supplement_prix'] ?? '0'));

        $v = new Validator();
        $v->required('supplement_prix', $supplementRaw, 'Supplément')
          ->nonNegativeNumber('supplement_prix', $supplementRaw, 'Supplément');

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect("/menus/{$idMenu}/sections");
            return;
        }

        // Vérifier que le produit est actif
        $produitRepo    = new ProduitRepository();
        $produitsActifs = $produitRepo->findAllActive();

        if (!in_array($idProduit, array_map('intval', array_column($produitsActifs, 'id_produit')), true)) {
            $this->flash('error', 'Produit invalide ou inactif.');
            $this->redirect("/menus/{$idMenu}/sections");
            return;
        }

        // Tentative d'insertion — la contrainte UNIQUE (section, produit) protège des doublons.
        $optionRepo = new OptionMenuRepository();

        try {
            $idOption = $optionRepo->create($idSection, $idProduit, (float) $supplementRaw);
        } catch (\PDOException $e) {
            // Code SQLSTATE 23505 = violation d'unicité (PostgreSQL)
            if ($e->getCode() === '23505') {
                $this->flash('error', 'Ce produit est déjà proposé dans cette section.');
                $this->redirect("/menus/{$idMenu}/sections");
                return;
            }
            throw $e;
        }

        (new TraceService())->log(
            'creation',
            'options_menu',
            $idOption,
            "section={$idSection};produit={$idProduit}"
        );

        $this->flash('success', 'Option ajoutée à la section.');
        $this->redirect("/menus/{$idMenu}/sections");
    }

    /**
     * Désactive une option (soft delete — préservation historique commandes).
     */
    public function desactiver(array $args = []): void
    {
        $this->requireRole(['Administration']);
        $this->requireCsrf();

        $idOption   = (int) ($args['id'] ?? 0);
        $optionRepo = new OptionMenuRepository();
        $option     = $optionRepo->findById($idOption);

        if ($option === null) {
            $this->abort(404);
            return;
        }

        // Récupérer le menu pour rediriger correctement
        $sectionRepo = new SectionMenuRepository();
        $section     = $sectionRepo->findById((int) $option['id_section_menu']);
        $idMenu      = $section !== null ? (int) $section['id_menu'] : 0;

        $optionRepo->desactiver($idOption);

        (new TraceService())->log(
            'desactivation',
            'options_menu',
            $idOption,
            "section={$option['id_section_menu']};produit={$option['id_produit']}"
        );

        $this->flash('success', 'Option retirée de la section.');
        $this->redirect($idMenu > 0 ? "/menus/{$idMenu}/sections" : '/menus');
    }
}
