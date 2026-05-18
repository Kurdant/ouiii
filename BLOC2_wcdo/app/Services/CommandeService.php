<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CommandeValidationException;
use App\Repositories\CommandeRepository;
use App\Repositories\MenuRepository;
use App\Repositories\ProduitRepository;
use App\Repositories\SectionMenuRepository;

/**
 * Noyau métier des commandes — source unique de vérité des règles
 * de création, de calcul et de transition de statut.
 *
 * Conformément au CDC §3 (saisie manuelle et API suivent les mêmes règles),
 * ce service est appelé aussi bien par le `CommandeController` du back-office
 * (sprint 8) que par `Api\CommandeApiController` (sprint 9).
 *
 * Règles centrales :
 *  - Les prix et libellés sont **toujours** lus depuis la base (jamais depuis l'entrée client)
 *    afin d'empêcher la manipulation côté appelant.
 *  - Un produit / menu doit être actif ET disponible pour être commandable.
 *  - Pour un menu : chaque section obligatoire doit recevoir entre `quantite_min`
 *    et `quantite_max` choix ; chaque choix doit faire référence à une option
 *    active de la section, dont le produit support est lui-même actif et disponible.
 *  - Transitions de statut figées : `a_preparer` → `preparee` → `livree`.
 *  - Numéro de retrait généré côté back-office : `R-XXXXXX` (6 chiffres aléatoires).
 *
 * La couche atomicité (transaction commande + lignes + choix) est déléguée au
 * `CommandeRepository::create()`.
 */
final class CommandeService
{
    public function __construct(
        private CommandeRepository    $commandeRepo,
        private ProduitRepository     $produitRepo,
        private MenuRepository        $menuRepo,
        private SectionMenuRepository $sectionRepo,
        private TraceService          $traceService,
    ) {
    }

    // =========================================================================
    // CRÉATION
    // =========================================================================

    /**
     * Crée une commande après validation complète des règles métier.
     *
     * Structure attendue de $input :
     *   [
     *     'type_service' => 'sur_place' | 'a_emporter',
     *     'lignes' => [
     *       [
     *         'type'     => 'produit',
     *         'id'       => int,        // id_produit
     *         'quantite' => int,
     *       ],
     *       [
     *         'type'     => 'menu',
     *         'id'       => int,        // id_menu
     *         'quantite' => int,
     *         'choix'    => [           // un tableau par section
     *           ['id_section_menu' => int, 'id_produit' => int],
     *           ...
     *         ],
     *       ],
     *       ...
     *     ],
     *   ]
     *
     * @param array<string, mixed> $input
     * @param string               $source 'api' ou 'back_office'
     *
     * @throws CommandeValidationException si une règle métier n'est pas respectée
     *
     * @return int ID de la commande créée
     */
    public function creer(array $input, string $source): int
    {
        $errors = [];

        // -- Validation du cadre général ---------------------------------------
        if (!in_array($source, ['api', 'back_office'], true)) {
            // Erreur de programmation, pas d'utilisateur : on lève directement
            throw new \InvalidArgumentException('Source invalide.');
        }

        $typeService = (string) ($input['type_service'] ?? '');
        if (!in_array($typeService, ['sur_place', 'a_emporter'], true)) {
            $errors[] = 'Type de service invalide (attendu : sur_place ou a_emporter).';
        }

        $lignesInput = $input['lignes'] ?? null;
        if (!is_array($lignesInput) || $lignesInput === []) {
            $errors[] = 'La commande doit comporter au moins une ligne.';
            throw new CommandeValidationException($errors);
        }

        // -- Validation ligne par ligne + construction des lignes BDD ----------
        $lignesPersist = [];
        $total         = 0.0;

        foreach ($lignesInput as $indexZeroBased => $ligneInput) {
            $numeroLigne = $indexZeroBased + 1;

            $type     = (string) ($ligneInput['type'] ?? '');
            $idEntite = (int) ($ligneInput['id'] ?? 0);
            $quantite = (int) ($ligneInput['quantite'] ?? 0);

            if ($quantite < 1) {
                $errors[] = "Ligne {$numeroLigne} : la quantité doit être supérieure à zéro.";
                continue;
            }
            if ($idEntite < 1) {
                $errors[] = "Ligne {$numeroLigne} : identifiant manquant.";
                continue;
            }

            if ($type === 'produit') {
                $lignePersist = $this->validerLigneProduit(
                    $numeroLigne,
                    $idEntite,
                    $quantite,
                    $errors,
                );
            } elseif ($type === 'menu') {
                $choixInput   = is_array($ligneInput['choix'] ?? null) ? $ligneInput['choix'] : [];
                $lignePersist = $this->validerLigneMenu(
                    $numeroLigne,
                    $idEntite,
                    $quantite,
                    $choixInput,
                    $errors,
                );
            } else {
                $errors[] = "Ligne {$numeroLigne} : type invalide (attendu : produit ou menu).";
                continue;
            }

            if ($lignePersist !== null) {
                $lignesPersist[] = $lignePersist;
                // `sous_total` est une string (number_format) → cast explicite pour
                // éviter le type juggling lors de l'accumulation du total.
                $total          += (float) $lignePersist['sous_total'];
            }
        }

        if ($errors !== []) {
            throw new CommandeValidationException($errors);
        }

        // -- Construction de la commande et persistance atomique ---------------
        $commandeRow = [
            'numero_retrait'            => $this->genererNumeroRetrait(),
            'source'                    => $source,
            'type_service'              => $typeService,
            'total'                     => number_format($total, 2, '.', ''),
            'id_utilisateur_auteur'     => $_SESSION['user']['id'] ?? null,
            'date_heure_retrait_prevue' => null,
        ];

        $idCommande = $this->commandeRepo->create($commandeRow, $lignesPersist);

        $this->traceService->log(
            'creation',
            'commandes',
            $idCommande,
            "source={$source};total={$commandeRow['total']};numero={$commandeRow['numero_retrait']}",
        );

        return $idCommande;
    }

