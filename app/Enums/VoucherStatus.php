<?php
namespace App\Enums;
enum VoucherStatus:string { case Draft='draft';case Active='active';case Paused='paused';case Expired='expired';case Exhausted='exhausted';case Archived='archived'; }
