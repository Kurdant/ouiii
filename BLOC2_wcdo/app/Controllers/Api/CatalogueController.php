<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Repositories\CategorieRepository;
use App\Repositories\MenuRepository;
use App\Repositories\ProduitRepository;
use App\Repositories\SectionMenuRepository;

/**
 * Endpoint `GET /api/catalogue`.
 *
 * Retourne au système de commande externe (borne / app client) le catalogue
 * commandable : catégories actives, produits actifs+disponibles regroupés
 * par catégorie, et menus actifs+disponibles avec leur composition complète
 * (sections + options).
 *
 * Aucune donnée interne (utilisateurs, sessions, traces, prix archivés) n'est
 * exposée — uniquement ce qui est utile à la prise de commande.
 */
final class CatalogueController extends ApiBaseController
{
    public function index(array $args = []): void
    {
        $this->requireApiKey();

        $categoriesRepo = new CategorieRepository();
        $produitsRepo   = new ProduitRepository();
        $menusRepo      = new MenuRepository();
        $sectionsRepo   = new SectionMenuRepository();

        // -- Catégories --------------------------------------------------------
        $categories = array_map(
            static fn(array $cat): array => [
                'id_categorie' => (int) $cat['id_categorie'],
                'nom'          => (string) $cat['nom'],
            ],
            $categoriesRepo->findAllActive(),
        );

        // -- Produits regroupés par catégorie ----------------------------------
        $produits = [];
        foreach ($produitsRepo->findAllAvailableActive() as $produit) {
            $produits[] = [
                'id_produit'   => (int) $produit['id_produit'],
                'id_categorie' => (int) $produit['id_categorie'],
                'nom'          => (string) $produit['nom'],
                'description'  => (string) ($produit['description'] ?? ''),
                'prix'         => (string) $produit['prix'],
                'image'        => $produit['image'] !== null ? (string) $produit['image'] : null,
            ];
        }

        // -- Menus avec leur composition (sections + options) ------------------
        $menus = [];
        foreach ($menusRepo->findAllAvailableActive() as $menu) {
            $idMenu   = (int) $menu['id_menu'];
            $sections = [];
            foreach ($sectionsRepo->findByMenuId($idMenu) as $section) {
                $options = [];
                foreach ($section['options'] as $option) {
                    if (!$option['disponible']) {
                        // On n'expose pas une option indisponible au client externe
                        continue;
                    }
                    $options[] = [
                        'id_produit'      => (int) $option['id_produit'],
                        'libelle'         => (string) $option['produit_nom'],
                        'supplement_prix' => (string) $option['supplement_prix'],
                    ];
                }

                $sections[] = [
                    'id_section_menu' => (int) $section['id_section_menu'],
                    'nom'             => (string) $section['nom'],
                    'obligatoire'     => (bool) $section['obligatoire'],
                    'quantite_min'    => (int) $section['quantite_min'],
                    'quantite_max'    => (int) $section['quantite_max'],
                    'options'         => $options,
                ];
            }

            $menus[] = [
                'id_menu'     => $idMenu,
                'nom'         => (string) $menu['nom'],
                'description' => (string) ($menu['description'] ?? ''),
                'prix'        => (string) $menu['prix'],
                'image'       => $menu['image'] !== null ? (string) $menu['image'] : null,
                'sections'    => $sections,
            ];
        }

        $this->jsonSuccess([
            'categories' => $categories,
            'produits'   => $produits,
            'menus'      => $menus,
        ]);
    }
}
