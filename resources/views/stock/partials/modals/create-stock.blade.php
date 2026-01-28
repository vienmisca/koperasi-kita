<!-- MODAL BARANG MASUK -->
<div x-show="showForm" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden shadow-2xl"
         @click.away="closeModal()">
        
        <!-- Header Modal -->
        <div class="px-8 py-6 border-b flex justify-between items-center bg-gray-50">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Barang Masuk
                </h2>
                <p class="text-gray-600 mt-1">Tambah stok baru atau input barang baru ke sistem</p>
            </div>
            <button @click="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-2xl w-10 h-10 flex items-center justify-center rounded-full hover:bg-red-50">
                ×
            </button>
        </div>

        <!-- Form Content -->
        <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar">
            <!-- Form Barang Masuk -->
            <form id="formBarangMasuk" @submit.prevent="processBarangMasuk()">
                <!-- Tanggal Masuk -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📅 Tanggal Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           x-model="form.tanggal_masuk"
                           class="w-full border-gray-300 border rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
                           required>
                </div>

                <!-- Pilihan Mode -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        🎯 Pilih Mode Input
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button"
                                @click="mode = 'existing'"
                                :class="mode === 'existing' 
                                    ? 'bg-blue-50 border-blue-600 ring-2 ring-blue-200' 
                                    : 'bg-white border-gray-200 hover:bg-gray-50'"
                                class="p-4 border rounded-xl text-center transition-all duration-200 group">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span class="font-bold text-gray-900">Barang Sudah Ada</span>
                                <span class="text-sm text-gray-500">Tambah stok barang yang terdaftar</span>
                            </div>
                        </button>
                        
                        <button type="button"
                                @click="mode = 'new'"
                                :class="mode === 'new' 
                                    ? 'bg-green-50 border-green-600 ring-2 ring-green-200' 
                                    : 'bg-white border-gray-200 hover:bg-gray-50'"
                                class="p-4 border rounded-xl text-center transition-all duration-200 group">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-gray-900">Barang Baru</span>
                                <span class="text-sm text-gray-500">Input barang baru ke sistem</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- MODE: BARANG SUDAH ADA -->
                <div x-show="mode === 'existing'" x-transition class="mb-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                        </div>
                        
                        <h3 class="text-lg font-bold text-blue-800 mb-4 relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Pilih Barang yang Sudah Ada
                        </h3>
                        
                        <!-- Pencarian Barang -->
                        <div class="mb-6 relative z-10">
                            <div class="relative">
                                <input type="text" 
                                       x-model="searchQuery"
                                       @input.debounce.300ms="searchBarang()"
                                       class="w-full border border-blue-300 rounded-xl px-4 py-3 pl-12 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                       placeholder="Cari barang berdasarkan nama atau kode...">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Barang -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-60 overflow-y-auto p-2 relative z-10 custom-scrollbar">
                            <template x-for="barang in filteredBarang" :key="barang.id_barang">
                                <div class="bg-white border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md"
                                     :class="selectedBarang && selectedBarang.id_barang === barang.id_barang 
                                         ? 'border-blue-500 ring-2 ring-blue-200 transform scale-[1.02]' 
                                         : 'hover:border-blue-300'"
                                     @click="selectExistingBarang(barang)">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-gray-900" x-text="barang.nama_barang"></h4>
                                            <div class="text-xs text-gray-500 mt-1 space-y-1">
                                                <div class="flex items-center gap-1">
                                                    <span class="bg-gray-100 px-1.5 rounded">Kode: <span x-text="barang.kode_barang"></span></span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                     <span>Stok: <span x-text="barang.stok" class="font-medium"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                                Rp <span x-text="formatNumber(barang.harga_jual)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Form Input Stok -->
                        <div x-show="selectedBarang" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-6 pt-6 border-t border-blue-200 relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Info Barang Terpilih -->
                                <div class="bg-white/80 backdrop-blur rounded-xl p-4 border border-blue-100 shadow-sm">
                                    <h4 class="font-bold text-gray-900 mb-2 border-b pb-2">📦 Info Barang</h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex justify-between"><span>Nama:</span> <span x-text="selectedBarang.nama_barang" class="font-medium text-gray-900"></span></div>
                                        <div class="flex justify-between"><span>Kategori:</span> <span x-text="selectedBarang.kategori.nama_kategori" class="font-medium text-gray-900"></span></div>
                                        <div class="flex justify-between"><span>Stok Saat Ini:</span> <span x-text="selectedBarang.stok" class="font-medium text-gray-900"></span></div>
                                        <div class="flex justify-between"><span>Harga Beli Terakhir:</span> <span class="font-medium text-gray-900">Rp <span x-text="formatNumber(selectedBarang.harga_beli)"></span></span></div>
                                    </div>
                                </div>

                                <!-- Input Jumlah -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Jumlah Masuk <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex items-center">
                                            <button type="button" 
                                                    @click="existingItem.jumlah > 1 ? existingItem.jumlah-- : null"
                                                    class="w-12 h-12 border border-gray-300 rounded-l-xl bg-gray-50 hover:bg-gray-100 text-xl font-bold transition-colors">
                                                -
                                            </button>
                                            <input type="number" 
                                                   x-model="existingItem.jumlah"
                                                   min="1"
                                                   class="flex-1 h-12 border-y border-gray-300 text-center text-lg font-bold focus:ring-0 z-10"
                                                   placeholder="0">
                                            <button type="button" 
                                                    @click="existingItem.jumlah++"
                                                    class="w-12 h-12 border border-gray-300 rounded-r-xl bg-gray-50 hover:bg-gray-100 text-xl font-bold transition-colors">
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Harga Beli Baru (Opsional) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Harga Beli Baru (jika berubah)
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                            <input type="number" 
                                                   x-model="existingItem.harga_beli_baru"
                                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 pl-10 focus:ring-2 focus:ring-blue-500"
                                                   :placeholder="formatNumber(selectedBarang.harga_beli)">
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika harga beli sama dengan sebelumnya.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODE: BARANG BARU -->
                <div x-show="mode === 'new'" x-transition class="mb-8">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 001-1l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                        </div>

                        <h3 class="text-lg font-bold text-green-800 mb-6 relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Input Barang Baru
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                            <!-- Kode & Nama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Barang <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" 
                                           x-model="newItem.kode_barang"
                                           class="w-full border-gray-300 border rounded-xl px-4 py-3 font-mono focus:ring-2 focus:ring-green-500"
                                           placeholder="CONTOH: SN001">
                                    <button type="button" 
                                            @click="generateRandomCode()"
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl border border-gray-200 transition-colors tooltip"
                                            title="Generate Code">
                                        🎲
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Barang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       x-model="newItem.nama_barang"
                                       class="w-full border-gray-300 border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500"
                                       placeholder="Contoh: Indomie Goreng">
                            </div>

                            <!-- Kategori & Satuan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select x-model="newItem.id_kategori"
                                        class="w-full border-gray-300 border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 bg-white">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori ?? [] as $kat)
                                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <select x-model="newItem.satuan"
                                        class="w-full border-gray-300 border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 bg-white">
                                    <option value="">Pilih Satuan</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="bungkus">Bungkus</option>
                                    <option value="dus">Dus</option>
                                    <option value="pak">Pak</option>
                                    <option value="kg">Kg</option>
                                    <option value="liter">Liter</option>
                                </select>
                            </div>

                            <!-- Harga Beli & Jual -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga Beli <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="number" 
                                           x-model="newItem.harga_beli"
                                           min="0"
                                           class="w-full border-gray-300 border rounded-xl px-4 py-3 pl-10 focus:ring-2 focus:ring-green-500"
                                           placeholder="0">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga Jual <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="number" 
                                           x-model="newItem.harga_jual"
                                           min="0"
                                           class="w-full border-gray-300 border rounded-xl px-4 py-3 pl-10 focus:ring-2 focus:ring-green-500"
                                           placeholder="0">
                                </div>
                            </div>

                            <!-- Upload Gambar Produk -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Gambar Produk (opsional)
                                </label>

                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-green-300 border-dashed rounded-xl cursor-pointer bg-green-50 hover:bg-green-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <p class="mb-2 text-sm text-green-700"><span class="font-semibold">Klik untuk upload</span></p>
                                        </div>
                                        <input type="file" accept="image/*" @change="previewImage($event)" class="hidden" />
                                    </label>
                                </div>

                                <!-- Preview gambar -->
                                <div x-show="imagePreview" class="mt-3 relative w-32 h-32">
                                    <img :src="imagePreview" class="w-full h-full object-cover rounded-xl border-2 border-green-500 shadow-md">
                                    <button @click="imagePreview = null; newItem.gambar = null" type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm">×</button>
                                </div>
                            </div>


                            <!-- Stok Awal & Minimal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stok Awal <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center">
                                    <button type="button" 
                                            @click="newItem.stok_awal > 0 ? newItem.stok_awal-- : null"
                                            class="w-12 h-12 border border-gray-300 rounded-l-xl bg-gray-50 hover:bg-gray-100 text-xl font-bold">
                                        -
                                    </button>
                                    <input type="number" 
                                           x-model="newItem.stok_awal"
                                           min="0"
                                           class="flex-1 h-12 border-y border-gray-300 text-center text-lg font-bold focus:ring-0"
                                           placeholder="0">
                                    <button type="button" 
                                            @click="newItem.stok_awal++"
                                            class="w-12 h-12 border border-gray-300 rounded-r-xl bg-gray-50 hover:bg-gray-100 text-xl font-bold">
                                        +
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stok Minimal <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       x-model="newItem.stok_minimal"
                                       min="1"
                                       class="w-full border-gray-300 border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500"
                                       placeholder="10">
                                <p class="text-xs text-gray-500 mt-1">Peringatan akan muncul jika stok di bawah ini.</p>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mt-6 relative z-10">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi (Opsional)
                            </label>
                            <textarea x-model="newItem.deskripsi"
                                      class="w-full border-gray-300 border rounded-xl px-4 py-3 h-24 focus:ring-2 focus:ring-green-500"
                                      placeholder="Deskripsi barang..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📝 Keterangan (Opsional)
                    </label>
                    <textarea x-model="form.keterangan"
                              class="w-full border-gray-300 border rounded-xl px-4 py-3 h-20 focus:ring-2 focus:ring-blue-500"
                              placeholder="Contoh: Pembelian dari supplier ABC, kiriman gudang pusat, dll..."></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end gap-4 border-t pt-6">
                    <button type="button" 
                            @click="closeModal()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                        Batal
                    </button>
                    
                    <button type="submit"
                            :disabled="!canSubmit"
                            :class="!canSubmit 
                                ? 'bg-gray-400 cursor-not-allowed' 
                                : mode === 'existing' 
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-blue-200' 
                                    : 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-green-200'"
                            class="px-8 py-3 text-white rounded-xl font-bold text-lg flex items-center gap-3 shadow-lg transform active:scale-95 transition-all">
                        <template x-if="mode === 'existing'">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Simpan Stok Masuk
                            </span>
                        </template>
                        <template x-if="mode === 'new'">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Simpan Barang Baru
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
