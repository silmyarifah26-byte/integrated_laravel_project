@extends('layouts.app', ['title' => 'Catat Pengadaan Hijab (Stock-In)'])

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    <div>
        <a href="{{ route('purchases.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Pembelian
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-lg border border-gray-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Form Pengadaan Hijab (Stock-In)</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Catat pengadaan produk dari supplier untuk menambah stok toko.</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                Stock-In Otomatis
            </span>
        </div>

        @if(isset($selectedProduct) && $selectedProduct)
            <div class="mb-5 p-3.5 rounded-md bg-yellow-50 border border-yellow-200 text-yellow-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <div>
                    <span class="font-semibold">
                        Mode Restock Cepat: Produk {{ $selectedProduct->name }} telah dipilih otomatis untuk penambahan stok.
                    </span>
                    <span class="block text-yellow-700 mt-0.5">
                        SKU: {{ $selectedProduct->sku }} &bull; Sisa Stok: {{ $selectedProduct->stock }} pcs &bull; Batas Minimum: {{ $selectedProduct->minimum_stock }} pcs
                    </span>
                </div>
                <a href="{{ route('purchases.create') }}" class="text-yellow-800 hover:underline flex-shrink-0 font-medium">
                    Reset
                </a>
            </div>
        @endif

        <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" class="space-y-5">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 bg-gray-50 p-4 rounded-md border border-gray-200">
                <!-- Supplier Selection -->
                <div class="sm:col-span-6">
                    <label for="supplier_id" class="block text-xs font-medium text-gray-700 mb-1">
                        Pemasok / Supplier <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required
                        class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchased Date -->
                <div class="sm:col-span-6">
                    <label for="purchased_at" class="block text-xs font-medium text-gray-700 mb-1">
                        Tanggal & Waktu Pengadaan <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="purchased_at" id="purchased_at" required
                        value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    @error('purchased_at')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="sm:col-span-12">
                    <label for="notes" class="block text-xs font-medium text-gray-700 mb-1">
                        Catatan Transaksi (Opsional)
                    </label>
                    <input type="text" name="notes" id="notes"
                        value="{{ old('notes') }}"
                        placeholder="Contoh: Pengadaan stok awal pashmina..."
                        class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    @error('notes')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Items Table Section -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">
                            Daftar Produk Hijab yang Dibeli
                        </h2>
                    </div>

                    <button type="button" id="addRowBtn" class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Baris
                    </button>
                </div>

                @error('items')
                    <div class="p-3 rounded-md bg-red-50 border border-red-200 text-xs text-red-700">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Dynamic Items Table -->
                <div class="bg-white rounded-md border border-gray-200 overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm text-gray-700" id="itemsTable">
                            <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 py-2.5 w-6/12 sm:w-5/12">Produk Hijab</th>
                                    <th class="px-2 py-2.5 text-center">Stok</th>
                                    <th class="px-2 py-2.5 text-center">Jumlah</th>
                                    <th class="px-3 py-2.5 text-right">Harga Beli Satuan (Rp)</th>
                                    <th class="px-3 py-2.5 text-right">Subtotal (Rp)</th>
                                    <th class="px-2 py-2.5 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTbody" class="divide-y divide-gray-200">
                                <!-- Dynamic rows inserted by JavaScript -->
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold text-gray-900 border-t border-gray-200">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right uppercase text-xs text-gray-600">
                                        Total Biaya Pengadaan:
                                    </td>
                                    <td class="px-3 py-3 text-right text-base text-blue-700 font-bold" id="grandTotalDisplay">
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
            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-gray-100">
                <a href="{{ route('purchases.index') }}" class="px-3.5 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan & Tambah Stok Hijab</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const productsData = @json($products);
    const oldItems = @json(old('items', []));
    const preselectedProduct = @json($selectedProduct ?? null);

    let rowIndex = 0;

    function createRow(item = null) {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 item-row';
        tr.dataset.index = rowIndex;

        const selectedProductId = item ? item.product_id : '';
        const quantity = item ? item.quantity : 1;
        const unitCost = item ? item.unit_cost : 0;

        let optionsHtml = '<option value="">-- Pilih Produk --</option>';
        productsData.forEach(p => {
            const isSelected = selectedProductId == p.id ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-cost="${p.purchase_price}" data-stock="${p.stock}" data-sku="${p.sku}" data-size="${p.size || ''}" ${isSelected}>
                ${p.name} [${p.sku}]
            </option>`;
        });

        tr.innerHTML = `
            <td class="px-3 py-2">
                <select name="items[${rowIndex}][product_id]" required class="product-select w-full px-2.5 py-1.5 rounded-md border border-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                    ${optionsHtml}
                </select>
            </td>
            <td class="px-2 py-2 text-center text-xs text-gray-500 current-stock-cell">
                -
            </td>
            <td class="px-2 py-2 text-center">
                <input type="number" name="items[${rowIndex}][quantity]" value="${quantity}" min="1" required
                    class="qty-input w-20 text-center px-2 py-1 rounded-md border border-gray-300 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="px-3 py-2 text-right">
                <input type="number" step="0.01" name="items[${rowIndex}][unit_cost]" value="${unitCost}" min="0" required
                    class="cost-input w-28 text-right px-2 py-1 rounded-md border border-gray-300 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="px-3 py-2 text-right font-medium text-gray-900 subtotal-display">
                Rp 0
            </td>
            <td class="px-2 py-2 text-center">
                <button type="button" class="remove-row-btn p-1 text-gray-400 hover:text-red-600 transition-colors" title="Hapus Baris">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </td>
        `;

        document.getElementById('itemsTbody').appendChild(tr);

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
                alert('Minimal harus ada 1 baris produk dalam transaksi.');
                return;
            }
            tr.remove();
            calculateGrandTotal();
        });

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
