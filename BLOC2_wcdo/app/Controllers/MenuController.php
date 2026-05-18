<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Validator;
use App\Repositories\MenuRepository;
use App\Services\TraceService;
use App\Services\UploadService;

/**
 * Gestion des menus composés — réservé au rôle Administration.
 * La composition (sections + options) est gérée par SectionMenuController
 * et OptionMenuController via /menus/{id}/sections.
 */
final class MenuController extends BaseController
{
    // Liste tous les menus (actifs et inactifs)
    public function index(array $args = []): void
    {
        $this->requireRole(['Administration']);

        $repo = new MenuRepository();
        $this->view('menus/index', [
            'title'     => 'Menus',
            'flash'     => $this->getFlash(),
            'menus'     => $repo->findAll(),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // Formulaire de création
    public function create(array $args = []): void
    {
        $this->requireRole(['Administration']);

        $this->view('menus/create', [
            'title'     => 'Créer un menu',
            'flash'     => $this->getFlash(),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // Soumission du formulaire de création
    public function store(array $args = []): void
    {
        $this->requireRole(['Administration']);
        $this->requireCsrf();

        $nom         = trim((string) ($_POST['nom']         ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $prixRaw     = trim((string) ($_POST['prix']        ?? ''));

        $v = new Validator();
        $v->required('nom', $nom, 'Nom')
          ->maxLength('nom', $nom, 150, 'Nom')
          ->required('description', $description, 'Description')
          ->required('prix', $prixRaw, 'Prix')
          ->positiveNumber('prix', $prixRaw, 'Prix');

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/menus/creer');
            return;
        }

        // Upload obligatoire à la création
        $imageFile = $_FILES['image'] ?? [];

        if (!isset($imageFile['error']) || $imageFile['error'] === UPLOAD_ERR_NO_FILE) {
            $this->flash('error', 'Une image est obligatoire.');
            $this->redirect('/menus/creer');
            return;
        }

        try {
            $imagePath = (new UploadService())->stocker($imageFile, 'menus');
        } catch (\RuntimeException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/menus/creer');
            return;
        }

        $repo = new MenuRepository();
        $id   = $repo->create([
            'nom'         => $nom,
            'description' => $description,
            'prix'        => $prixRaw,
            'image'       => $imagePath,
        ]);

        (new TraceService())->log('creation', 'menus', $id, "nom={$nom}");

        $this->flash('success', "Menu « {$nom} » créé. Ajoutez maintenant ses sections.");
        $this->redirect("/menus/{$id}/sections");
    }

    // Formulaire d'édition
    public function edit(array $args = []): void
    {
        $this->requireRole(['Administration']);

        $id   = (int) ($args['id'] ?? 0);
        $repo = new MenuRepository();
        $menu = $repo->findById($id);

        if ($menu === null) {
            $this->abort(404);
            return;
        }

        $this->view('menus/edit', [
            'title'     => 'Modifier un menu',
            'flash'     => $this->getFlash(),
            'menu'      => $menu,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // Soumission du formulaire d'édition
    public function update(array $args = []): void
    {
        $this->requireRole(['Administration']);
        $this->requireCsrf();

        $id   = (int) ($args['id'] ?? 0);
        $repo = new MenuRepository();
        $menu = $repo->findById($id);

        if ($menu === null) {
            $this->abort(404);
            return;
        }

        $nom         = trim((string) ($_POST['nom']         ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $prixRaw     = trim((string) ($_POST['prix']        ?? ''));
        $disponible  = isset($_POST['disponible']);

        $v = new Validator();
        $v->required('nom', $nom, 'Nom')
          ->maxLength('nom', $nom, 150, 'Nom')
          ->required('description', $description, 'Description')
          ->required('prix', $prixRaw, 'Prix')
          ->positiveNumber('prix', $prixRaw, 'Prix');

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect("/menus/{$id}/editer");
            return;
        }

        $data = [
            'nom'         => $nom,
            'description' => $description,
            'prix'        => $prixRaw,
            'disponible'  => $disponible,
        ];

        // Image optionnelle à l'édition
        $imageFile = $_FILES['image'] ?? [];

        if (isset($imageFile['error']) && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $newImagePath = (new UploadService())->stocker($imageFile, 'menus');
            } catch (\RuntimeException $e) {
                $this->flash('error', $e->getMessage());
                $this->redirect("/menus/{$id}/editer");
                return;
            }

            if (!empty($menu['image'])) {
                (new UploadService())->supprimer($menu['image']);
            }

            $data['image'] = $newImagePath;
        }

        $repo->update($id, $data);

        (new TraceService())->log('modification', 'menus', $id, "nom={$nom}");

        $this->flash('success', "Menu « {$nom} » mis à jour.");
        $this->redirect('/menus');
    }

    // Désactivation (soft delete — préservation historique commandes)
    public function desactiver(array $args = []): void
    {
        $this->requireRole(['Administration']);
        $this->requireCsrf();

        $id   = (int) ($args['id'] ?? 0);
        $repo = new MenuRepository();
        $menu = $repo->findById($id);

        if ($menu === null) {
            $this->abort(404);
            return;
        }

        $repo->desactiver($id);

        (new TraceService())->log('desactivation', 'menus', $id, "nom={$menu['nom']}");

        $this->flash('success', "Menu « {$menu['nom']} » désactivé.");
        $this->redirect('/menus');
    }
}
