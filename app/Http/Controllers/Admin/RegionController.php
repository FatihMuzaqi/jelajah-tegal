<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Region::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $regions = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.regions.index', compact('regions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name',
            'code' => 'nullable|string|max:20|unique:regions,code',
        ]);

        $code = !empty($validated['code']) ? strtoupper($validated['code']) : strtoupper(substr(Str::slug($validated['name']), 0, 10));

        Region::create([
            'name' => $validated['name'],
            'code' => $code,
            'level' => 'district',
        ]);

        return redirect()->route('admin.regions.index')->with('status', 'Wilayah / Kecamatan baru berhasil ditambahkan!');
    }

    public function update(Request $request, Region $region): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name,' . $region->id,
            'code' => 'nullable|string|max:20|unique:regions,code,' . $region->id,
        ]);

        $region->update([
            'name' => $validated['name'],
            'code' => !empty($validated['code']) ? strtoupper($validated['code']) : $region->code,
        ]);

        return redirect()->route('admin.regions.index')->with('status', 'Data Wilayah / Kecamatan berhasil diperbarui!');
    }

    public function destroy(Region $region): RedirectResponse
    {
        if ($region->mitras()->count() > 0) {
            return redirect()->route('admin.regions.index')->with('error', 'Wilayah tidak dapat dihapus karena sedang terikat pada akun Mitra aktif.');
        }

        $region->delete();

        return redirect()->route('admin.regions.index')->with('status', 'Wilayah / Kecamatan berhasil dihapus!');
    }
}
