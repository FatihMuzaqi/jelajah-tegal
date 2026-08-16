<?php

namespace App\Http\Controllers\Api;

use App\Actions\Payments\ProcessMidtransNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, ProcessMidtransNotification $action): JsonResponse
    {
        try {
            $event = $action->execute($request->all());
            return response()->json(['accepted' => true, 'event_id' => $event->provider_event_id]);
        } catch (ValidationException $e) {
            $status = array_key_exists('signature_key', $e->errors()) ? 401 : 422;
            return response()->json(['accepted' => false, 'errors' => $e->errors()], $status);
        }
    }
}
