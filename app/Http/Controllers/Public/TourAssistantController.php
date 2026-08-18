<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\TourAssistant\ItineraryGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourAssistantController extends Controller
{
    public function __construct(private ItineraryGeneratorService $generator) {}

    public function index(): View
    {
        return view('public.tour-assistant.index');
    }

    public function generate(Request $request): View|RedirectResponse
    {
        // 1. Jika diakses via GET tanpa parameter lengkap
        if ($request->isMethod('get')) {
            if (!$request->has(['start_date', 'budget', 'pax', 'categories'])) {
                // Cek apakah ada riwayat generate sebelumnya di session
                if (session()->has('last_tour_assistant')) {
                    $cached = session('last_tour_assistant');
                    $options = $cached['options'];
                    $data = $cached['data'];
                    return view('public.tour-assistant.result', compact('options', 'data'));
                }

                // Redirect dengan aman ke formulir jika belum ada data preferensi
                return redirect()->route('tour-assistant.index')->with('info', 'Silakan tentukan preferensi liburan Anda terlebih dahulu.');
            }
        }

        $data = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget' => 'required|numeric|min:10000',
            'pax' => 'required|integer|min:1|max:50',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string|in:accommodation,tourism,culinary,event,rental',
        ]);

        $options = $this->generator->generate($data['start_date'], $data['end_date'], $data['budget'], $data['pax'], $data['categories']);

        // Simpan ke session agar saat refresh halaman (F5) / kembali dari halaman lain tidak hilang atau error
        session(['last_tour_assistant' => [
            'options' => $options,
            'data' => $data,
        ]]);

        return view('public.tour-assistant.result', compact('options', 'data'));
    }
}
