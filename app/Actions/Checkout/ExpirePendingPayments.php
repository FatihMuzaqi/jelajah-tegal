<?php
namespace App\Actions\Checkout;
use App\Models\Order;
class ExpirePendingPayments
{public function __construct(private ReleaseOrder $release){}public function execute():int{$count=0;Order::where('status','pending_payment')->where('expires_at','<=',now())->select('id')->chunkById(100,function($orders)use(&$count){foreach($orders as $order){$this->release->execute($order,'expired');$count++;}});return $count;}}
