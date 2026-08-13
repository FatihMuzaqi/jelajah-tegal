<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ChatbotSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'api_key',
        'model',
        'base_url',
        'system_prompt_addition',
        'temperature',
        'max_tokens',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'temperature' => 'decimal:2',
            'max_tokens' => 'integer',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Dapatkan instance pengaturan chatbot aktif (dengan cache).
     */
    public static function current(): self
    {
        $cached = Cache::get('active_chatbot_settings');

        if ($cached instanceof self) {
            return $cached;
        }

        $setting = static::query()->first();

        if (! $setting) {
            $setting = static::query()->create([
                'is_enabled' => true,
                'api_key' => config('services.gemini.api_key', env('GEMINI_API_KEY', '')),
                'model' => config('services.gemini.model', env('GEMINI_MODEL', 'gemini-3.5-flash')),
                'base_url' => config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'),
                'system_prompt_addition' => null,
                'temperature' => 0.70,
                'max_tokens' => 800,
            ]);
        }

        Cache::put('active_chatbot_settings', $setting, 300);

        return $setting;
    }

    /**
     * Periksa apakah chatbot sedang aktif.
     */
    public static function isEnabled(): bool
    {
        return static::current()->is_enabled;
    }

    /**
     * Bersihkan cache pengaturan chatbot.
     */
    public static function clearCache(): void
    {
        Cache::forget('active_chatbot_settings');
        Cache::forget('chatbot_knowledge_base');
    }
}
