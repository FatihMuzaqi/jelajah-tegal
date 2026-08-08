<?php

namespace App\Enums;

enum CatalogStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Published = 'published';
    case Rejected = 'rejected';
    case Archived = 'archived';
    case TakenDown = 'taken_down';
}