    // =========================================================================
    // TRANSITIONS DE STATUT
    // =========================================================================

    /**
     * Passe une commande au statut `preparee`.
     *
     * @throws CommandeValidationException si la commande n'existe pas ou si la
     *         transition n'est pas autorisée (statut actuel ≠ a_preparer).
     */
    public function marquerPreparee(int $idCommande): void
    {
        $commande = $this->commandeRepo->findById($idCommande);
        if ($commande === null) {
            throw new CommandeValidationException(['Commande introuvable.']);
        }
        if ($commande['statut'] !== 'a_preparer') {
            throw new CommandeValidationException([
                'Cette commande ne peut plus être déclarée préparée '
                . "(statut actuel : {$commande['statut']}).",
            ]);
        }

        $this->commandeRepo->marquerPreparee($idCommande);

        $this->traceService->log(
            'preparation',
            'commandes',
            $idCommande,
            "numero={$commande['numero_retrait']}",
        );
    }

    /**
     * Passe une commande au statut `livree` à partir de son numéro de retrait.
     *
     * Conformément au CDC §6.4.1, le rôle Accueil identifie la commande à remettre
     * au client par son numéro de retrait (et non par son ID). Cette signature reflète
     * directement l'entrée utilisateur attendue côté contrôleur Sprint 8 :
     *     POST /commandes/livraison   { numero_retrait: "R-123456" }
     *
     * Le retour (commande complète) est destiné à afficher à l'opérateur Accueil
     * un récapitulatif post-livraison (numéro, total, heure de retrait, statut
     * actualisé) sans nécessiter un deuxième aller-retour en base.
     *
     * @throws CommandeValidationException si la commande n'existe pas ou n'est
     *         pas dans le statut `preparee`.
     *
     * @return array<string, mixed> Commande re-chargée depuis la BDD avec les
     *         champs : id_commande, numero_retrait, source, type_service, statut,
     *         total, date_commande, date_heure_retrait_prevue, date_preparation,
     *         date_livraison, id_utilisateur_auteur, auteur_identifiant.
     */
    public function marquerLivreeParNumeroRetrait(string $numeroRetrait): array
    {
        $numero = trim($numeroRetrait);
        if ($numero === '') {
            throw new CommandeValidationException(['Le numéro de retrait est requis.']);
        }

        $commande = $this->commandeRepo->findByNumeroRetrait($numero);
        if ($commande === null) {
            throw new CommandeValidationException([
                "Aucune commande trouvée pour le numéro de retrait « {$numero} ».",
            ]);
        }
        if ($commande['statut'] !== 'preparee') {
            throw new CommandeValidationException([
                'Cette commande ne peut pas être déclarée livrée '
                . "(statut actuel : {$commande['statut']}).",
            ]);
        }

        $this->commandeRepo->marquerLivree((int) $commande['id_commande']);

        $this->traceService->log(
            'livraison',
            'commandes',
            (int) $commande['id_commande'],
            "numero={$commande['numero_retrait']}",
        );

        // Re-charger pour refléter date_livraison et nouveau statut
        return $this->commandeRepo->findById((int) $commande['id_commande']) ?? $commande;
    }

