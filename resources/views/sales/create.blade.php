@extends('layouts.app', ['title' => $user->isCustomer() ? "Buat Pesanan Hijab - HijabSociety" : "Form Penjualan Kasir - HijabSociety"])

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    <div>
        <a href="{{ route('sales.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Transaksi
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-lg border border-gray-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    {{ $user->isCustomer() ? "Pemesanan Hijab Cantik HijabSociety" : "Transaksi Penjualan Kasir (Stock-Out)" }}
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                    {{ $user->isCustomer() ? 'Pilih hijab favorit Anda. Stok fisik akan otomatis berkurang saat pesanan berhasil disimpan.' : 'Catat transaksi penjualan kasir toko dengan pemotongan stok otomatis.' }}
                </p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                Stock-Out Otomatis
            </span>
        </div>

        <form action="{{ route('sales.store') }}" method="POST" id="saleForm" class="space-y-5">
            @csrf

            <!-- Customer & Transaction Information -->
            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-4">
                @if($user->isCustomer())
                    <!-- Customer Self-Order Info -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-md border border-gray-200">
                        <div>
                            <span class="text-xs font-semibold text-gray-400 block uppercase">Pemesan</span>
                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }} &bull; {{ $user->phone ?: '-' }}</div>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wider">Kasir Toko Pemroses</span>
                            <span class="text-xs font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 inline-block mt-0.5">
                                Syahputri (Staff Kasir)
                            </span>
                        </div>
                    </div>
                @else
                    <!-- Kasir Toko Bertugas & Pemilihan Pelanggan -->
                    <div class="bg-white p-3.5 rounded-md border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-cash-register"></i>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wider">Petugas Kasir Toko (Aktif)</span>
                                <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">Peran: {{ strtoupper($user->role) }} Toko HijabSociety</div>
                            </div>
                        </div>
                        @if($user->isAdmin() && !empty($cashiers) && count($cashiers) > 1)
                            <div class="w-full sm:w-64">
                                <label for="cashier_id" class="block text-[11px] font-semibold text-gray-600 mb-1">
                                    Pilih Kasir Bertugas
                                </label>
                                <select name="cashier_id" id="cashier_id"
                                    class="w-full px-2.5 py-1.5 rounded-md border border-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 bg-white">
                                    @foreach($cashiers as $c)
                                        <option value="{{ $c->id }}" {{ (old('cashier_id', $user->id) == $c->id) ? 'selected' : '' }}>
                                            {{ $c->name }} ({{ ucfirst($c->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="cashier_id" value="{{ $user->id }}">
                            <span class="px-2.5 py-1 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <i class="fa-solid fa-circle-check text-green-500 mr-1"></i> Kasir POS Aktif
                            </span>
                        @endif
                    </div>

                    <!-- Admin / Staff Customer Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-6">
                            <label for="customer_id" class="block text-xs font-medium text-gray-700 mb-1">
                                Pelanggan Terdaftar (Member)
                            </label>
                            <select name="customer_id" id="customer_id"
                                class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 bg-white">
                                <option value="">-- Pembeli Umum (Non-Member) --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }} ({{ $cust->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Pilih jika pembeli memiliki akun member customer.</p>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="customer_name" class="block text-xs font-medium text-gray-700 mb-1">
                                Nama Pembeli Walk-In / Umum
                            </label>
                            <input type="text" name="customer_name" id="customer_name"
                                value="{{ old('customer_name') }}"
                                placeholder="Contoh: Pembeli Walk-In"
                                class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 bg-white">
                            <p class="text-xs text-gray-400 mt-1">Diisi bila pembeli tidak memiliki akun member.</p>
                        </div>
                    </div>
                @endif

                <!-- Payment Method Selection -->
                <div class="bg-white p-4 rounded-md border border-gray-200 space-y-2">
                    <label class="block text-xs font-medium text-gray-700">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                        <!-- QRIS -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="QRIS" class="peer sr-only" {{ old('payment_method', 'QRIS') === 'QRIS' ? 'checked' : '' }}>
                            <div class="p-2.5 rounded-md border border-gray-300 peer-checked:border-rose-600 peer-checked:bg-rose-50 transition-all text-center">
                                <div class="text-xs font-semibold text-gray-800">QRIS</div>
                                <div class="text-[10px] text-gray-400">Scan & Bayar</div>
                            </div>
                        </label>

                        <!-- Transfer Bank -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Transfer Bank" class="peer sr-only" {{ old('payment_method') === 'Transfer Bank' ? 'checked' : '' }}>
                            <div class="p-2.5 rounded-md border border-gray-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center">
                                <div class="text-xs font-semibold text-gray-800">Transfer Bank</div>
                                <div class="text-[10px] text-gray-400">BCA, Mandiri, BSI</div>
                            </div>
                        </label>

                        <!-- E-Wallet -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="E-Wallet" class="peer sr-only" {{ old('payment_method') === 'E-Wallet' ? 'checked' : '' }}>
                            <div class="p-2.5 rounded-md border border-gray-300 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition-all text-center">
                                <div class="text-xs font-semibold text-gray-800">E-Wallet</div>
                                <div class="text-[10px] text-gray-400">OVO, DANA, GoPay</div>
                            </div>
                        </label>

                        <!-- Tunai / COD -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Tunai / COD" class="peer sr-only" {{ old('payment_method') === 'Tunai / COD' ? 'checked' : '' }}>
                            <div class="p-2.5 rounded-md border border-gray-300 peer-checked:border-green-600 peer-checked:bg-green-50 transition-all text-center">
                                <div class="text-xs font-semibold text-gray-800">Tunai / COD</div>
                                <div class="text-[10px] text-gray-400">Bayar di Tempat</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-medium text-gray-700 mb-1">
                        Catatan Transaksi (Opsional)
                    </label>
                    <input type="text" name="notes" id="notes"
                        value="{{ old('notes') }}"
                        placeholder="Contoh: Kado ulang tahun, dibungkus rapi..."
                        class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 bg-white">
                </div>
            </div>

            <!-- Items Table Section -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">
                            Keranjang Hijab yang Dibeli (Multi-Item)
                        </h2>
                    </div>

                    <button type="button" id="addRowBtn" class="inline-flex items-center px-3 py-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Baris Hijab
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
                                    <th class="px-3 py-2.5 w-6/12 sm:w-5/12">Pilih Hijab</th>
                                    <th class="px-2 py-2.5 text-center">Stok</th>
                                    <th class="px-2 py-2.5 text-center">Jumlah</th>
                                    <th class="px-3 py-2.5 text-right">Harga Jual</th>
                                    <th class="px-3 py-2.5 text-right">Subtotal</th>
                                    <th class="px-2 py-2.5 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTbody" class="divide-y divide-gray-200">
                                <!-- Dynamic rows inserted by JavaScript -->
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold text-gray-900 border-t border-gray-200">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right uppercase text-xs text-gray-600">
                                        Total Tagihan Belanja:
                                    </td>
                                    <td class="px-3 py-3 text-right text-base text-rose-600 font-bold" id="grandTotalDisplay">
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
                <a href="{{ route('sales.index') }}" class="px-3.5 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Konfirmasi & Simpan Pesanan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const availableProducts = @json($products);
    const defaultHijabImg = "{{ asset('images/hijab/pashmina-ceruty.jpg') }}";
    let rowIndex = 0;

    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function addRow(selectedProductId = '', quantity = 1) {
        const tr = document.createElement('tr');
        tr.className = 'item-row hover:bg-gray-50';

        let optionsHtml = '<option value="">-- Pilih Koleksi Hijab --</option>';
        availableProducts.forEach(p => {
            const isSelected = String(p.id) === String(selectedProductId) ? 'selected' : '';
            const imgPath = p.image_asset_url || (p.image_url ? ("{{ asset('') }}" + p.image_url.replace(/^\//, '')) : defaultHijabImg);
            optionsHtml += `<option value="${p.id}" data-price="${p.selling_price}" data-stock="${p.stock}" data-image="${imgPath}" data-img="${imgPath}" ${isSelected}>
                ${p.name} (${p.size || 'Standar'}) - Stok: ${p.stock}
            </option>`;
        });

        tr.innerHTML = `
            <td class="px-3 py-2">
                <div class="flex items-center space-x-2">
                    <img src="${defaultHijabImg}" class="row-thumb w-10 h-10 rounded object-cover border border-gray-200 shrink-0" alt="Thumb">
                    <div class="flex-1">
                        <select name="items[${rowIndex}][product_id]" required
                            class="product-select w-full px-2.5 py-1.5 rounded-md border border-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-rose-500 bg-white">
                            ${optionsHtml}
                        </select>
                    </div>
                </div>
            </td>
            <td class="px-2 py-2 text-center text-xs text-gray-500 stock-cell">
                -
            </td>
            <td class="px-2 py-2 text-center">
                <input type="number" name="items[${rowIndex}][quantity]" value="${quantity}" min="1" required
                    class="qty-input w-20 text-center px-2 py-1 rounded-md border border-gray-300 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-rose-500">
                <span class="stock-warning text-[10px] text-red-600 block mt-0.5 hidden">Stok Kurang!</span>
            </td>
            <td class="px-3 py-2 text-right text-xs text-gray-700 price-display">
                Rp 0
            </td>
            <td class="px-3 py-2 text-right font-medium text-rose-600 subtotal-display">
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
        const removeBtn = tr.querySelector('.remove-row-btn');
        const thumb = tr.querySelector('.row-thumb');

        function updateRowFromSelection() {
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                const stock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
                const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
                const img = selectedOpt.getAttribute('data-image') || selectedOpt.getAttribute('data-img') || defaultHijabImg;
                tr.querySelector('.stock-cell').textContent = `${stock} pcs`;
                tr.querySelector('.price-display').textContent = formatRupiah(price);
                thumb.src = img;
                qtyInput.max = stock;
            } else {
                tr.querySelector('.stock-cell').textContent = '-';
                tr.querySelector('.price-display').textContent = 'Rp 0';
                thumb.src = defaultHijabImg;
            }
            validateAndCalculateRow(tr);
            calculateGrandTotal();
        }

        select.addEventListener('change', updateRowFromSelection);
        qtyInput.addEventListener('input', () => validateAndCalculateRow(tr));

        removeBtn.addEventListener('click', () => {
            const allRows = document.querySelectorAll('.item-row');
            if (allRows.length <= 1) {
                alert('Minimal harus ada 1 baris produk dalam pesanan.');
                return;
            }
            tr.remove();
            calculateGrandTotal();
        });

        if (selectedProductId) {
            select.value = String(selectedProductId);
            for (let i = 0; i < select.options.length; i++) {
                if (String(select.options[i].value) === String(selectedProductId)) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }
        updateRowFromSelection();

        rowIndex++;
    }

    function validateAndCalculateRow(tr) {
        const select = tr.querySelector('.product-select');
        const selectedOpt = select.options[select.selectedIndex];
        const stock = selectedOpt && selectedOpt.value ? (parseInt(selectedOpt.getAttribute('data-stock')) || 0) : 0;
        const price = selectedOpt && selectedOpt.value ? (parseFloat(selectedOpt.getAttribute('data-price')) || 0) : 0;
        const qty = parseInt(tr.querySelector('.qty-input').value) || 0;
        const warning = tr.querySelector('.stock-warning');

        if (selectedOpt && selectedOpt.value && qty > stock) {
            warning.classList.remove('hidden');
            tr.querySelector('.qty-input').classList.add('border-red-500', 'bg-red-50');
        } else {
            warning.classList.add('hidden');
            tr.querySelector('.qty-input').classList.remove('border-red-500', 'bg-red-50');
        }

        const subtotal = price * qty;
        tr.querySelector('.subtotal-display').textContent = formatRupiah(subtotal);
        tr.setAttribute('data-subtotal', subtotal);

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const sub = parseFloat(row.getAttribute('data-subtotal')) || 0;
            grandTotal += sub;
        });
        document.getElementById('grandTotalDisplay').textContent = formatRupiah(grandTotal);
    }

    document.getElementById('addRowBtn').addEventListener('click', () => addRow());

    function initFormRows() {
        const oldItems = @json(old('items', []));
        const selectedProductIdFromQuery = @json($selectedProductId ?? request()->query('product_id'));
        if (oldItems && oldItems.length > 0) {
            oldItems.forEach(item => {
                addRow(item.product_id, item.quantity);
            });
        } else if (selectedProductIdFromQuery) {
            addRow(selectedProductIdFromQuery, 1);
        } else {
            addRow();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFormRows);
    } else {
        initFormRows();
    }
</script>
@endsection
