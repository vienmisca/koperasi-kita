@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-5rem)] -m-4 md:-m-6 flex flex-col md:flex-row overflow-hidden bg-gray-50 font-sans" x-data="posSystem()">
    
    <!-- LEFT SECTION: PRODUCTS (Catalog) -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative border-r border-gray-200">
        
        <!-- Header & Search -->
        <div class="px-6 py-5 bg-white border-b border-gray-100 flex flex-col sm:flex-row gap-4 justify-between items-center z-10">
            <div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">Katalog Produk</h1>
                <p class="text-xs text-gray-500">Pilih item untuk ditambahkan</p>
            </div>
            
            <div class="relative w-full sm:w-80 group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <!-- Search Input -->
                <input type="text" 
                       x-model="search"
                       x-ref="searchInput"
                       @keydown.enter.prevent="handleScan()"
                       class="block w-full pl-10 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/50 transition-all"
                       placeholder="Cari produk / scan..." 
                       autofocus>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <kbd class="hidden sm:inline-block px-2 py-0.5 text-[10px] font-bold text-gray-400 bg-white border border-gray-200 rounded-lg shadow-sm">/</kbd>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-6 scroll-smooth custom-scrollbar bg-[#f8f9fa]">
            
            <!-- Empty State -->
            <div x-show="filteredProducts.length === 0" class="h-full flex flex-col items-center justify-center opacity-60">
                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-600">Tidak ada produk ditemukan</h3>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4 pb-20">
                <template x-for="product in filteredProducts" :key="product.id_barang">
                    <button @click="product.stok > 0 ? addToCart(product) : null" 
                            :class="product.stok <= 0 ? 'grayscale opacity-80 cursor-not-allowed' : 'hover:shadow-lg hover:border-blue-100 hover:-translate-y-1'"
                            class="group bg-white rounded-2xl p-3 shadow-sm border border-gray-100 text-left flex flex-col h-full transition-all duration-200 relative overflow-hidden">
                        
                        <!-- Image Area -->
                        <div class="aspect-[4/3] w-full rounded-xl bg-gray-50 overflow-hidden relative mb-3">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-300">
                                <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <img x-show="product.gambar" 
                                 :src="'/storage/' + product.gambar" 
                                 loading="lazy"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            
                            <!-- Out of Stock Overlay -->
                            <div x-show="product.stok <= 0" class="absolute inset-0 bg-black/50 flex items-center justify-center z-10">
                                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg transform rotate-[-10deg] border-2 border-white">HABIS</span>
                            </div>
                             
                             <div class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[10px] font-bold bg-white/90 shadow-sm border border-gray-100"
                                  :class="product.stok <= 5 ? 'text-red-500' : 'text-gray-600'">
                                 <span x-text="product.stok"></span> Left
                             </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-800 text-sm leading-snug mb-1 line-clamp-2" x-text="product.nama_barang"></h3>
                            <p class="text-[10px] text-gray-400 font-mono mb-2" x-text="product.kode_barang"></p>
                            
                            <div class="mt-auto pt-2 border-t border-gray-50 flex items-center justify-between">
                                <span class="font-bold text-gray-900 text-base">
                                    <span class="text-[10px] text-gray-400 align-top">Rp</span><span x-text="formatNumber(product.harga_jual)"></span>
                                </span>
                                <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- RIGHT SECTION: CHECKOUT UI (Matched to Reference Image) -->
    <div class="w-full md:w-[400px] bg-white flex flex-col h-full relative shadow-xl z-20">
        
        <!-- 1. Header removed -->
        <!-- 1. Header -->
        <div class="px-6 pt-6 pb-2 border-b border-gray-100/50">
            <div class="flex justify-between items-center mb-1">
                <h2 class="text-lg font-bold text-gray-800">Transaksi Baru</h2>
                <span class="text-xs font-mono text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">#TRX-{{ date('ymd') }}-...</span>
            </div>
            <p class="text-[10px] text-gray-400">Kasir: {{ Auth::user()->name }}</p>
        </div>

        <!-- 3. List Item (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-6 py-2 custom-scrollbar relative">
            <div x-show="cart.length === 0" class="absolute inset-0 flex flex-col items-center justify-center">
                <p class="text-sm text-gray-300 font-medium">No Item Selected</p>
            </div>

            <template x-for="(item, index) in cart" :key="item.id_barang">
                <div class="flex justify-between items-start mb-4 group animate-fade-in-up">
                    <div class="flex-1 pr-4">
                        <div class="flex items-center gap-2 mb-1">
                             <h4 class="text-sm font-bold text-gray-800" x-text="item.nama_barang"></h4>
                        </div>
                        <div class="flex items-center gap-3">
                             <div class="flex items-center bg-gray-50 rounded-lg p-0.5 border border-gray-100">
                                 <button @click="updateQty(index, -1)" class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-red-500">-</button>
                                 <span class="text-xs font-bold text-gray-700 w-6 text-center" x-text="item.qty"></span>
                                 <button @click="updateQty(index, 1)" class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-blue-500">+</button>
                             </div>
                             <span class="text-[10px] text-gray-400">@ <span x-text="formatNumber(item.harga_jual)"></span></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-gray-800">
                            Rp <span x-text="formatNumber(item.harga_jual * item.qty)"></span>
                        </span>
                    </div>
                </div>
            </template>
        </div>

        <!-- 4. Divider -->
        <div class="relative h-4 w-full overflow-hidden mt-2">
            <div class="absolute top-1/2 w-full border-t-2 border-dashed border-gray-200"></div>
            <!-- Half Circles (optional for receipt look) -->
            <div class="absolute -left-2 top-0 w-4 h-4 bg-gray-50 rounded-full"></div>
            <div class="absolute -right-2 top-0 w-4 h-4 bg-gray-50 rounded-full"></div>
        </div>

        <!-- 5. Totals Info -->
        <div class="px-6 pt-2 pb-4 space-y-2 bg-white">
            <div class="flex justify-between text-xs text-gray-500 font-medium">
                <span>Subtotal</span>
                <span>Rp <span x-text="formatNumber(total)"></span></span>
            </div>

            <div class="border-b border-dashed border-gray-200 my-2 opacity-50"></div>
            
            <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-extrabold text-gray-800 uppercase tracking-wide">Total</span>
                <span class="text-xl font-extrabold text-gray-900">Rp <span x-text="formatNumber(total)"></span></span>
            </div>
            
            <!-- Kembalian / Kekurangan -->
            <div x-show="payAmount > 0 || payAmount === 0" class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg"
                 :class="payAmount < total ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-700'">
                <span class="text-xs font-bold" x-text="payAmount < total ? 'Kekurangan' : 'Kembalian'"></span>
                <span class="text-sm font-bold">
                    Rp <span x-text="formatNumber(Math.abs(payAmount - total))"></span>
                </span>
            </div>
        </div>

        <!-- 6. Input & Actions -->
        <div class="px-6 pb-6 bg-white space-y-3">
            <div>
                 <div class="relative w-full">
                     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                         <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                     </div>
                     <input type="number" 
                            x-model.number="payAmount"
                            min="0"
                            max="100000000"
                            @input="if($el.value < 0) $el.value = 0; if($el.value > 100000000) $el.value = 100000000;"
                            class="w-full bg-gray-50 border-none rounded-xl py-3 pl-10 pr-3 text-sm font-semibold placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:bg-white transition-all"
                            placeholder="Jumlah Bayar (Rp)">
                </div>
            </div>
            
            <button @click="processCheckout()"
                    :disabled="cart.length === 0 || payAmount < total || isLoading"
                    class="w-full py-4 bg-[#3b82f6] hover:bg-blue-600 text-white rounded-xl font-bold text-base shadow-lg shadow-blue-200 disabled:opacity-50 disabled:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2">
                <span x-show="!isLoading">Place Order</span>
                <span x-show="isLoading" class="animate-spin">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
        </div>
    </div>
    <!-- Success Modal -->
    <div x-show="showSuccessModal" 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-100"
             @click.away="closeSuccessModal()">
            
            <!-- Modal Content -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-[bounce_1s_infinite]">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Transaksi Berhasil!</h2>
                <p class="text-gray-500 text-sm mt-1">Struk pembayaran telah diterbitkan</p>
            </div>

            <!-- Receipt Preview -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 max-h-[300px] overflow-y-auto shadow-inner">
                 <div x-html="receiptPreviewHtml" class="font-mono text-[10px] leading-relaxed receipt-content"></div>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-2 gap-3">
                <button @click="printReceiptFromModal()" class="flex items-center justify-center gap-2 px-4 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-xl font-medium transition-all active:scale-95 shadow-lg shadow-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak
                </button>
                <button @click="closeSuccessModal()" class="px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition-all active:scale-95 shadow-lg shadow-blue-200">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function posSystem() {
        return {
            search: '',
            products: @json($products),
            cart: [],
            payAmount: '',
            customerName: 'Guest Customer',
            isLoading: false,
            showSuccessModal: false,
            receiptPreviewHtml: '',
            lastReceiptData: null,

            init() {
                window.addEventListener('keydown', (e) => {
                    if (e.key === '/') {
                        e.preventDefault();
                        this.$refs.searchInput.focus();
                    }
                });
            },
            
            get filteredProducts() {
                if (!this.search) return this.products;
                const lowerSearch = this.search.toLowerCase();
                return this.products.filter(p => 
                    p.nama_barang.toLowerCase().includes(lowerSearch) || 
                    p.kode_barang.toLowerCase().includes(lowerSearch)
                );
            },
            
            get total() {
                return this.cart.reduce((sum, item) => sum + (item.harga_jual * item.qty), 0);
            },

            get change() {
                return (this.payAmount || 0) - this.total;
            },
            
            
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            },
            
            notify(type, message) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { type, message } 
                }));
            },
            
            handleScan() {
                if (!this.search) return;
                const exactMatch = this.products.find(p => p.kode_barang.toLowerCase() === this.search.toLowerCase());
                if (exactMatch) {
                    this.addToCart(exactMatch);
                    this.search = '';
                    return;
                }
                if (this.filteredProducts.length === 1) {
                    this.addToCart(this.filteredProducts[0]);
                    this.search = '';
                    return;
                }
                this.playSound('error');
                this.notify('error', 'Produk tidak ditemukan!');
            },

            playSound(type = 'success') {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type === 'error' ? 'sawtooth' : 'sine';
                osc.frequency.setValueAtTime(type === 'error' ? 200 : 800, ctx.currentTime);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                gain.gain.exponentialRampToValueAtTime(0.00001, ctx.currentTime + 0.15);
                osc.stop(ctx.currentTime + 0.15);
            },
            
            addToCart(product) {
                const existingItem = this.cart.find(item => item.id_barang === product.id_barang);
                if (existingItem) {
                    if (existingItem.qty >= product.stok) {
                        this.playSound('error');
                        this.notify('error', 'Stok tidak mencukupi!');
                        return;
                    }
                    existingItem.qty++;
                } else {
                    if (product.stok <= 0) {
                         this.playSound('error');
                         this.notify('error', 'Stok Habis!');
                         return;
                    }
                    this.cart.push({ ...product, qty: 1 });
                }
                this.playSound('success');
            },
            
            updateQty(index, change) {
                const item = this.cart[index];
                const product = this.products.find(p => p.id_barang === item.id_barang);
                const newQty = item.qty + change;
                if (newQty <= 0) {
                    this.cart.splice(index, 1);
                    return;
                }
                if (newQty > product.stok) {
                    this.playSound('error');
                    this.notify('warning', 'Stok maksimal tercapai!');
                    return;
                }
                item.qty = newQty;
            },
            
            processCheckout() {
                if (this.cart.length === 0) return;
                
                // Validate payment
                if ((this.payAmount || 0) < this.total) {
                    this.notify('error', 'Pembayaran kurang!');
                    return;
                }

                this.isLoading = true;

                fetch('{{ route('transaksi.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        cart: this.cart.map(item => ({
                            id_barang: item.id_barang,
                            quantity: item.qty,
                            price: item.harga_jual
                        })),
                        bayar: this.payAmount,
                        customer_name: this.customerName,
                        payment_method: 'tunai', 
                        // You could expand this to use real values from the UI
                    })
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => Promise.reject(err));
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Notify success (optional, keeping it for sound)
                        // this.notify('success', `Transaksi Berhasil!`);
                        
                        // Prepare receipt data
                        this.lastReceiptData = data;
                        this.generateReceiptPreview(data);
                        this.showSuccessModal = true;
                        this.playSound('success');
                        
                        // Update local stock visual
                        this.cart.forEach(cartItem => {
                            const product = this.products.find(p => p.id_barang === cartItem.id_barang);
                            if (product) product.stok -= cartItem.qty;
                        });

                        this.cart = [];
                        this.payAmount = '';
                    } else {
                        this.notify('error', data.message || 'Terjadi kesalahan');
                        this.playSound('error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.notify('error', error.message || 'Gagal memproses transaksi.');
                    this.playSound('error');
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },

            generateReceiptPreview(data) {
                // Defines the structure for the modal preview (using simple HTML)
                this.receiptPreviewHtml = `
                    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 4px;">{{ \App\Models\Setting::getValue('toko_nama', 'KOPERASI KITA') }}</div>
                    <div style="text-align: center; margin-bottom: 8px;">{{ \App\Models\Setting::getValue('toko_alamat', '-') }}</div>
                    <div style="border-top: 1px dashed #ccc; margin: 8px 0;"></div>
                    <div style="display: flex; justify-content: space-between;"><span>No:</span> <span>${data.no_penjualan}</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Tgl:</span> <span>${data.tanggal}</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Kasir:</span> <span>${data.kasir}</span></div>
                    <div style="border-top: 1px dashed #ccc; margin: 8px 0;"></div>
                    <table style="width: 100%; border-collapse: collapse;">
                        ${data.items.map(item => `
                            <tr><td colspan="3" style="padding-top: 4px; font-weight: bold;">${item.nama_barang}</td></tr>
                            <tr>
                                <td style="width: 20%;">${item.qty}x</td>
                                <td style="width: 40%; text-align: right;">${this.formatNumber(item.harga)}</td>
                                <td style="width: 40%; text-align: right;">${this.formatNumber(item.subtotal)}</td>
                            </tr>
                        `).join('')}
                    </table>
                    <div style="border-top: 1px dashed #ccc; margin: 8px 0;"></div>
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; margin-top: 4px;">
                        <span>TOTAL</span>
                        <span>Rp ${this.formatNumber(data.total)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                        <span>Bayar</span>
                        <span>Rp ${this.formatNumber(data.bayar)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                        <span>Kembali</span>
                        <span>Rp ${this.formatNumber(data.kembalian)}</span>
                    </div>
                    <div style="border-top: 1px dashed #ccc; margin: 8px 0;"></div>
                    <div style="text-align: center; margin-top: 8px;">Terima Kasih</div>
                `;
            },

            closeSuccessModal() {
                this.showSuccessModal = false;
            },

            printReceiptFromModal() {
                 if (this.lastReceiptData) {
                    this.printReceipt(this.lastReceiptData);
                 }
            },

            printReceipt(data) {
                const receiptHtml = `
                    <html>
                    <head>
                        <title>Struk Belanja</title>
                        <style>
                            @page { margin: 0; size: 58mm auto; }
                            body { font-family: 'Courier New', monospace; font-size: 10px; margin: 0; padding: 10px; width: 58mm; box-sizing: border-box; }
                            .center { text-align: center; }
                            .right { text-align: right; }
                            .bold { font-weight: bold; }
                            .divider { border-top: 1px dashed #000; margin: 5px 0; }
                            table { width: 100%; border-collapse: collapse; }
                            td { vertical-align: top; }
                            .qt { width: 15%; } .pr { width: 55%; text-align: right; } .sb { width: 30%; text-align: right; }
                        </style>
                    </head>
                    <body>
                        <div class="center bold" style="font-size: 14px;">{{ \App\Models\Setting::getValue('toko_nama', 'KOPERASI KITA') }}</div>
                        <div class="center">{{ \App\Models\Setting::getValue('toko_alamat', '-') }}</div>
                        <div class="divider"></div>
                        <div>${data.no_penjualan}</div>
                        <div>${data.tanggal}</div>
                        <div>Kasir: ${data.kasir}</div>
                        <div class="divider"></div>
                        <table>
                            ${data.items.map(item => `
                                <tr><td colspan="3" class="bold">${item.nama_barang}</td></tr>
                                <tr>
                                    <td>${item.qty}x</td>
                                    <td class="right">${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
                                    <td class="right">${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                                </tr>
                            `).join('')}
                        </table>
                        <div class="divider"></div>
                        <table>
                            <tr><td class="bold">Total</td><td class="right bold">${new Intl.NumberFormat('id-ID').format(data.total)}</td></tr>
                            <tr><td>Bayar</td><td class="right">${new Intl.NumberFormat('id-ID').format(data.bayar)}</td></tr>
                            <tr><td>Kembali</td><td class="right">${new Intl.NumberFormat('id-ID').format(data.kembalian)}</td></tr>
                        </table>
                        <div class="divider"></div>
                        <div class="center">Terima Kasih</div>
                    </body>
                    </html>
                `;
                let iframe = document.getElementById('receipt-frame');
                if (!iframe) {
                    iframe = document.createElement('iframe');
                    iframe.id = 'receipt-frame';
                    iframe.style.display = 'none';
                    document.body.appendChild(iframe);
                }
                const doc = iframe.contentWindow.document;
                doc.open(); doc.write(receiptHtml); doc.close();
                iframe.onload = () => { iframe.contentWindow.focus(); iframe.contentWindow.print(); };
            }
        }
    }
</script>
@endsection
