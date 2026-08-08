<?php
namespace App\Enums;
enum WithdrawalStatus:string
{case Submitted='submitted';case UnderReview='under_review';case Approved='approved';case Rejected='rejected';case Processing='processing';case Paid='paid';case Cancelled='cancelled';}
