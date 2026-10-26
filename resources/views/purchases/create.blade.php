@extends('layouts.app', ['title' => 'Catat Pembelian Boneka (Stock-In)'])

@section('content')
<div class="max-w-5xl mx-auto py-4 space-y-6">
    <div class="mb-4">
        <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Pembelian
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Form Pembelian Boneka (Stock-In)</h1>
                <p class="text-xs text-slate-500 mt-1">Catat pengadaan multi-item boneka dari pabrik/supplier untuk menambah saldo stok fisik secara otomatis.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                <i class="fa-solid fa-boxes-packing mr-1.5"></i> Stock-In Otomatis
            </span>
        </div>

        @if(isset($selectedProduct) && $selectedProduct)
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center space-x-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                    <div>
                        <h2 class="font-extrabold text-sm text-amber-900">
                            Mode Restock Cepat: Boneka {{ $selectedProduct->name }} telah dipilih otomatis untuk penambahan stok.
                        </h2>
                        <p class="text-xs text-amber-700 mt-0.5">
                            Stok Saat Ini: <strong>{{ $selectedProduct->stock }} pcs</strong> &bull; Batas Minimum: <strong>{{ $selectedProduct->minimum_stock }} pcs</strong> &bull; Estimasi Harga Beli: <strong>Rp {{ number_format($selectedProduct->purchase_price, 0, ',', '.') }}</strong>
                        </p>
                    </div>
                </div>
                <a href="{{ route('purchases.create') }}" class="text-xs font-bold text-amber-800 hover:text-amber-950 underline flex-shrink-0">
                    Bersihkan Pilihan
                </a>
            </div>
        @endif

        <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" class="space-y-6">
            @csrf

            <!-- Header Information Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200/80">
                <!-- Supplier Selection -->
                <div class="sm:col-span-6">
                    <label for="supplier_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pemasok / Pabrik Boneka <span class="text-rose-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-medium">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchased Date -->
                <div class="sm:col-span-6">
                    <label for="purchased_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal & Waktu Pengadaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="purchased_at" id="purchased_at" required
                        value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-medium">
                    @error('purchased_at')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="sm:col-span-12">
                    <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Transaksi (Opsional)
                    </label>
                    <input type="text" name="notes" id="notes"
                        value="{{ old('notes') }}"
                        placeholder="Contoh: Pengadaan batch boneka teddy bear untuk musim liburan akhir tahun..."
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    @error('notes')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Items Table Section -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center">
                            <i class="fa-solid fa-list-check text-blue-600 mr-2"></i> Daftar Boneka yang Dibeli (Multi-Item)
                        </h2>
                        <p class="text-xs text-slate-500">Pilih boneka, tentukan kuantitas masuk, dan verifikasi harga beli per unit.</p>
                    </div>

                    <button type="button" id="addRowBtn" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold transition-colors">
                        <i class="fa-solid fa-plus mr-1.5"></i> Tambah Baris Boneka
                    </button>
                </div>

                @error('items')
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 font-bold">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Dynamic Items Table -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm text-slate-600" id="itemsTable">
                            <thead class="bg-slate-50 text-[11px] uppercase font-extrabold text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-7/12 sm:w-5/12">Pilih Produk Boneka</th>
                                    <th class="px-3 py-3 w-2/12 sm:w-2/12 text-center">Stok Saat Ini</th>
                                    <th class="px-3 py-3 w-2/12 sm:w-2/12 text-center">Jumlah Masuk</th>
                                    <th class="px-3 py-3 w-3/12 sm:w-3/12 text-right">Harga Beli Satuan (Rp)</th>
                                    <th class="px-3 py-3 w-2/12 text-right">Subtotal (Rp)</th>
                                    <th class="px-2 py-3 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTbody" class="divide-y divide-slate-100">
                                <!-- Dynamic rows inserted by JavaScript -->
                            </tbody>
                            <tfoot class="bg-slate-50 font-black text-slate-900 border-t border-slate-200">
                                <tr>
                                    <td colspan="4" class="px-4 py-3.5 text-right uppercase tracking-wider text-xs">
                                        Total Biaya Pengadaan:
                                    </td>
                                    <td class="px-3 py-3.5 text-right text-base text-blue-700" id="grandTotalDisplay">
                                        Rp 0
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('purchases.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm shadow-md transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan & Tambah Stok Boneka</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template for JavaScript Data -->
<script>
    const productsData = @json($products);
    const oldItems = @json(old('items', []));
    const preselectedProduct = @json($selectedProduct ?? null);

    let rowIndex = 0;

    function createRow(item = null) {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/60 transition-colors item-row';
        tr.dataset.index = rowIndex;

        const selectedProductId = item ? item.product_id : '';
        const quantity = item ? item.quantity : 1;
        const unitCost = item ? item.unit_cost : 0;

        let optionsHtml = '<option value="">-- Pilih Boneka --</option>';
        productsData.forEach(p => {
            const isSelected = selectedProductId == p.id ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-cost="${p.purchase_price}" data-stock="${p.stock}" data-sku="${p.sku}" data-size="${p.size || ''}" ${isSelected}>
                ${p.name} [${p.sku}] - ${p.size || ''}
            </option>`;
        });

        tr.innerHTML = `
            <td class="px-4 py-3">
                <select name="items[${rowIndex}][product_id]" required class="product-select w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-medium">
                    ${optionsHtml}
                </select>
            </td>
            <td class="px-3 py-3 text-center text-xs font-bold text-slate-500 current-stock-cell">
                -
            </td>
            <td class="px-3 py-3 text-center">
                <input type="number" name="items[${rowIndex}][quantity]" value="${quantity}" min="1" required
                    class="qty-input w-24 text-center px-2 py-1.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </td>
            <td class="px-3 py-3 text-right">
                <input type="number" step="0.01" name="items[${rowIndex}][unit_cost]" value="${unitCost}" min="0" required
                    class="cost-input w-full text-right px-3 py-1.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </td>
            <td class="px-3 py-3 text-right font-black text-slate-900 subtotal-display">
                Rp 0
            </td>
            <td class="px-2 py-3 text-center">
                <button type="button" class="remove-row-btn p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Baris">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            </td>
        `;

        document.getElementById('itemsTbody').appendChild(tr);

        // Bind events
        const select = tr.querySelector('.product-select');
        const qtyInput = tr.querySelector('.qty-input');
        const costInput = tr.querySelector('.cost-input');
        const removeBtn = tr.querySelector('.remove-row-btn');

        select.addEventListener('change', () => {
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                const defaultCost = selectedOpt.getAttribute('data-cost') || 0;
                const stock = selectedOpt.getAttribute('data-stock') || 0;
                if (!item || !item.unit_cost) {
                    costInput.value = parseFloat(defaultCost).toFixed(2);
                }
                tr.querySelector('.current-stock-cell').textContent = `${stock} pcs`;
            } else {
                tr.querySelector('.current-stock-cell').textContent = '-';
            }
            updateRowSubtotal(tr);
        });

        qtyInput.addEventListener('input', () => updateRowSubtotal(tr));
        costInput.addEventListener('input', () => updateRowSubtotal(tr));

        removeBtn.addEventListener('click', () => {
            const allRows = document.querySelectorAll('.item-row');
            if (allRows.length <= 1) {
                alert('Minimal harus ada 1 baris boneka dalam transaksi.');
                return;
            }
            tr.remove();
            calculateGrandTotal();
        });

        // Trigger change for initial stock & cost setup
        if (selectedProductId) {
            costInput.value = parseFloat(unitCost).toFixed(2);
            select.dispatchEvent(new Event('change'));
        }

        updateRowSubtotal(tr);
        rowIndex++;
    }

    function updateRowSubtotal(tr) {
        const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(tr.querySelector('.cost-input').value) || 0;
        const subtotal = qty * cost;
        tr.querySelector('.subtotal-display').textContent = formatRupiah(subtotal);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(tr.querySelector('.cost-input').value) || 0;
            grandTotal += (qty * cost);
        });
        document.getElementById('grandTotalDisplay').textContent = formatRupiah(grandTotal);
    }

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(number));
    }

    // Initialize with old items, preselected restock product, or 1 empty row
    document.addEventListener('DOMContentLoaded', () => {
        if (oldItems && oldItems.length > 0) {
            oldItems.forEach(item => createRow(item));
        } else if (preselectedProduct) {
            const suggestedQty = Math.max(10, ((preselectedProduct.minimum_stock || 5) * 2) - preselectedProduct.stock);
            createRow({
                product_id: preselectedProduct.id,
                quantity: suggestedQty > 0 ? suggestedQty : 10,
                unit_cost: preselectedProduct.purchase_price
            });
        } else {
            createRow();
        }

        document.getElementById('addRowBtn').addEventListener('click', () => {
            createRow();
        });
    });
</script>
@endsection
