<?php

namespace App\Support\Payments;

class PaymentResult
{
    public function __construct(
        public bool $ok,
        public ?string $redirectUrl = null,
        public ?string $reference = null,
        public ?string $error = null,
    ) {}

    public static function redirect(string $url, string $reference): self
    {
        return new self(true, $url, $reference);
    }

    public static function fail(string $error): self
    {
        return new self(false, error: $error);
    }
}
