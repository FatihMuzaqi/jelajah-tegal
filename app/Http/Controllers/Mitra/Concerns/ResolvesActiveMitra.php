<?php

namespace App\Http\Controllers\Mitra\Concerns;

use App\Models\Mitra;
use Illuminate\Http\Request;

trait ResolvesActiveMitra
{
    private function activeMitra(Request $request): Mitra
    {
        return Mitra::query()->findOrFail($request->session()->get('active_mitra_id'));
    }
}