    // =========================================================================
    // VALIDATION : LIGNE PRODUIT
    // =========================================================================

    /**
     * Valide une ligne de type 'produit' et retourne la structure persistable,
     * ou null en cas d'erreur (les erreurs sont accumulées dans $errors).
     *
     * @param array<int, string> $errors Référence — accumule les messages d'erreur
     *
     * @return array<string, mixed>|null
     */
    private function validerLigneProduit(
        int   $numeroLigne,
        int   $idProduit,
        int   $quantite,
        array &$errors,
    ): ?array {
        $produit = $this->produitRepo->findById($idProduit);

        if ($produit === null) {
            $errors[] = "Ligne {$numeroLigne} : produit introuvable (id {$idProduit}).";
            return null;
        }
        if (!$produit['actif']) {
            $errors[] = "Ligne {$numeroLigne} : produit « {$produit['nom']} » non commandable (archivé).";
            return null;
        }
        if (!$produit['disponible']) {
            $errors[] = "Ligne {$numeroLigne} : produit « {$produit['nom']} » indisponible actuellement.";
            return null;
        }

        $prixUnitaire = (float) $produit['prix'];
        $sousTotal    = $prixUnitaire * $quantite;

        return [
            'type_ligne'             => 'produit',
            'id_produit'             => (int) $produit['id_produit'],
            'id_menu'                => null,
            'libelle_article'        => (string) $produit['nom'],
            'quantite'               => $quantite,
            'prix_unitaire_applique' => number_format($prixUnitaire, 2, '.', ''),
            'sous_total'             => number_format($sousTotal, 2, '.', ''),
            'choix'                  => [],
        ];
    }

    // =========================================================================
    // VALIDATION : LIGNE MENU
    // =========================================================================

