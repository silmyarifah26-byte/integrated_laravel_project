<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with search and pagination.
     */
    public function index(Request $request): View
    {
        abort_if(! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->isStaff()), 403, 'Akses Ditolak: Hanya Admin dan Staff yang berhak melihat data kategori.');

        $search = $request->query('search');

        $categories = Category::withCount('products')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new category (Admin only).
     */
    public function create(): View
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak menambah kategori.');

        return view('categories.create');
    }

    /**
     * Store a newly created category (Admin only).
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak menambah kategori.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah terdaftar.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Display the specified category or redirect to edit.
     */
    public function show(Category $category): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola kategori.');

        return redirect()->route('categories.edit', $category);
    }

    /**
     * Show the form for editing the category (Admin only).
     */
    public function edit(Category $category): View
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengubah kategori.');

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category (Admin only).
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengubah kategori.');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah digunakan.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified category (Admin only).
     */
    public function destroy(Category $category): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak menghapus kategori.');

        if ($category->products()->exists()) {
            $count = $category->products()->count();
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih memuat {$count} produk hijab terkait. Hapus atau pindahkan produk terlebih dahulu.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
