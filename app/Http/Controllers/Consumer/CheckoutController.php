<?php
namespace App\Http\Controllers\Consumer;
use App\Actions\Checkout\CreateCheckout;use App\Http\Controllers\Controller;use App\Http\Requests\Consumer\CheckoutRequest;use App\Models\Order;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;use Illuminate\View\View;
class CheckoutController extends Controller
{public function store(CheckoutRequest $r,CreateCheckout $action):RedirectResponse{$order=$action->execute($r->user(),$r->validated());return redirect()->route('consumer.orders.show',$order)->with('status','Checkout '.$order->order_number.' tersimpan aman.');}public function index(Request $r):View{$orders=Order::where('user_id',$r->user()->id)->with('items')->latest()->paginate(15);return view('consumer.orders.index',compact('orders'));}public function show(Request $r,Order $order):View{abort_unless($order->user_id===$r->user()->id,403);return view('consumer.orders.show',['order'=>$order->load(['items.tickets','payments','voucher'])]);}}
