@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-5rem)] -m-4 md:-m-6 flex flex-col md:flex-row overflow-hidden bg-white font-sans" x-data="posSystem()">
    
    <!-- LEFT SECTION: CATALOG -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative border-r border-gray-100">
        
        <!-- Header & Search (Simplified) -->
        <div class="px-6 py-4 border-b border-gray-100 flex gap-4 items-center bg-white z-20">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <!-- Search Input with Shortcut Hint -->
                <input type="text" 
                       x-model="search"
                       x-ref="searchInput"
                       @keydown.enter.prevent="handleScan()"
                       class="block w-full pl-11 pr-12 py-3 bg-gray-50 border-none rounded-xl text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all font-medium"
                       placeholder="Cari produk..." 
                       autofocus>
                
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="px-2 py-1 bg-white border border-gray-200 rounded-md text-[10px] text-gray-400 font-bold shadow-sm">/</span>
                </div>
            </div>

            <!-- Categories (Minimal Pills) -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar max-w-[50%]">
                <button @click="activeCategory = 'Semua'" 
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all"
                        :class="activeCategory === 'Semua' ? 'bg-gray-900 text-white shadow-md' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'">
                    Semua
                </button>

                <template x-for="cat in categories" :key="cat">
                    <button @click="activeCategory = cat" 
                            class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all"
                            :class="activeCategory === cat ? 'bg-gray-900 text-white shadow-md' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'">
                        <span x-text="cat"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-6 scroll-smooth custom-scrollbar bg-white">
            
            <div x-show="filteredProducts.length === 0" class="h-full flex flex-col items-center justify-center text-center opacity-0 animate-fade-in-up" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="text-gray-400 font-medium">Produk tidak ditemukan</p>
            </div>

            <!-- Smaller Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 pb-20">
                <template x-for="product in filteredProducts" :key="product.id_barang">
                    <div @click="product.stok > 0 ? addToCart(product) : null" 
                         :class="product.stok <= 0 ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer hover:shadow-lg hover:-translate-y-0.5'"
                         class="group bg-white rounded-xl p-2 border border-gray-100 transition-all duration-200 relative overflow-hidden flex flex-col hover:border-blue-100">
                        
                        <!-- Image Area -->
                        <div class="aspect-square rounded-lg bg-[#F8F6F2] overflow-hidden relative mb-2">
                             <div class="absolute inset-0 flex items-center justify-center text-gray-200">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <img x-show="product.gambar" 
                                 :src="'/storage/' + product.gambar" 
                                 loading="lazy"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

                            <!-- Out of Stock Overlay -->
                            <div x-show="product.stok <= 0" class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest border border-gray-400 px-2 py-1 rounded">Habis</span>
                            </div>

                            <!-- Blue Plus Button (Appears on Hover) -->
                            <div x-show="product.stok > 0" 
                                 class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-200">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-800 text-[13px] leading-tight mb-1 line-clamp-2" x-text="product.nama_barang"></h3>
                            
                            <div class="mt-auto flex items-center justify-between">
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider" x-text="product.kategori ? product.kategori.nama_kategori : 'Umum'"></span>
                                <span class="font-bold text-gray-900 text-sm">
                                    <span class="text-[10px] font-normal text-gray-400 mr-0.5">Rp</span><span x-text="formatNumber(product.harga_jual)"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- RIGHT SECTION: CART (Clean White) -->
    <div class="w-full md:w-[380px] bg-white flex flex-col h-full border-l border-gray-100 shadow-[0_0_40px_rgba(0,0,0,0.03)] z-30">
        
        <!-- Cart Header -->
        <div class="p-5 border-b border-gray-50 flex items-center justify-between bg-white">
            <h2 class="text-lg font-bold text-gray-800">Keranjang</h2>
            <button @click="clearCart()" 
                    x-show="cart.length > 0"
                    class="text-xs font-bold text-red-500 hover:text-red-600 transition-colors uppercase tracking-wide">
                Hapus Semua
            </button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto px-5 py-2 custom-scrollbar">
            <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-center opacity-40">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-3">
                     <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <p class="text-xs font-bold text-gray-400">Belum ada barang</p>
            </div>

            <template x-for="(item, index) in cart" :key="item.id_barang">
                <div class="flex gap-3 mb-4 group animate-fade-in-up">
                    <!-- Thumbnail -->
                    <div class="w-12 h-12 rounded-lg bg-gray-50 overflow-hidden shrink-0 border border-gray-100">
                         <img x-show="item.gambar" :src="'/storage/' + item.gambar" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-0.5">
                            <h4 class="font-bold text-gray-800 text-xs line-clamp-1" x-text="item.nama_barang"></h4>
                            <span class="font-bold text-gray-900 text-xs"><span class="text-[9px] text-gray-400 font-normal">Rp</span> <span x-text="formatNumber(item.harga_jual * item.qty)"></span></span>
                        </div>
                        
                        <div class="flex items-center justify-between mt-1">
                             <div class="text-[10px] text-gray-400">@ <span x-text="formatNumber(item.harga_jual)"></span></div>
                             
                             <!-- Qty Control -->
                             <div class="flex items-center bg-gray-50 rounded-lg p-0.5 border border-gray-100">
                                <button @click="item.qty > 1 ? updateQty(index, -1) : removeItem(index)" class="w-5 h-5 flex items-center justify-center rounded-md bg-white text-gray-400 hover:text-red-500 shadow-sm transition-colors">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <span class="text-xs font-bold text-gray-800 w-6 text-center" x-text="item.qty"></span>
                                <button @click="updateQty(index, 1)" class="w-5 h-5 flex items-center justify-center rounded-md bg-white text-gray-400 hover:text-blue-500 shadow-sm transition-colors">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="p-5 bg-white border-t border-gray-50">
            <div class="flex justify-between items-end mb-4">
                <span class="text-sm font-medium text-gray-500">Total Tagihan</span>
                <span class="text-2xl font-black text-gray-900 tracking-tight">
                    <span class="text-sm font-bold text-gray-400 align-top mt-1 inline-block mr-0.5">Rp</span><span x-text="formatNumber(total)"></span>
                </span>
            </div>

            <!-- Payment Input -->
            <div class="mb-4 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="text-gray-400 font-bold text-sm">Rp</span>
                </div>
                <input type="number" 
                       x-model.number="payAmount" 
                       class="block w-full pl-10 pr-4 py-3 bg-gray-50 border-none rounded-xl text-gray-900 font-bold text-lg focus:ring-2 focus:ring-blue-500/20 transition-all font-mono placeholder-gray-300"
                       placeholder="0">
                
                <div x-show="payAmount > 0" class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <span class="text-[10px] font-bold uppercase tracking-wider" :class="change >= 0 ? 'text-green-500' : 'text-red-400'"
                          x-text="change >= 0 ? 'Kembali' : 'Kurang'"></span>
                </div>
            </div>

            <div x-show="payAmount > 0 && change >= 0" class="flex justify-between items-center mb-4 px-1 animate-fade-in-up">
                 <span class="text-xs font-bold text-gray-400">Kembalian</span>
                 <span class="text-base font-bold text-green-600 font-mono">Rp <span x-text="formatNumber(change)"></span></span>
            </div>

            <button @click="processCheckout()"
                    :disabled="cart.length === 0 || payAmount < total || isLoading"
                    class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-200 disabled:opacity-50 disabled:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2">
                <span x-show="!isLoading">Bayar Sekarang</span>
                <svg x-show="!isLoading" class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                <span x-show="isLoading" class="animate-spin border-2 border-white/20 border-t-white rounded-full w-4 h-4"></span>
            </button>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div x-show="showSuccessModal" 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 transform transition-all"
             @click.away="closeSuccessModal()">
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-800">Transaksi Sukses!</h2>
                <p class="text-gray-400 text-xs mt-1">Struk pembayaran siap dicetak</p>
            </div>
            
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-5 max-h-[300px] overflow-y-auto">
                 <div x-html="receiptPreviewHtml" class="font-mono text-[10px] leading-relaxed receipt-content opacity-70"></div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button @click="printReceiptFromModal()" class="py-2.5 bg-gray-900 text-white rounded-xl font-bold text-xs hover:bg-black transition-colors shadow-lg shadow-gray-200">
                    Cetak Struk
                </button>
                <button @click="closeSuccessModal()" class="py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function posSystem() {
        return {
            search: '',
            activeCategory: 'Semua',
            products: @json($products),
            cart: [],
            payAmount: '',
            isLoading: false,
            showSuccessModal: false,
            receiptPreviewHtml: '',
            lastReceiptData: null,
            
            init() {
                // Focus shortcut
                window.addEventListener('keydown', (e) => {
                    if (e.key === '/' && document.activeElement !== this.$refs.searchInput) {
                        e.preventDefault();
                        this.$refs.searchInput.focus();
                    }
                });
            },

            get categories() {
                const cats = this.products
                    .map(p => p.kategori ? p.kategori.nama_kategori : 'Lainnya')
                    .filter((v, i, a) => a.indexOf(v) === i && v !== 'Lainnya'); 
                return cats;
            },

            get filteredProducts() {
                let items = this.products;
                if (this.activeCategory !== 'Semua') {
                    items = items.filter(p => p.kategori && p.kategori.nama_kategori === this.activeCategory);
                }
                if (this.search) {
                    const lowerSearch = this.search.toLowerCase();
                    items = items.filter(p => 
                        p.nama_barang.toLowerCase().includes(lowerSearch) || 
                        p.kode_barang.toLowerCase().includes(lowerSearch)
                    );
                }
                return items;
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
            
            addToCart(product) {
                const existingItem = this.cart.find(item => item.id_barang === product.id_barang);
                if (existingItem) {
                    if (existingItem.qty >= product.stok) {
                        this.notify('error', 'Stok tidak mencukupi');
                        return;
                    }
                    existingItem.qty++;
                } else {
                    if (product.stok <= 0) return;
                    this.cart.push({ ...product, qty: 1 });
                }
            },
            
            updateQty(index, change) {
                const item = this.cart[index];
                const product = this.products.find(p => p.id_barang === item.id_barang);
                const newQty = item.qty + change;
                
                if (newQty <= 0) {
                    this.removeItem(index);
                    return;
                }
                if (newQty > product.stok) {
                    this.notify('error', 'Stok maksimal');
                    return;
                }
                item.qty = newQty;
            },

            removeItem(index) {
                this.cart.splice(index, 1);
            },
            
            clearCart() {
                if(confirm('Kosongkan keranjang?')) {
                    this.cart = [];
                    this.payAmount = '';
                }
            },
            
            handleScan() {
                if (!this.search) return;
                const exactMatch = this.products.find(p => p.kode_barang.toLowerCase() === this.search.toLowerCase());
                if(exactMatch) {
                    this.addToCart(exactMatch);
                    this.search = ''; 
                }
            },
            
            processCheckout() {
                 if (this.cart.length === 0) return;
                
                if ((this.payAmount || 0) < this.total) {
                    this.notify('error', 'Pembayaran kurang');
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
                        customer_name: 'Umum', // Default since input removed
                        payment_method: 'tunai', 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.lastReceiptData = data;
                        this.generateReceiptPreview(data);
                        this.showSuccessModal = true;
                        
                        this.cart.forEach(cartItem => {
                            const product = this.products.find(p => p.id_barang === cartItem.id_barang);
                            if (product) product.stok -= cartItem.qty;
                        });

                        this.cart = [];
                        this.payAmount = '';
                    } else {
                        this.notify('error', 'Gagal memproses');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.notify('error', 'Terjadi kesalahan sistem');
                })
                .finally(() => this.isLoading = false);
            },

            notify(type, message) {
                 window.dispatchEvent(new CustomEvent('notify', { detail: { type, message } }));
            },

            generateReceiptPreview(data) {
                this.receiptPreviewHtml = `
                    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 4px;">KOPERASI KITA</div>
                    <hr style="margin: 8px 0; border: 0; border-top: 1px dashed #ddd;">
                    <table style="width: 100%; border-collapse: collapse;">
                        ${data.items.map(item => `
                            <tr><td colspan="2" style="font-weight: bold;">${item.nama_barang}</td></tr>
                            <tr>
                                <td>${item.qty} x ${this.formatNumber(item.harga)}</td>
                                <td style="text-align: right;">${this.formatNumber(item.subtotal)}</td>
                            </tr>
                        `).join('')}
                    </table>
                    <hr style="margin: 8px 0; border: 0; border-top: 1px dashed #ddd;">
                    <div style="display:flex; justify-content:space-between; font-weight:bold;"><span>Total</span><span>Rp ${this.formatNumber(data.total)}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span>Bayar</span><span>Rp ${this.formatNumber(data.bayar)}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span>Kembali</span><span>Rp ${this.formatNumber(data.kembalian)}</span></div>
                `;
            },
            
            printReceiptFromModal() {
                 if (this.lastReceiptData) this.printReceipt(this.lastReceiptData);
            },

            printReceipt(data) {
                 const receiptHtml = `
                    <html>
                    <head>
                        <title>Struk Belanja</title>
                        <style>
                            @page { margin: 0; size: 58mm auto; }
                            body { font-family: 'Courier New', monospace; font-size: 10; margin: 0; padding: 5px; width: 58mm; }
                            .center { text-align: center; }
                            .right { text-align: right; }
                            .bold { font-weight: bold; }
                            .divider { border-top: 1px dashed #000; margin: 5px 0; }
                            table { width: 100%; }
                            td { vertical-align: top; }
                        </style>
                    </head>
                    <body>
                        <div class="center bold">KOPERASI KITA</div>
                        <div class="center" style="font-size: 9px;">${data.tanggal}</div>
                        <div class="divider"></div>
                        <table>
                            ${data.items.map(item => `
                                <tr><td colspan="2" class="bold">${item.nama_barang}</td></tr>
                                <tr>
                                    <td>${item.qty} x ${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
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
            },
            
            closeSuccessModal() {
                this.showSuccessModal = false;
            }
        }
    }
</script>
@endsection
