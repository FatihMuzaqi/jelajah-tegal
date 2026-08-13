<?php

namespace App\Http\Controllers;

use App\Services\GeminiChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    public function __construct(protected GeminiChatbotService $chatbotService) {}

    /**
     * Handle incoming chatbot message from AJAX widget.
     */
    public function message(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'max:500'],
            'history' => ['nullable', 'array', 'max:10'],
            'history.*.user' => ['nullable', 'string', 'max:500'],
            'history.*.bot' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'reply' => 'Mohon kirimkan pertanyaan Anda dengan panjang maksimal 500 karakter.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        $reply = $this->chatbotService->ask($userMessage, $history);

        return response()->json([
            'success' => true,
            'reply' => $reply,
        ]);
    }
}
