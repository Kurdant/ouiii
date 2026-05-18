<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception métier levée par CommandeService lorsqu'une commande
 * ne respecte pas les règles de cohérence : ligne invalide, produit
 * indisponible, menu mal composé, etc.
 *
 * Porte une liste plate d'erreurs lisibles destinées à être affichées
 * à l'utilisateur (back-office) ou retournées au client (API).
 */
final class CommandeValidationException extends RuntimeException
{
    /** @var array<int, string> */
    private array $errors;

    /**
     * @param array<int, string> $errors Liste des messages d'erreur
     */
    public function __construct(array $errors, string $message = 'Commande invalide.')
    {
        parent::__construct($message);
        $this->errors = array_values($errors);
    }

    /**
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors[0] ?? null;
    }
}
