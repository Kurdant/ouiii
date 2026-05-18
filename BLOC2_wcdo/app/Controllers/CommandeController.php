<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;
use App\Exceptions\CommandeValidationException;
use App\Repositories\CommandeRepository;
use App\Repositories\MenuRepository;
use App\Repositories\ProduitRepository;
use App\Repositories\SectionMenuRepository;
use App\Services\CommandeService;
use App\Services\TraceService;

/**
 * Gestion des commandes côté back-office.
 *
 * Rôles autorisés (CDC §1) :
 *   - Accueil       : saisie manuelle, livraison par numéro de retrait, consultation
 *   - Preparation   : consultation des commandes `à préparer`, déclaration "préparée"
 *   - Administration: toutes actions
 *
 * Toutes les opérations métier (création, transitions, validations) sont
 * déléguées au `CommandeService` (sprint 7) afin que les règles soient
 * strictement identiques avec l'API REST (sprint 9).
 */
final class CommandeController extends BaseController
{
    private const ROLES_TOUS         = ['Administration', 'Accueil', 'Preparation'];
    private const ROLES_ACCUEIL_ADM  = ['Administration', 'Accueil'];
    private const ROLES_PREP_ADM     = ['Administration', 'Preparation'];

    // -------------------------------------------------------------------------
    // LISTE
    // -------------------------------------------------------------------------

    /**
     * Liste des commandes adaptée au rôle :
     *   - Preparation : uniquement `a_preparer`, triées par heure de retrait croissante
     *   - Accueil / Administration : toutes, filtrables par ?statut=…
     */
    public function index(array $args = []): void
    {
        $this->requireRole(self::ROLES_TOUS);

        $repo   = new CommandeRepository();
        $user   = $this->currentUser();
        $isPrep = ($user['role'] ?? null) === 'Preparation';

        if ($isPrep) {
            $commandes     = $repo->findAPreparer();
            $statutCourant = 'a_preparer';
        } else {
            $statutDemande    = $_GET['statut'] ?? null;
            $statutsAutorises = ['a_preparer', 'preparee', 'livree'];
            $statutCourant    = in_array($statutDemande, $statutsAutorises, true)
                ? (string) $statutDemande
                : null;
            $commandes        = $repo->findAll($statutCourant);
        }

        $this->view('commandes/index', [
            'title'         => 'Commandes',
            'flash'         => $this->getFlash(),
            'commandes'     => $commandes,
            'isPreparation' => $isPrep,
            'statutCourant' => $statutCourant,
            'role'          => $user['role'] ?? null,
            'csrfToken'     => $this->csrfToken(),
        ]);
    }

    // -------------------------------------------------------------------------
    // DÉTAIL
    // -------------------------------------------------------------------------

