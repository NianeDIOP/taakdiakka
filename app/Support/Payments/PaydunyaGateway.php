<?php

namespace App\Support\Payments;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Passerelle PayDunya. Les clés sont saisies depuis l'admin (table settings),
 * jamais en dur. Tant qu'elles ne sont pas renseignées, isConfigured() renvoie
 * false et le système retombe automatiquement sur la passerelle de démonstration.
 *
 * Référence API : https://paydunya.com/developers
 */
class PaydunyaGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'paydunya';
    }

    public function name(): string
    {
        return 'PayDunya';
    }

    private function mode(): string
    {
        return Setting::get('payment.paydunya_mode', 'test'); // test | live
    }

    private function creds(): array
    {
        return [
            'master'  => Setting::get('payment.paydunya_master_key'),
            'private' => Setting::get('payment.paydunya_private_key'),
            'token'   => Setting::get('payment.paydunya_token'),
        ];
    }

    public function isConfigured(): bool
    {
        $c = $this->creds();

        return ! empty($c['master']) && ! empty($c['private']) && ! empty($c['token']);
    }

    private function baseUrl(): string
    {
        return $this->mode() === 'live'
            ? 'https://app.paydunya.com/api/v1'
            : 'https://app.paydunya.com/sandbox-api/v1';
    }

    private function headers(): array
    {
        $c = $this->creds();

        return [
            'PAYDUNYA-MASTER-KEY'  => $c['master'],
            'PAYDUNYA-PRIVATE-KEY' => $c['private'],
            'PAYDUNYA-TOKEN'       => $c['token'],
            'Content-Type'         => 'application/json',
        ];
    }

    public function initiate(array $context, string $returnUrl, string $cancelUrl): PaymentResult
    {
        if (! $this->isConfigured()) {
            return PaymentResult::fail('PayDunya n\'est pas configuré.');
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl() . '/checkout-invoice/create', [
                    'invoice' => [
                        'total_amount' => $context['amount'] ?? 0,
                        'description'  => $context['label'] ?? 'Abonnement TàakDiàkka',
                    ],
                    'store' => [
                        'name' => Setting::get('site.name', 'TàakDiàkka'),
                    ],
                    'actions' => [
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                    ],
                ]);

            $data = $response->json();

            if (($data['response_code'] ?? null) === '00' && ! empty($data['response_text'])) {
                return PaymentResult::redirect($data['response_text'], $data['token'] ?? '');
            }

            return PaymentResult::fail($data['response_text'] ?? 'Échec de l\'initialisation du paiement.');
        } catch (\Throwable $e) {
            Log::error('PayDunya initiate failed', ['error' => $e->getMessage()]);

            return PaymentResult::fail('Erreur de communication avec PayDunya.');
        }
    }

    public function confirm(string $reference): bool
    {
        if (! $this->isConfigured() || $reference === '') {
            return false;
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->get($this->baseUrl() . '/checkout-invoice/confirm/' . $reference);

            $data = $response->json();

            return ($data['status'] ?? null) === 'completed';
        } catch (\Throwable $e) {
            Log::error('PayDunya confirm failed', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
