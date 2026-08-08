<?php

namespace App\Enums;

enum RentalBookingStatus: string
{
    case Requested = 'requested';
    case DocumentReview = 'document_review';
    case Approved = 'approved';
    case Active = 'active';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}

