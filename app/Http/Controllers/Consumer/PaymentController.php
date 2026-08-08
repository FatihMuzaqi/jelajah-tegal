<?php
namespace App\Http\Controllers\Consumer;
use App\Actions\Payments\CreateSnapTransaction;use App\Http\Controllers\Controller;use App\Models\Order;use App\Services\Payments\MidtransConfiguration;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;
class PaymentController extends Controller
{public function snap(Request $request,Order $order,CreateSnapTransaction $action,MidtransConfiguration $configuration):RedirectResponse{abort_unless($order->user_id===$request->user()->id,403);abort_unless($configuration->enabled(),503,'Payment Midtrans dinonaktifkan.');$configuration->assertReady();$payment=$action->execute($order->payments()->firstOrFail());return redirect()->away($payment->snap_redirect_url);}}
