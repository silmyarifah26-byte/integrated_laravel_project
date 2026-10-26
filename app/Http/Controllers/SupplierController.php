<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers with search and pagination.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $suppliers = Supplier::withCount('purchases')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    /**
     * Show the form for creating a new supplier (Admin only).
     */
    public function create(): View
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created supplier (Admin only).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama supplier/pabrik wajib diisi.',
            'phone.required' => 'Nomor telepon supplier wajib diisi.',
            'email.email' => 'Format email supplier tidak valid.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Pemasok '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the supplier (Admin only).
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier (Admin only).
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama supplier/pabrik wajib diisi.',
            'phone.required' => 'Nomor telepon supplier wajib diisi.',
            'email.email' => 'Format email supplier tidak valid.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Data pemasok '{$supplier->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified supplier (Admin only).
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            $count = $supplier->purchases()->count();
            return back()->with('error', "Pemasok '{$supplier->name}' tidak dapat dihapus karena sudah memiliki riwayat {$count} transaksi pembelian (Stock-In).");
        }

        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', "Pemasok '{$name}' berhasil dihapus.");
    }
}
