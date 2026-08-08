<?php

namespace App\Services\Payments;

class MidtransSignature
{
    public function __construct(private MidtransConfiguration $configuration) {}

    public function valid(array $payload): bool
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key'] as $field) {
            if (! is_string($payload[$field] ?? null) || $payload[$field] === '') {
                return false;
            }
        }
        $expected = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$this->configuration->serverKey());
        return hash_equals($expected, strtolower($payload['signature_key']));
    }
}
