<?php
namespace App\Enums;
enum OrderStatus:string { case PendingPayment='pending_payment';case Paid='paid';case Confirmed='confirmed';case Processing='processing';case Fulfilled='fulfilled';case Completed='completed';case Cancelled='cancelled';case Expired='expired';case Refunded='refunded'; }
