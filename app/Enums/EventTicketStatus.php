<?php

namespace App\Enums;

enum EventTicketStatus: string
{
    case Issued = 'issued';
    case Used = 'used';
    case Void = 'void';
    case Expired = 'expired';
}

