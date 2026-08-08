<?php

namespace App\Services\Checkout\Contracts;

use App\Models\User;
use App\Services\Checkout\CheckoutQuote;

interface CheckoutAdapter
{
    public function quoteAndReserve(User $user, array $payload): CheckoutQuote;
}
