<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Validator;
use App\Repositories\MenuRepository;
use App\Repositories\ProduitRepository;
use App\Repositories\SectionMenuRepository;
use App\Services\TraceService;

/**
 * Gestion des sections d'un menu — page de composition.
 * Une section représente une "partie" du menu (ex : "Votre plat", "Votre boisson").
 * Réservé au rôle Administration.
 */
final class SectionMenuController extends BaseController
{
    /**
     * Affiche la page de gestion d'un menu : ses sections et leurs options,
     * avec les formulaires d'ajout. C'est la page la plus dense du back-office catalogue.
     */
    public function index(array $args = []): void
    {
        $this->requireRole(['Administration']);

        $idMenu   = (int) ($args['id'] ?? 0);
        $menuRepo = new MenuRepository();
        $menu     = $menuRepo->findById($idMenu);

        if ($menu === null) {
            $this->abort(404);
            return;
        }

        $sectionRepo = new SectionMenuRepository();
        $sections    = $sectionRepo->findByMenuId($idMenu);

        // Liste des produits actifs proposables comme options
        $produitRepo     = new ProduitRepository();
        $produitsActifs  = $produitRepo->findAllActive();

        $this->view('menus/sections', [
            'title'          => "Composition du menu — {$menu['nom']}",
            'flash'          => $this->getFlash(),
            'menu'           => $menu,
            'sections'       => $sections,
            'produitsActifs' => $produitsActifs,
            'csrfToken'      => $this->csrfToken(),
        ]);
    }

    /**
     * Ajoute une nouvelle section au menu.
     * Validation : nom requis, quantite_min >= 0, quantite_max >= 1, max >= min.
     */
    public function store(array $args = []): void
    {
        $this->requireRole(['Administration']);
        $this->requireCsrf();

        $idMenu   = (int) ($args['id'] ?? 0);
        $menuRepo = new MenuRepository();
        $menu     = $menuRepo->findById($idMenu);

        if ($menu === null) {
            $this->abort(404);
            return;
        }

        $nom         = trim((string) ($_POST['nom']          ?? ''));
        $obligatoire = isset($_POST['obligatoire']);
        $qMinRaw     = trim((string) ($_POST['quantite_min'] ?? '1'));
        $qMaxRaw     = trim((string) ($_POST['quantite_max'] ?? '1'));

        $v = new Validator();
        $v->required('nom', $nom, 'Nom')
          ->maxLength('nom', $nom, 100, 'Nom')
          ->required('quantite_min', $qMinRaw, 'Quantité min')
          ->intBetween('quantite_min', $qMinRaw, 0, 99, 'Quantité min')
          ->required('quantite_max', $qMaxRaw, 'Quantité max')
          ->intBetween('quantite_max', $qMaxRaw, 1, 99, 'Quantité max');

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect("/menus/{$idMenu}/sections");
            return;
        }

        $qMin = (int) $qMinRaw;
        $qMax = (int) $qMaxRaw;

        // Cohérence : quantité max doit être >= quantité min (CHECK BDD)
        if ($qMax < $qMin) {
            $this->flash('error', 'La quantité max doit être supérieure ou égale à la quantité min.');
            $this->redirect("/menus/{$idMenu}/sections");
            return;
        }

        $sectionRepo = new SectionMenuRepository();
        $idSection   = $sectionRepo->create($idMenu, $nom, $obligatoire, $qMin, $qMax);

        (new TraceService())->log(
            'creation',
            'sections_menu',
            $idSection,
            "menu={$idMenu};nom={$nom}"
        );

        $this->flash('success', "Section « {$nom} » ajoutée au menu.");
        $this->redirect("/menus/{$idMenu}/sections");
    }
}
