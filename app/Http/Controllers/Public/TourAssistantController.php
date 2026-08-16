<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\TourAssistant\ItineraryGeneratorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourAssistantController extends Controller
{
    public function __construct(private ItineraryGeneratorService $generator) {}

    public function index(): View
    {
        return view('public.tour-assistant.index');
    }

    public function generate(Request $request): View
    {
        $data = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget' => 'required|numeric|min:10000',
            'pax' => 'required|integer|min:1|max:50',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string|in:accommodation,tourism,culinary,event,rental',
        ]);

        $options = $this->generator->generate($data['start_date'], $data['end_date'], $data['budget'], $data['pax'], $data['categories']);

        return view('public.tour-assistant.result', compact('options', 'data'));
    }
}
