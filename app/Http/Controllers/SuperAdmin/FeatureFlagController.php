<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function index()
    {
        $flags = FeatureFlag::all();

        return view('super-admin.flags.index', compact('flags'));
    }

    public function toggle(Request $request, FeatureFlag $flag): RedirectResponse
    {
        $flag->update([
            'status' => $flag->status === 'enabled' ? 'disabled' : 'enabled',
        ]);

        return back()->with('status', "Status feature flag {$flag->key} berhasil diperbarui.");
    }
}
