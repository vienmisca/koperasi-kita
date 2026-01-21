@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-6rem)] flex flex-col md:flex-row gap-4" x-data="posSystem()">
    
    <!-- Left: Product List -->
    <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-full">
        <!-- Search Bar -->
        <div class="p-4 border-b bg-white z-10">
            <div class="relative">
                <input type="text" 
                       x-model="search"
                       class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                       placeholder="Cari produk (nama/kode)..."
                       autofocus>
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</div>
            </div>
        </div>
        
        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="product in filteredProducts" :key="product.id_barang">
                    <button @click="addToCart(product)" 
                            class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-300 transition-all text-left flex flex-col h-full group">
                        
                        <div class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center text-3xl group-hover:scale-105 transition-transform overflow-hidden relative">
                            <!-- Placeholder Image / Icon -->
                             <span x-show="!product.gambar">📦</span>
                             <img x-show="product.gambar" :src="'/storage/' + product.gambar" class="w-full h-full object-cover">
                             
                             <!-- Stock Badge -->
                             <div class="absolute top-1 right-1 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded backdrop-blur-sm">
                                Stok: <span x-text="product.stok"></span>
                             </div>
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 text-sm leading-tight mb-1 line-clamp-2" x-text="product.nama_barang"></h3>
                            <p class="text-xs text-gray-500 mb-2" x-text="product.kode_barang"></p>
                        </div>
                        
                        <div class="mt-auto font-bold text-blue-600">
                            Rp <span x-text="formatNumber(product.harga_jual)"></span>
                        </div>
                    </button>
                </template>
                
                <!-- No Results -->
                <div x-show="filteredProducts.length === 0" class="col-span-full py-12 text-center text-gray-500 flex flex-col items-center">
                    <span class="text-4xl mb-3 opacity-30">🔍</span>
                    <p>Produk tidak ditemukan.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right: Cart (Receipt Style) -->
    <div class="w-full md:w-96 flex flex-col h-full">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 h-full flex flex-col overflow-hidden relative" style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));">
            
            <!-- Receipt Top Decoration -->
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-400 to-indigo-500"></div>

            <!-- Receipt Header -->
            <div class="mt-2 p-5 border-b-2 border-dashed border-gray-200 text-center bg-gray-50">
                <h2 class="font-bold text-gray-800 text-lg uppercase tracking-wider">Koperasi Kita</h2>
                <p class="text-xs text-gray-500 mt-1">Jl. Pendidikan No. 123</p>
                 <div class="mt-3 flex justify-between text-xs text-gray-400 font-mono">
                     <span>{{ date('d/m/Y') }}</span>
                     <span>#TRX-{{ rand(1000,9999) }}</span>
                 </div>
            </div>
            
            <!-- Cart Items (Receipt Body) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#fffdfb]" style="background-image: radial-gradient(#f0f0f0 1px, transparent 1px); background-size: 20px 20px;">
                 <template x-for="(item, index) in cart" :key="item.id_barang">
                    <div class="flex gap-2 p-2 relative group hover:bg-gray-50 rounded transition-colors">
                        <!-- Remove Btn (Absolute) -->
                         <button @click="updateQty(index, -item.qty)" class="absolute -left-2 top-1 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity p-1">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                         </button>

                        <div class="flex-1 font-mono text-sm text-gray-700">
                             <div class="flex justify-between font-bold">
                                 <span x-text="item.nama_barang" class="truncate w-32"></span>
                                 <span>Rp <span x-text="formatNumber(item.harga_jual * item.qty)"></span></span>
                             </div>
                             <div class="flex justify-between text-xs text-gray-500 mt-1">
                                 <span class="flex items-center gap-2">
                                     <button @click="updateQty(index, -1)" class="w-5 h-5 bg-gray-200 rounded text-gray-600 hover:bg-gray-300">-</button>
                                     <span class="w-4 text-center" x-text="item.qty"></span>
                                     <button @click="updateQty(index, 1)" class="w-5 h-5 bg-gray-200 rounded text-gray-600 hover:bg-gray-300">+</button>
                                      x Rp <span x-text="formatNumber(item.harga_jual)"></span>
                                 </span>
                             </div>
                        </div>
                    </div>
                 </template>
                 
                 <!-- Empty Cart State -->
                 <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-gray-300">
                     <svg class="w-16 h-16 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                     <p class="text-sm font-mono opacity-60">Belum ada item</p>
                 </div>
            </div>
            
            <!-- Receipt Footer (Totals) -->
            <div class="bg-gray-50 p-5 border-t-2 border-dashed border-gray-200 relative">
                <!-- Zigzag Decoration Top -->
                <div class="absolute -top-1 left-0 w-full h-2 bg-transparent" 
                     style="background: linear-gradient(135deg, transparent 50%, #f9fafb 50%), linear-gradient(45deg, #f9fafb 50%, transparent 50%); background-size: 10px 10px;"></div>

                <div class="space-y-2 font-mono text-sm text-gray-600 mb-4">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>Rp <span x-text="formatNumber(total)"></span></span>
                    </div>
                    <!-- Tax or Discount placeholder -->
                 <!--   <div class="flex justify-between text-xs">
                        <span>Pajak (0%)</span>
                        <span>Rp 0</span>
                    </div> -->
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t border-gray-300 mb-4">
                    <span class="font-bold text-gray-800 text-lg uppercase">Total</span>
                    <span class="font-bold text-indigo-700 text-xl font-mono">Rp <span x-text="formatNumber(total)"></span></span>
                </div>
                
                <button @click="processCheckout()" 
                        :disabled="cart.length === 0"
                        class="w-full py-3.5 bg-gray-800 text-white rounded-xl font-bold text-lg shadow-lg hover:bg-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95 flex justify-center items-center gap-2">
                    <span>Bayar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
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
            
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            },
            
            addToCart(product) {
                // Check stock logic
                const existingItem = this.cart.find(item => item.id_barang === product.id_barang);
                
                if (existingItem) {
                    if (existingItem.qty >= product.stok) {
                        alert('Stok tidak mencukupi!');
                        return;
                    }
                    existingItem.qty++;
                } else {
                    if (product.stok <= 0) {
                         alert('Stok Habis!');
                         return;
                    }
                    this.cart.push({
                        ...product,
                        qty: 1
                    });
                }
                
                // Play sound effect (optional)
                // new Audio('/sounds/beep.mp3').play().catch(e => {});
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
                    alert('Stok maksimal tercapai!');
                    return;
                }
                
                item.qty = newQty;
            },
            
            clearCart() {
                if(confirm('Kosongkan keranjang?')) {
                    this.cart = [];
                }
            },
            
            processCheckout() {
                if (this.cart.length === 0) return;
                
                const total = this.formatNumber(this.total);
                if (confirm(`Total Belanja: Rp ${total}\n\nLanjutkan pembayaran?`)) {
                    // Logic to backend
                    alert('Simulasi: Transaksi Berhasil!\nStok akan berkurang (perlu implementasi backend).');
                    this.cart = [];
                }
            }
        }
    }
</script>
@endsection
