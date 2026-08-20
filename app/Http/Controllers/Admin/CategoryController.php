<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $serviceTypes = ServiceType::orderBy('name')->get();

        $query = Category::with('serviceType');

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('service_type_id')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories', 'serviceTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120|unique:categories,slug',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        
        // Pastikan slug unik jika dibuat otomatis
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        Category::create([
            'service_type_id' => $validated['service_type_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:120|unique:categories,slug,' . $category->id,
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'service_type_id' => $validated['service_type_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil dihapus!');
    }
}