    /**
     * Valide une ligne de type 'menu' avec sa composition (sections + choix)
     * et retourne la structure persistable, ou null en cas d'erreur.
     *
     * @param array<int, array<string, mixed>> $choixInput Tableau brut des choix client
     * @param array<int, string>               $errors     Référence — accumule les messages
     *
     * @return array<string, mixed>|null
     */
    private function validerLigneMenu(
        int   $numeroLigne,
        int   $idMenu,
        int   $quantite,
        array $choixInput,
        array &$errors,
    ): ?array {
        $menu = $this->menuRepo->findById($idMenu);

        if ($menu === null) {
            $errors[] = "Ligne {$numeroLigne} : menu introuvable (id {$idMenu}).";
            return null;
        }
        if (!$menu['actif']) {
            $errors[] = "Ligne {$numeroLigne} : menu « {$menu['nom']} » non commandable (archivé).";
            return null;
        }
        if (!$menu['disponible']) {
            $errors[] = "Ligne {$numeroLigne} : menu « {$menu['nom']} » indisponible actuellement.";
            return null;
        }

        $sections = $this->sectionRepo->findByMenuId($idMenu);

        // Regrouper les choix client par id_section_menu pour faciliter la vérification.
        // On accumule les erreurs (continue) plutôt que de retourner immédiatement
        // afin que l'utilisateur voit tous les problèmes de structure d'un coup.
        $choixParSection      = [];
        $structureChoixValide = true;
        foreach ($choixInput as $indexChoix => $choix) {
            $idSection = (int) ($choix['id_section_menu'] ?? 0);
            $idProduit = (int) ($choix['id_produit'] ?? 0);
            if ($idSection < 1 || $idProduit < 1) {
                $errors[]             = "Ligne {$numeroLigne}, choix "
                                      . ($indexChoix + 1)
                                      . ' : structure invalide (section ou produit manquant).';
                $structureChoixValide = false;
                continue;
            }
            $choixParSection[$idSection][] = $idProduit;
        }
        if (!$structureChoixValide) {
            // Inutile de continuer à valider sections/options si la structure est cassée
            return null;
        }

        $choixPersist  = [];
        $totalSupplement = 0.0;

        // Vérifier chaque section du menu : présence, cardinalité, options valides
        foreach ($sections as $section) {
            $idSection         = (int) $section['id_section_menu'];
            $nbChoixClient     = count($choixParSection[$idSection] ?? []);
            $obligatoire       = (bool) $section['obligatoire'];
            $qMin              = (int) $section['quantite_min'];
            $qMax              = (int) $section['quantite_max'];
            $nomSection        = (string) $section['nom'];

            // Sections obligatoires : nb de choix doit être dans [qmin, qmax]
            // Sections facultatives : si nb > 0 alors doit aussi être dans [qmin, qmax]
            if ($obligatoire && $nbChoixClient < $qMin) {
                $errors[] = "Ligne {$numeroLigne} : section « {$nomSection} » exige "
                          . "au minimum {$qMin} choix.";
                continue;
            }
            if ($nbChoixClient > $qMax) {
                $errors[] = "Ligne {$numeroLigne} : section « {$nomSection} » autorise "
                          . "au maximum {$qMax} choix.";
                continue;
            }
            if (!$obligatoire && $nbChoixClient > 0 && $nbChoixClient < $qMin) {
                $errors[] = "Ligne {$numeroLigne} : section « {$nomSection} » exige "
                          . "au minimum {$qMin} choix lorsqu'elle est utilisée.";
                continue;
            }

            // Indexer les options actives de la section par id_produit pour O(1)
            $optionsParProduit = [];
            foreach ($section['options'] as $option) {
                $optionsParProduit[(int) $option['id_produit']] = $option;
            }

            // Pour chaque produit choisi : doit faire partie des options de la section ET être disponible
            foreach ($choixParSection[$idSection] ?? [] as $idProduitChoisi) {
                $option = $optionsParProduit[$idProduitChoisi] ?? null;
                if ($option === null) {
                    $errors[] = "Ligne {$numeroLigne} : option non proposée dans la section "
                              . "« {$nomSection} ».";
                    continue;
                }
                if (!$option['disponible']) {
                    $errors[] = "Ligne {$numeroLigne} : option « {$option['produit_nom']} » "
                              . "actuellement indisponible.";
                    continue;
                }

                $supplement       = (float) $option['supplement_prix'];
                $totalSupplement += $supplement;

                $choixPersist[] = [
                    'id_produit'               => (int) $option['id_produit'],
                    'nom_section'              => $nomSection,
                    'libelle_produit'          => (string) $option['produit_nom'],
                    'prix_supplement_applique' => number_format($supplement, 2, '.', ''),
                ];
            }
        }

        // Refuser les choix portant sur des sections qui n'appartiennent pas au menu
        $sectionsValides = array_map(static fn(array $s) => (int) $s['id_section_menu'], $sections);
        foreach (array_keys($choixParSection) as $idSectionChoisie) {
            if (!in_array($idSectionChoisie, $sectionsValides, true)) {
                $errors[] = "Ligne {$numeroLigne} : section inconnue dans le menu (id {$idSectionChoisie}).";
            }
        }

        if ($errors !== []) {
            return null;
        }

        $prixMenu      = (float) $menu['prix'];
        $prixUnitaire  = $prixMenu + $totalSupplement;
        $sousTotal     = $prixUnitaire * $quantite;

        return [
            'type_ligne'             => 'menu',
            'id_produit'             => null,
            'id_menu'                => (int) $menu['id_menu'],
            'libelle_article'        => (string) $menu['nom'],
            'quantite'               => $quantite,
            'prix_unitaire_applique' => number_format($prixUnitaire, 2, '.', ''),
            'sous_total'             => number_format($sousTotal, 2, '.', ''),
            'choix'                  => $choixPersist,
        ];
    }

    // =========================================================================
    // UTILITAIRES
    // =========================================================================

    /**
     * Génère un numéro de retrait au format `R-XXXXXX` (6 chiffres aléatoires).
     * Décision pragmatique : aléatoire cryptographique simple, collision négligeable
     * sur le volume opérationnel d'un point de vente.
     */
    private function genererNumeroRetrait(): string
    {
        $valeur = random_int(0, 999999);
        return 'R-' . str_pad((string) $valeur, 6, '0', STR_PAD_LEFT);
    }
}
