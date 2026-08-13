<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotSetting;
use App\Services\AuditLogger;
use App\Services\GeminiChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotSettingController extends Controller
{
    public function __construct(protected GeminiChatbotService $chatbotService, protected AuditLogger $audit) {}

    public function index(): View
    {
        abort_unless(auth()->user()->can('settings.manage') || auth()->user()->hasRole('super-admin'), 403);

        $setting = ChatbotSetting::current();

        $availableModels = [
            'gemini-3.5-flash' => 'Gemini 3.5 Flash (Sangat Cepat & Direkomendasikan)',
            'gemini-3.6-flash' => 'Gemini 3.6 Flash (Terbaru & Responsif)',
            'gemini-3.1-flash-lite' => 'Gemini 3.1 Flash Lite (Ringan & Hemat Kuota)',
            'gemini-flash-latest' => 'Gemini Flash Latest (Versi Flash Otomatis)',
            'gemini-2.5-pro' => 'Gemini 2.5 Pro (Penalaran Kompleks)',
        ];

        return view('super-admin.chatbot.index', compact('setting', 'availableModels'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('settings.manage') || auth()->user()->hasRole('super-admin'), 403);

        $validated = $request->validate([
            'is_enabled' => ['required', 'boolean'],
            'api_key' => ['nullable', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:64'],
            'base_url' => ['required', 'url', 'max:255'],
            'system_prompt_addition' => ['nullable', 'string', 'max:2000'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:1'],
            'max_tokens' => ['required', 'integer', 'min:100', 'max:4000'],
        ]);

        $setting = ChatbotSetting::query()->first() ?? new ChatbotSetting();
        $before = $setting->toArray();

        $setting->fill($validated);
        $setting->updated_by = $request->user()->id;
        $setting->save();

        ChatbotSetting::clearCache();

        $this->audit->record('superadmin.chatbot_settings_updated', $setting, $before, $setting->fresh()->toArray(), $request->user());

        return redirect()->route('super-admin.chatbot.index')->with('status', 'Konfigurasi AI Chatbot berhasil diperbarui.');
    }

    public function testConnection(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('settings.manage') || auth()->user()->hasRole('super-admin'), 403);

        $apiKey = $request->input('api_key');
        $model = $request->input('model');
        $baseUrl = $request->input('base_url');

        $result = $this->chatbotService->testConnection($apiKey, $model, $baseUrl);

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
