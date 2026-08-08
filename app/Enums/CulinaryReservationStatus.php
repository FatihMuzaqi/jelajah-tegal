<?php

namespace App\Enums;

enum CulinaryReservationStatus: string
{
    case Requested = 'requested';
    case Confirmed = 'confirmed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case NoShow = 'no_show';
}

