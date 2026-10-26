@extends('layouts.app', ['title' => $user->isCustomer() ? "Buat Pesanan Boneka - Diza's Plushie" : "Form Penjualan Kasir - Diza's Plushie"])

@section('content')
<div class="max-w-5xl mx-auto py-4 space-y-6">
    <div class="mb-4">
        <a href="{{ route('sales.index') }}" class="text-xs font-black text-slate-500 hover:text-rose-500 inline-flex items-center transition-colors">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Transaksi
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-pink-100 shadow-xl shadow-pink-100/30">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-pink-100 pb-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center">
                    <span class="mr-2">🌸</span>
                    {{ $user->isCustomer() ? "Pemesanan Boneka Cantik Diza's Plushie" : "Transaksi Penjualan Kasir (Stock-Out)" }}
                </h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    {{ $user->isCustomer() ? 'Pilih koleksi boneka kesayanganmu. Stok fisik akan terpotong secara otomatis setelah transaksi tersimpan.' : 'Catat transaksi penjualan kasir toko boneka dengan kalkulasi server-side dan validasi atomik.' }}
                </p>
            </div>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-black bg-pink-50 text-rose-600 border border-pink-200">
                <i class="fa-solid fa-boxes-packing mr-1.5"></i> Stock-Out Otomatis
            </span>
        </div>

        <form action="{{ route('sales.store') }}" method="POST" id="saleForm" class="space-y-6">
            @csrf

            <!-- Customer & Transaction Information -->
            <div class="bg-pink-50/40 p-5 rounded-3xl border border-pink-100 space-y-4">
                @if($user->isCustomer())
                    <!-- Fixed Customer Info for Customer Role -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-pink-100 shadow-sm">
                        <div class="flex items-center space-x-3">
                            <span class="w-12 h-12 rounded-2xl bg-pink-100 text-rose-500 flex items-center justify-center text-xl shadow-sm">
                                <i class="fa-solid fa-heart"></i>
                            </span>
                            <div>
                                <span class="text-[10px] uppercase font-black text-rose-400 block tracking-wider">Pemesan (Customer)</span>
                                <h3 class="text-base font-black text-slate-800">{{ $user->name }}</h3>
                                <p class="text-xs text-slate-400">{{ $user->email }} &bull; {{ $user->phone ?: 'No. telp belum diisi' }}</p>
                            </div>
                        </div>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-pink-100 text-rose-700 border border-pink-200">
                            💕 Pesanan Pelanggan Cantik
                        </span>
                    </div>
                @else
                    <!-- Admin / Staff Customer Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-6">
                            <label for="customer_id" class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                Pelanggan Terdaftar (Member)
                            </label>
                            <select name="customer_id" id="customer_id"
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-pink-200 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 bg-white font-medium">
                                <option value="">-- Bukan Member / Pembeli Umum --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }} ({{ $cust->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Pilih jika pembeli memiliki akun member customer.</p>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="customer_name" class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Pembeli Umum (Guest)
                            </label>
                            <input type="text" name="customer_name" id="customer_name"
                                value="{{ old('customer_name') }}"
                                placeholder="Contoh: Kak Diza / Pelanggan Walk-In"
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-pink-200 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 bg-white">
                            <p class="text-[11px] text-slate-400 mt-1">Diisi bila pembeli adalah pembeli umum tanpa akun member.</p>
                        </div>
                    </div>
                @endif

                <!-- Payment Method Selection -->
                <div class="bg-white p-5 rounded-2xl border border-pink-100 shadow-sm space-y-3">
                    <div class="flex items-center space-x-2">
                        <span class="w-8 h-8 rounded-xl bg-pink-100 text-rose-500 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-credit-card"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-slate-800">Pilih Metode Pembayaran</h3>
                            <p class="text-xs text-slate-400">Pilih saluran pembayaran resmi Diza's Plushie</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                        <!-- QRIS -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="QRIS" class="peer sr-only" {{ old('payment_method', 'QRIS') === 'QRIS' ? 'checked' : '' }}>
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-rose-400 peer-checked:bg-rose-50/70 transition-all text-center flex flex-col items-center space-y-1.5 hover:border-pink-300">
                                <i class="fa-solid fa-qrcode text-xl text-rose-500"></i>
                                <span class="text-xs font-black text-slate-800 block">QRIS Instant</span>
                                <span class="text-[10px] text-slate-400 block font-medium">BCA, GoPay, Shopee</span>
                            </div>
                        </label>

                        <!-- Transfer Bank -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Transfer Bank" class="peer sr-only" {{ old('payment_method') === 'Transfer Bank' ? 'checked' : '' }}>
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-blue-400 peer-checked:bg-blue-50/70 transition-all text-center flex flex-col items-center space-y-1.5 hover:border-blue-300">
                                <i class="fa-solid fa-building-columns text-xl text-blue-500"></i>
                                <span class="text-xs font-black text-slate-800 block">Transfer Bank</span>
                                <span class="text-[10px] text-slate-400 block font-medium">BCA / Mandiri / BSI</span>
                            </div>
                        </label>

                        <!-- E-Wallet -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="E-Wallet" class="peer sr-only" {{ old('payment_method') === 'E-Wallet' ? 'checked' : '' }}>
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-purple-400 peer-checked:bg-purple-50/70 transition-all text-center flex flex-col items-center space-y-1.5 hover:border-purple-300">
                                <i class="fa-solid fa-wallet text-xl text-purple-500"></i>
                                <span class="text-xs font-black text-slate-800 block">E-Wallet</span>
                                <span class="text-[10px] text-slate-400 block font-medium">OVO, DANA, GoPay</span>
                            </div>
                        </label>

                        <!-- Tunai / COD -->
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Tunai / COD" class="peer sr-only" {{ old('payment_method') === 'Tunai / COD' ? 'checked' : '' }}>
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-emerald-400 peer-checked:bg-emerald-50/70 transition-all text-center flex flex-col items-center space-y-1.5 hover:border-emerald-300">
                                <i class="fa-solid fa-money-bill-wave text-xl text-emerald-500"></i>
                                <span class="text-xs font-black text-slate-800 block">Tunai / COD</span>
                                <span class="text-[10px] text-slate-400 block font-medium">Bayar di Tempat</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Transaksi (Opsional)
                    </label>
                    <input type="text" name="notes" id="notes"
                        value="{{ old('notes') }}"
                        placeholder="Contoh: Kado ulang tahun, dibungkus pita kado pink, atau ucapan selamat..."
                        class="w-full px-4 py-2.5 rounded-2xl border border-pink-200 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 bg-white">
                </div>
            </div>

            <!-- Items Table Section -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900 flex items-center">
                            <i class="fa-solid fa-basket-shopping text-rose-500 mr-2"></i> Keranjang Boneka yang Dibeli (Multi-Item)
                        </h2>
                        <p class="text-xs text-slate-500 font-medium">Pilih boneka dan masukkan kuantitas. Sistem akan memvalidasi ketersediaan stok fisik secara ketat.</p>
                    </div>

                    <button type="button" id="addRowBtn" class="inline-flex items-center px-4 py-2 rounded-2xl bg-pink-100 hover:bg-pink-200 text-rose-700 border border-pink-300 text-xs font-black transition-colors shadow-sm">
                        <i class="fa-solid fa-plus mr-1.5"></i> Tambah Baris Boneka
                    </button>
                </div>

                @error('items')
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 font-bold flex items-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <!-- Dynamic Items Table -->
                <div class="bg-white rounded-3xl border border-pink-100 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm text-slate-600" id="itemsTable">
                            <thead class="bg-pink-50/60 text-[11px] uppercase font-black text-slate-600 border-b border-pink-100">
                                <tr>
                                    <th class="px-4 py-3 w-6/12 sm:w-5/12">Pilih Boneka</th>
                                    <th class="px-3 py-3 w-2/12 text-center">Stok Gudang</th>
                                    <th class="px-3 py-3 w-2/12 text-center">Jumlah Beli</th>
                                    <th class="px-3 py-3 w-2/12 text-right">Harga Jual</th>
                                    <th class="px-3 py-3 w-2/12 text-right">Subtotal</th>
                                    <th class="px-2 py-3 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTbody" class="divide-y divide-pink-50">
                                <!-- Dynamic rows inserted by JavaScript -->
                            </tbody>
                            <tfoot class="bg-pink-50/50 font-black text-slate-900 border-t border-pink-100">
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-right uppercase tracking-wider text-xs font-black">
                                        Total Tagihan Belanja:
                                    </td>
                                    <td class="px-3 py-4 text-right text-lg text-rose-600 font-black" id="grandTotalDisplay">
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
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-pink-100">
                <a href="{{ route('sales.index') }}" class="px-5 py-2.5 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-black text-sm shadow-md shadow-pink-300/30 transition-all flex items-center">
                    <i class="fa-solid fa-check mr-2"></i> Konfirmasi & Simpan Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Multi-Item Row Logic with Plushie Thumbnails -->
<script>
    const availableProducts = @json($products);
    const defaultPlushieImg = "{{ asset('images/plushies/teddy-bear-pita.jpg') }}";
    let rowIndex = 0;

    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function addRow(selectedProductId = '', quantity = 1) {
        const tr = document.createElement('tr');
        tr.className = 'item-row hover:bg-pink-50/20 transition-colors';

        let optionsHtml = '<option value="">-- Pilih Koleksi Boneka --</option>';
        availableProducts.forEach(p => {
            const isSelected = String(p.id) === String(selectedProductId) ? 'selected' : '';
            const imgPath = p.image_asset_url || (p.image_url ? ("{{ asset('') }}" + p.image_url.replace(/^\//, '')) : defaultPlushieImg);
            optionsHtml += `<option value="${p.id}" data-price="${p.selling_price}" data-stock="${p.stock}" data-image="${imgPath}" data-img="${imgPath}" ${isSelected}>
                ${p.name} (${p.size || 'Standard'}) - Sisa: ${p.stock} pcs
            </option>`;
        });

        tr.innerHTML = `
            <td class="px-4 py-3">
                <div class="flex items-center space-x-2.5">
                    <img src="${defaultPlushieImg}" onerror="this.onerror=null; this.src='${defaultPlushieImg}';" class="row-thumb w-12 h-12 rounded-xl object-cover object-center bg-pink-50 border border-pink-100 shrink-0 shadow-xs" alt="Thumb">
                    <div class="flex-1">
                        <select name="items[${rowIndex}][product_id]" required
                            class="product-select w-full px-3 py-2 rounded-xl border border-pink-200 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-rose-400 focus:border-rose-400 bg-white">
                            ${optionsHtml}
                        </select>
                    </div>
                </div>
            </td>
            <td class="px-3 py-3 text-center font-bold text-slate-600 stock-cell">
                -
            </td>
            <td class="px-3 py-3 text-center">
                <input type="number" name="items[${rowIndex}][quantity]" value="${quantity}" min="1" required
                    class="qty-input w-24 text-center px-2 py-1.5 rounded-xl border border-pink-200 text-xs sm:text-sm font-black focus:ring-2 focus:ring-rose-400 focus:border-rose-400">
                <span class="stock-warning text-[10px] text-rose-600 font-bold block mt-0.5 hidden">Stok Kurang!</span>
            </td>
            <td class="px-3 py-3 text-right font-bold text-slate-700 price-display">
                Rp 0
            </td>
            <td class="px-3 py-3 text-right font-black text-rose-600 subtotal-display">
                Rp 0
            </td>
            <td class="px-2 py-3 text-center">
                <button type="button" class="remove-row-btn p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Baris">
                    <i class="fa-solid fa-trash-can text-sm"></i>
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
                const img = selectedOpt.getAttribute('data-image') || selectedOpt.getAttribute('data-img') || defaultPlushieImg;
                tr.querySelector('.stock-cell').textContent = `${stock} pcs`;
                tr.querySelector('.price-display').textContent = formatRupiah(price);
                thumb.src = img;
                qtyInput.max = stock;
            } else {
                tr.querySelector('.stock-cell').textContent = '-';
                tr.querySelector('.price-display').textContent = 'Rp 0';
                thumb.src = defaultPlushieImg;
            }
            validateAndCalculateRow(tr);
            calculateGrandTotal();
        }

        select.addEventListener('change', updateRowFromSelection);
        qtyInput.addEventListener('input', () => validateAndCalculateRow(tr));

        removeBtn.addEventListener('click', () => {
            const allRows = document.querySelectorAll('.item-row');
            if (allRows.length <= 1) {
                alert('Minimal harus ada 1 baris boneka dalam pesanan.');
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
            tr.querySelector('.qty-input').classList.add('border-rose-500', 'bg-rose-50');
        } else {
            warning.classList.add('hidden');
            tr.querySelector('.qty-input').classList.remove('border-rose-500', 'bg-rose-50');
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

    // Restore old inputs or initialize with preselected product from query
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
            // Initialize with 1 empty row
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