    public function show(array $args = []): void
    {
        $this->requireRole(self::ROLES_TOUS);

        $id       = (int) ($args['id'] ?? 0);
        $repo     = new CommandeRepository();
        $commande = $repo->findByIdWithLignes($id);

        if ($commande === null) {
            $this->abort(404);
        }

        $user = $this->currentUser();
        $this->view('commandes/show', [
            'title'     => 'Commande ' . $commande['numero_retrait'],
            'flash'     => $this->getFlash(),
            'commande'  => $commande,
            'role'      => $user['role'] ?? null,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // -------------------------------------------------------------------------
    // SAISIE MANUELLE — formulaire + traitement
    // -------------------------------------------------------------------------

    /**
     * Formulaire de saisie manuelle d'une commande.
     * Accessible à Accueil et Administration.
     */
    public function create(array $args = []): void
    {
        $this->requireRole(self::ROLES_ACCUEIL_ADM);

        $produits    = (new ProduitRepository())->findAllAvailableActive();
        $menus       = (new MenuRepository())->findAllAvailableActive();
        $sectionRepo = new SectionMenuRepository();

        // Pour chaque menu, charger sa composition (sections + options) afin que
        // le formulaire puisse afficher les choix obligatoires/facultatifs.
        $menusComposes = [];
        foreach ($menus as $menu) {
            $menu['sections'] = $sectionRepo->findByMenuId((int) $menu['id_menu']);
            $menusComposes[]  = $menu;
        }

        $this->view('commandes/create', [
            'title'     => 'Nouvelle commande',
            'flash'     => $this->getFlash(),
            'produits'  => $produits,
            'menus'     => $menusComposes,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Traitement du formulaire de saisie : transforme `$_POST` en payload
     * pour `CommandeService::creer()` et applique le pattern PRG.
     */
    public function store(array $args = []): void
    {
        $this->requireRole(self::ROLES_ACCUEIL_ADM);
        $this->requireCsrf();

        $input = $this->buildInputFromPost($_POST);

        if ($input['lignes'] === []) {
            $this->flash('error', 'La commande doit comporter au moins un produit ou un menu.');
            $this->redirect('/commandes/creer');
        }

        try {
            $service    = $this->buildService();
            $idCommande = $service->creer($input, 'back_office');
        } catch (CommandeValidationException $e) {
            $messages = implode(' • ', $e->getErrors());
            $this->flash('error', 'Commande invalide : ' . $messages);
            $this->redirect('/commandes/creer');
        }

        $this->flash('success', 'Commande créée avec succès.');
        $this->redirect('/commandes/' . $idCommande);
    }

    // -------------------------------------------------------------------------
    // TRANSITIONS DE STATUT
    // -------------------------------------------------------------------------

    /**
     * Déclare une commande comme préparée.
     * Accessible à Preparation et Administration.
     */
    public function marquerPreparee(array $args = []): void
    {
        $this->requireRole(self::ROLES_PREP_ADM);
        $this->requireCsrf();

        $id = (int) ($args['id'] ?? 0);

        try {
            $this->buildService()->marquerPreparee($id);
        } catch (CommandeValidationException $e) {
            $this->flash('error', $e->firstError() ?? 'Transition impossible.');
            $this->redirect('/commandes');
        }

        $this->flash('success', 'Commande déclarée préparée.');
        $this->redirect('/commandes');
    }

    /**
     * Variante par ID (consultation détail) : marque livrée la commande dont
     * l'ID est dans l'URL. On résout le numéro de retrait avant d'appeler le
     * service afin de conserver l'unique chemin métier `marquerLivreeParNumeroRetrait`.
     * Accessible à Accueil et Administration.
     */
    public function marquerLivree(array $args = []): void
    {
        $this->requireRole(self::ROLES_ACCUEIL_ADM);
        $this->requireCsrf();

        $id       = (int) ($args['id'] ?? 0);
        $repo     = new CommandeRepository();
        $commande = $repo->findById($id);

        if ($commande === null) {
            $this->abort(404);
        }

        try {
            $this->buildService()->marquerLivreeParNumeroRetrait((string) $commande['numero_retrait']);
        } catch (CommandeValidationException $e) {
            $this->flash('error', $e->firstError() ?? 'Transition impossible.');
            $this->redirect('/commandes/' . $id);
        }

        $this->flash('success', 'Commande déclarée livrée.');
        $this->redirect('/commandes/' . $id);
    }

    // -------------------------------------------------------------------------
    // LIVRAISON PAR NUMÉRO DE RETRAIT (rôle Accueil au comptoir)
    // -------------------------------------------------------------------------

    /** Affiche le formulaire de saisie du numéro de retrait pour livrer. */
    public function livraisonForm(array $args = []): void
    {
        $this->requireRole(self::ROLES_ACCUEIL_ADM);

        $this->view('commandes/livraison', [
            'title'     => 'Déclarer une livraison',
            'flash'     => $this->getFlash(),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /** Traite la livraison à partir du numéro de retrait saisi au comptoir. */
    public function livraisonParNumero(array $args = []): void
    {
        $this->requireRole(self::ROLES_ACCUEIL_ADM);
        $this->requireCsrf();

        $numero = trim((string) ($_POST['numero_retrait'] ?? ''));

        if ($numero === '') {
            $this->flash('error', 'Le numéro de retrait est obligatoire.');
            $this->redirect('/commandes/livraison');
        }

        try {
            $commande = $this->buildService()->marquerLivreeParNumeroRetrait($numero);
        } catch (CommandeValidationException $e) {
            $this->flash('error', $e->firstError() ?? 'Livraison impossible.');
            $this->redirect('/commandes/livraison');
        }

        $this->flash('success', 'Commande ' . $commande['numero_retrait'] . ' livrée.');
        $this->redirect('/commandes/' . $commande['id_commande']);
    }

    // -------------------------------------------------------------------------
    // HELPERS PRIVÉS
    // -------------------------------------------------------------------------

    /**
     * Construit une instance prête à l'emploi de `CommandeService` en
     * injectant ses dépendances. Centralisé ici pour éviter la duplication.
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

    /**
     * Transforme la structure `$_POST` (issue du formulaire de saisie) en
     * payload consommable par `CommandeService::creer()`.
     *
     * Convention du formulaire :
     *   - `type_service`                              : 'sur_place' | 'a_emporter'
     *   - `produits[<id_produit>]`                    : entier (quantité, ignoré si ≤ 0)
     *   - `menus[<id_menu>][quantite]`                : entier (ignoré si ≤ 0)
     *   - `menus[<id_menu>][sections][<id_section>]`  : id_produit (string)
     *                                                   ou tableau (sélection multiple)
     *
     * @param array<string, mixed> $post
     *
     * @return array{type_service: string, lignes: list<array<string, mixed>>}
     */
    private function buildInputFromPost(array $post): array
    {
        $lignes = [];

        // -- Lignes produits ---------------------------------------------------
        $produitsPost = is_array($post['produits'] ?? null) ? $post['produits'] : [];
        foreach ($produitsPost as $idProduit => $quantite) {
            $idProduit = (int) $idProduit;
            $quantite  = (int) $quantite;
            if ($idProduit > 0 && $quantite > 0) {
                $lignes[] = [
                    'type'     => 'produit',
                    'id'       => $idProduit,
                    'quantite' => $quantite,
                ];
            }
        }

        // -- Lignes menus ------------------------------------------------------
        $menusPost = is_array($post['menus'] ?? null) ? $post['menus'] : [];
        foreach ($menusPost as $idMenu => $menuData) {
            $idMenu   = (int) $idMenu;
            $quantite = (int) (is_array($menuData) ? ($menuData['quantite'] ?? 0) : 0);
            if ($idMenu < 1 || $quantite < 1) {
                continue;
            }

            $choix    = [];
            $sections = is_array($menuData['sections'] ?? null) ? $menuData['sections'] : [];
            foreach ($sections as $idSection => $valeur) {
                $idSection = (int) $idSection;
                if ($idSection < 1) {
                    continue;
                }
                // Une section retourne soit un id unique (string), soit un
                // tableau d'ids (sélection multiple selon `quantite_max`).
                $ids = is_array($valeur) ? $valeur : [$valeur];
                foreach ($ids as $idProduitOption) {
                    $idProduitOption = (int) $idProduitOption;
                    if ($idProduitOption < 1) {
                        continue;
                    }
                    $choix[] = [
                        'id_section_menu' => $idSection,
                        'id_produit'      => $idProduitOption,
                    ];
                }
            }

            $lignes[] = [
                'type'     => 'menu',
                'id'       => $idMenu,
                'quantite' => $quantite,
                'choix'    => $choix,
            ];
        }

        // -- Lignes menus (nouvelle convention : instances indexées) ----------
        // Émis par commande-create.js sous forme menu_instances[N][id_menu]
        // et menu_instances[N][sections][id_section]
        $menuInstances = is_array($post['menu_instances'] ?? null) ? $post['menu_instances'] : [];
        foreach ($menuInstances as $instanceData) {
            if (!is_array($instanceData)) {
                continue;
            }
            $idMenu = (int) ($instanceData['id_menu'] ?? 0);
            if ($idMenu < 1) {
                continue;
            }

            $choix    = [];
            $sections = is_array($instanceData['sections'] ?? null) ? $instanceData['sections'] : [];
            foreach ($sections as $idSection => $valeur) {
                $idSection = (int) $idSection;
                if ($idSection < 1) {
                    continue;
                }
                $ids = is_array($valeur) ? $valeur : [$valeur];
                foreach ($ids as $idProduitOption) {
                    $idProduitOption = (int) $idProduitOption;
                    if ($idProduitOption < 1) {
                        continue;
                    }
                    $choix[] = [
                        'id_section_menu' => $idSection,
                        'id_produit'      => $idProduitOption,
                    ];
                }
            }

            $lignes[] = [
                'type'     => 'menu',
                'id'       => $idMenu,
                'quantite' => 1,
                'choix'    => $choix,
            ];
        }

        return [
            'type_service' => (string) ($post['type_service'] ?? ''),
            'lignes'       => $lignes,
        ];
    }
}
