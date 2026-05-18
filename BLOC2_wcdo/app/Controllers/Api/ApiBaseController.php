<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\BaseController;

/**
 * Base commune aux contrôleurs API REST.
 *
 * Centralise :
 *  - l'authentification par clé API (`X-API-Key`)
 *  - le décodage strict du body JSON
 *  - les helpers de réponse JSON (succès, erreur, validation)
 *
 * Conformément au CDC, l'API ne s'appuie pas sur les sessions du back-office :
 * elle est `stateless`, identifiée par une clé partagée stockée dans la variable
 * d'environnement `API_KEY` (cf. `.env`).
 */
abstract class ApiBaseController extends BaseController
{
    /**
     * Vérifie la présence et la validité de l'en-tête `X-API-Key`.
     * Termine la requête avec un `401` JSON si la clé est absente ou invalide.
     *
     * La comparaison est effectuée avec `hash_equals()` pour résister aux
     * attaques temporelles (OWASP A02 — Cryptographic Failures).
     */
    protected function requireApiKey(): void
    {
        $expected = (string) getenv('API_KEY');

        if ($expected === '') {
            // Mauvaise configuration serveur : on refuse plutôt que d'accepter
            // une API non protégée. Pas de détail technique côté client.
            $this->jsonError('Service indisponible.', 503);
        }

        // PHP normalise X-API-Key en HTTP_X_API_KEY dans $_SERVER
        $provided = (string) ($_SERVER['HTTP_X_API_KEY'] ?? '');

        if ($provided === '' || !hash_equals($expected, $provided)) {
            $this->jsonError('Clé API invalide ou manquante.', 401);
        }
    }

    /**
     * Décode et retourne le body JSON de la requête.
     * Termine la requête avec un `400` JSON si le body est absent ou mal formé.
     *
     * @return array<string, mixed>
     */
    protected function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            $this->jsonError('Corps de requête JSON requis.', 400);
        }

        try {
            $decoded = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->jsonError('JSON invalide : ' . $e->getMessage(), 400);
        }

        if (!is_array($decoded)) {
            $this->jsonError('Le corps JSON doit être un objet.', 400);
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * Émet une réponse JSON de succès et termine la requête.
     *
     * @param array<string, mixed> $data
     */
    protected function jsonSuccess(array $data, int $status = 200): void
    {
        $this->json($data, $status);
        exit;
    }

    /**
     * Émet une réponse JSON d'erreur simple et termine la requête.
     */
    protected function jsonError(string $message, int $status = 400): void
    {
        $this->json(['erreur' => $message], $status);
        exit;
    }

    /**
     * Émet une réponse JSON d'erreurs de validation et termine la requête.
     *
     * @param list<string> $erreurs
     */
    protected function jsonValidationErrors(array $erreurs, int $status = 400): void
    {
        $this->json(['erreurs' => array_values($erreurs)], $status);
        exit;
    }
}
