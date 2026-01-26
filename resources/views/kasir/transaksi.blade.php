@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-5rem)] -m-4 md:-m-6 flex flex-col md:flex-row overflow-hidden bg-gray-100 font-sans" x-data="posSystem()">
    
    <!-- LEFT SECTION: PRODUCTS -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
        
        <!-- Header & Search -->
        <div class="px-6 py-5 bg-white/80 backdrop-blur-sm border-b border-gray-200 flex flex-col sm:flex-row gap-4 justify-between items-center z-10">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Katalog Produk</h1>
                <p class="text-sm text-gray-500">Pilih produk atau scan barcode</p>
            </div>
            
            <div class="relative w-full sm:w-96 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <!-- Search Input with Barcode Scanner Support -->
                <input type="text" 
                       x-model="search"
                       x-ref="searchInput"
                       @keydown.enter.prevent="handleScan()"
                       class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-2xl leading-5 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm"
                       placeholder="Cari nama / kode / scan..." 
                       autofocus>
                
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <kbd class="hidden sm:inline-block px-2 py-0.5 bg-white border border-gray-200 rounded-md text-xs text-gray-400 font-medium font-mono">/</kbd>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-6 scroll-smooth custom-scrollbar">
            
            <div x-show="filteredProducts.length === 0" class="h-full flex flex-col items-center justify-center text-center opacity-0 animate-fade-in-up" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ditemukan</h3>
                <p class="text-gray-500 max-w-xs mx-auto">Produk dengan kata kunci tersebut tidak tersedia di katalog.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 pb-20">
                <template x-for="product in filteredProducts" :key="product.id_barang">
                    <div @click="addToCart(product)" 
                         class="group bg-white rounded-3xl p-3 cursor-pointer shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_20px_25px_-5px_rgba(0,0,0,0.1),0_10px_10px_-5px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-indigo-100 relative overflow-hidden h-full flex flex-col">
                        
                        <!-- Image Container -->
                        <div class="aspect-[4/3] rounded-2xl bg-gray-100 overflow-hidden relative mb-3">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-300 bg-gray-50">
                                <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <img x-show="product.gambar" 
                                 :src="'/storage/' + product.gambar" 
                                 loading="lazy"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            
                            <!-- Badge Stock -->
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-[10px] font-bold text-white bg-black/60 backdrop-blur-md rounded-lg"
                                      :class="{'bg-red-500/80': product.stok <= 5}">
                                    <span x-text="product.stok"></span> Stok
                                </span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-800 text-sm leading-tight mb-1 group-hover:text-indigo-600 transition-colors line-clamp-2" x-text="product.nama_barang"></h3>
                            <p class="text-[10px] text-gray-400 font-mono mb-3" x-text="product.kode_barang"></p>
                            
                            <div class="mt-auto flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400">Harga</span>
                                    <span class="font-bold text-gray-900 text-base">
                                        Rp <span x-text="formatNumber(product.harga_jual)"></span>
                                    </span>
                                </div>
                                <button class="w-8 h-8 rounded-full bg-gray-50 text-gray-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- RIGHT SECTION: CART (Dark Theme) -->
    <div class="w-full md:w-[420px] bg-[#1a1c23] text-white flex flex-col h-full shadow-2xl z-20 transition-all duration-300">
        
        <!-- Cart Header -->
        <div class="p-6 bg-[#1a1c23] border-b border-gray-700/50 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-bold tracking-tight">Order Saat Ini</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <p class="text-xs text-gray-400 font-mono">TRX-{{ date('ymd') }}-{{ rand(100,999) }}</p>
                </div>
            </div>
            <button @click="clearCart()" 
                    x-show="cart.length > 0"
                    class="p-2 rounded-xl bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-gray-700 transition-colors"
                    title="Kosongkan Keranjang">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar relative">
            
            <div x-show="cart.length === 0" class="absolute inset-0 flex flex-col items-center justify-center opacity-30 pointer-events-none text-center p-6">
                <svg class="w-20 h-20 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <h3 class="text-lg font-medium text-gray-400">Keranjang Kosong</h3>
                <p class="text-sm text-gray-600 mt-1 max-w-[200px]">Scan produk atau pilih dari katalog untuk memulai transaksi.</p>
            </div>

            <template x-for="(item, index) in cart" :key="item.id_barang">
                <div class="bg-[#242731] p-4 rounded-2xl border border-gray-700/50 group hover:border-indigo-500/30 transition-all duration-300">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 pr-3">
                            <h4 class="font-medium text-sm text-gray-100 leading-snug line-clamp-2" x-text="item.nama_barang"></h4>
                            <p class="text-[10px] text-gray-500 font-mono mt-1" x-text="item.kode_barang"></p>
                        </div>
                        <div class="text-right">
                             <div class="font-bold text-white text-sm">Rp <span x-text="formatNumber(item.harga_jual * item.qty)"></span></div>
                             <div class="text-[10px] text-gray-500">@ <span x-text="formatNumber(item.harga_jual)"></span></div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between bg-[#1a1c23] rounded-xl p-1.5 w-max">
                         <button @click="updateQty(index, -1)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#242731] text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                         </button>
                         <input readonly :value="item.qty" class="w-10 text-center bg-transparent border-0 text-white font-bold text-sm p-0 focus:ring-0">
                         <button @click="updateQty(index, 1)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#242731] text-gray-400 hover:text-indigo-400 hover:bg-gray-700 transition-colors">
                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                         </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Checkout Area -->
        <div class="bg-[#1a1c23] border-t border-gray-800 p-6 z-20">
            <!-- Payment Details -->
            <div class="space-y-3 mb-6">
                <div class="flex justify-between text-gray-400 text-sm">
                    <span>Subtotal</span>
                    <span>Rp <span x-text="formatNumber(total)"></span></span>
                </div>
                <!-- Discount row can go here -->
                <div class="w-full h-px bg-gray-800 my-2"></div>
                <div class="flex justify-between items-end">
                    <span class="text-base font-semibold text-white">Total Tagihan</span>
                    <span class="text-3xl font-bold text-indigo-400 tracking-tight">
                        <span class="text-sm font-normal text-gray-500 mr-1">Rp</span><span x-text="formatNumber(total)"></span>
                    </span>
                </div>
            </div>

            <!-- Pay Input -->
            <div class="mb-4 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="text-indigo-400 font-bold">Rp</span>
                </div>
                <input type="number" 
                       x-model.number="payAmount" 
                       class="block w-full pl-12 pr-4 py-4 bg-[#242731] border border-gray-700/50 rounded-2xl text-white font-bold text-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-600 shadow-inner"
                       placeholder="Masukkan Jumlah Bayar">
            </div>

            <!-- Change & Button -->
             <div class="flex items-center justify-between mb-4 px-1">
                 <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Kembalian</span>
                 <span class="text-xl font-bold font-mono" :class="change >= 0 ? 'text-green-400' : 'text-red-400'">
                     Rp <span x-text="formatNumber(Math.abs(change))"></span>
                 </span>
             </div>

            <button @click="processCheckout()"
                    :disabled="cart.length === 0 || payAmount < total || isLoading"
                    class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-bold text-lg shadow-lg hover:shadow-indigo-500/25 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform active:scale-95 flex items-center justify-center gap-3">
                <span x-show="!isLoading" class="flex items-center gap-2">
                    Proses Transaksi <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
                <span x-show="isLoading" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memproses...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
    function posSystem() {
        return {
            search: '',
            products: @json($products),
            cart: [],
            payAmount: 0,
            isLoading: false,
            customerName: 'Umum',
            paymentMethod: 'tunai',
            
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
                return this.payAmount - this.total;
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
                
                const exactMatch = this.products.find(p => 
                    p.kode_barang.toLowerCase() === this.search.toLowerCase()
                );
                
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
            
            clearCart() {
                if(confirm('Kosongkan keranjang?')) {
                    this.cart = [];
                    this.payAmount = 0;
                }
            },
            
            processCheckout() {
                if (this.cart.length === 0) return;
                
                if (this.payAmount < this.total) {
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
                        payment_method: this.paymentMethod,
                    })
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => Promise.reject(err));
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.notify('success', `Transaksi Berhasil! Kembalian: Rp ${this.formatNumber(data.kembalian)}`);
                        this.printReceipt(data);
                        
                        this.cart.forEach(cartItem => {
                            const product = this.products.find(p => p.id_barang === cartItem.id_barang);
                            if (product) product.stok -= cartItem.qty;
                        });

                        this.cart = [];
                        this.payAmount = 0;
                    } else {
                        this.notify('error', data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.notify('error', error.message || 'Gagal memproses transaksi.');
                })
                .finally(() => {
                    this.isLoading = false;
                });
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
                        <div class="center bold" style="font-size: 14px;">KOPERASI</div>
                        <div class="center">Jl. Pendidikan No. 123</div>
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
