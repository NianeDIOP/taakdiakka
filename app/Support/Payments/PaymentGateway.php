<?php

namespace App\Support\Payments;

interface PaymentGateway
{
    /** Identifiant technique (stub | paydunya). */
    public function key(): string;

    /** Nom affiché. */
    public function name(): string;

    /** La passerelle est-elle prête à encaisser (clés présentes) ? */
    public function isConfigured(): bool;

    /**
     * Initie un paiement et renvoie l'URL vers laquelle rediriger l'utilisateur.
     *
     * @param  array  $context  ['amount' => int, 'label' => string, 'email' => ?string]
     */
    public function initiate(array $context, string $returnUrl, string $cancelUrl): PaymentResult;

    /** Confirme/vérifie un paiement à partir de sa référence. */
    public function confirm(string $reference): bool;
}
