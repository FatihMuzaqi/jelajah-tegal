<?php
namespace App\Http\Controllers\Mitra;
use App\Http\Controllers\Controller;use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;use App\Models\Order;use Illuminate\Http\Request;use Illuminate\View\View;
class OrderController extends Controller { use ResolvesActiveMitra;public function index(Request $r):View{abort_unless($r->user()->can('orders.view'),403);return view('mitra.orders.index',['orders'=>Order::where('mitra_id',$this->activeMitra($r)->id)->with('items')->latest()->paginate(20)]);}public function show(Request $r,Order $order):View{abort_unless($r->user()->can('orders.view')&&$order->mitra_id===$this->activeMitra($r)->id,403);return view('mitra.orders.show',['order'=>$order->load(['items','payments','user'])]);}}
