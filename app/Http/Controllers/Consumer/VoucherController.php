<?php
namespace App\Http\Controllers\Consumer;
use App\Http\Controllers\Controller;use App\Services\Vouchers\VoucherEngine;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;
class VoucherController extends Controller { public function claim(Request $r,VoucherEngine $engine):RedirectResponse{$d=$r->validate(['code'=>'required|string|max:64','mitra_id'=>'nullable|exists:mitras,id']);$engine->claim($r->user(),$d['code'],$d['mitra_id']??null);return back()->with('status','Voucher berhasil diklaim.');} }
