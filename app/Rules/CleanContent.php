<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CleanContent implements ValidationRule
{
    /**
     * Forbidden word lists categorized by violation type.
     */
    protected array $gamblingWords = [
        'slot', 'gacor', 'maxwin', 'zeus', 'pragmatic', 'pragmaticplay', 'togel', 'poker',
        'casino', 'kasino', 'scatter', 'sbobet', 'judol', 'judi', 'depo', 'wd', 'rtp',
        'rtp live', 'situs gacor', 'link slot', 'bandar', 'taruhan', 'jackpot', 'freebet',
        'bonus new member', 'pola gacor', 'mahjong ways', 'kakek zeus', 'starlight princess',
        'gates of olympus', 'hoki slot', 'agen slot', 'slot88', 'bocoran slot', 'menang slot',
    ];

    protected array $illegalLoanWords = [
        'pinjol', 'pinjaman online', 'dana ghaib', 'dana cepat', 'tanpa jaminan',
        'bunga rendah cair cepat', 'butuh dana', 'gestun', 'pinjam uang', 'kredit kilat',
        'dana instan', 'pinjaman kilat', 'cair cepat', 'modal ktp', 'tanpa bi checking',
        'pinjaman dana', 'jasa gestun', 'pinjam dana', 'dana talangan',
    ];

    protected array $pornWords = [
        'bokep', 'porn', 'porno', 'sex', 'seks', 'vcs', 'open bo', 'openbo', 'lendir',
        'bugil', 'mesum', 'desah', 'pepek', 'kontol', 'memek', 'ngentot', 'colmek',
        'sange', 'toket', 'bispak', 'tete', 'lonte', 'psk', 'jablay', 'video viral',
        'video 18+', 'video panas', 'video mesum', 'ngocok', 'coli', 'ngewe', 'telanjang',
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            return;
        }

        $text = strtolower($value);

        // 1. Check for URL / External Link spam
        $urlPatterns = [
            '/(?:https?:\/\/|ftp:\/\/|www\.)[^\s]+/i',
            '/\b[a-zA-Z0-9-]+\.(?:com|net|org|id|xyz|top|vip|link|club|site|online|cc|click|live|shop|win|bet|casino|me|io|info|biz|asia|tv|app)\b/i',
            '/\b(?:bit\.ly|t\.me|wa\.me|wa\.link|tinyurl\.com|linktr\.ee|s\.id|cutt\.ly|gg\.gg)\/[^\s]+/i',
        ];

        foreach ($urlPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail('Konten tidak diperbolehkan menyertakan link/tautan website atau kontak spam eksternal.');
                return;
            }
        }

        // Normalize text for word matching (remove repeated symbols / spaces disguised as words)
        $normalizedText = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $normalizedText = preg_replace('/\s+/', ' ', $normalizedText);

        // 2. Check for Gambling words
        foreach ($this->gamblingWords as $word) {
            if ($this->containsWord($normalizedText, $word)) {
                $fail('Konten tidak boleh mengandung kata/promosi terkait judi online atau perjudian (' . e($word) . ').');
                return;
            }
        }

        // 3. Check for Illegal Loans (Pinjol)
        foreach ($this->illegalLoanWords as $word) {
            if ($this->containsWord($normalizedText, $word)) {
                $fail('Konten tidak boleh mengandung penawaran/kata terkait pinjaman online (pinjol) atau dana ilegal.');
                return;
            }
        }

        // 4. Check for Pornography / Obscenity
        foreach ($this->pornWords as $word) {
            if ($this->containsWord($normalizedText, $word)) {
                $fail('Konten tidak boleh mengandung kata atau unsur pornografi, asusila, atau kata tidak senonoh.');
                return;
            }
        }
    }

    /**
     * Check if a word or phrase exists in normalized text with word boundary matching.
     */
    protected function containsWord(string $text, string $word): bool
    {
        $escaped = preg_quote($word, '/');
        return (bool) preg_match('/\b' . $escaped . '\b/i', $text);
    }
}
