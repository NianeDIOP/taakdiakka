<?php

namespace App\Support\Payments;

/**
 * Passerelle de démonstration : simule un paiement réussi sans service externe.
 * Utilisée tant que PayDunya n'est pas configuré. Redirige vers une page de
 * paiement factice interne qui imite le tunnel d'un vrai prestataire.
 */
class StubGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'stub';
    }

    public function name(): string
    {
        return 'Paiement de démonstration';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function initiate(array $context, string $returnUrl, string $cancelUrl): PaymentResult
    {
        $reference = 'STUB-' . strtoupper(uniqid());

        $url = route('subscribe.simulate', [
            'ref'    => $reference,
            'return' => $returnUrl,
            'cancel' => $cancelUrl,
            'amount' => $context['amount'] ?? 0,
            'label'  => $context['label'] ?? '',
        ]);

        return PaymentResult::redirect($url, $reference);
    }

    public function confirm(string $reference): bool
    {
        // Paiement simulé : toujours validé.
        return str_starts_with($reference, 'STUB-');
    }
}
