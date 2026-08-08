<?php

namespace App\Support;

final class TicketToken
{
    public static function for(string $id, int $version = 1): string
    {
        $message = $id.'.v'.$version;
        return $message.'.'.hash_hmac('sha256', $message, (string) config('tickets.signing_key'));
    }

    public static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
