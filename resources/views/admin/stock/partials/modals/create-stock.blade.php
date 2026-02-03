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
         class="bg-white rounded-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden shadow-2xl"
         @click.away="closeModal()">
        
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Barang Masuk
                </h2>
                <p class="text-sm text-gray-600 mt-0.5">Tambah stok baru atau input barang baru ke sistem</p>
            </div>
            <button @click="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-2xl w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50">
                ×
            </button>
        </div>

        <!-- Form Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)] custom-scrollbar">
            <!-- Form Barang Masuk -->
            <form id="formBarangMasuk" @submit.prevent="processBarangMasuk()">
                
                <div class="flex flex-col md:flex-row gap-6 mb-5">
                    <!-- Tanggal Masuk -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            📅 Tanggal Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               x-model="form.tanggal_masuk"
                               class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow text-sm"
                               required>
                    </div>

                    <!-- Pilihan Mode -->
                    <div class="w-full md:w-2/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            🎯 Pilih Mode Input
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button"
                                    @click="mode = 'existing'"
                                    :class="mode === 'existing' 
                                        ? 'bg-blue-50 border-blue-600 ring-2 ring-blue-200' 
                                        : 'bg-white border-gray-200 hover:bg-gray-50'"
                                    class="p-2 border rounded-lg text-center transition-all duration-200 group flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <div class="text-left">
                                    <span class="block font-bold text-gray-900 text-sm">Barang Sudah Ada</span>
                                    <span class="block text-xs text-gray-500">Tambah stok lama</span>
                                </div>
                            </button>
                            
                            <button type="button"
                                    @click="mode = 'new'"
                                    :class="mode === 'new' 
                                        ? 'bg-green-50 border-green-600 ring-2 ring-green-200' 
                                        : 'bg-white border-gray-200 hover:bg-gray-50'"
                                    class="p-2 border rounded-lg text-center transition-all duration-200 group flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div class="text-left">
                                    <span class="block font-bold text-gray-900 text-sm">Barang Baru</span>
                                    <span class="block text-xs text-gray-500">Input produk baru</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- MODE: BARANG SUDAH ADA -->
                <div x-show="mode === 'existing'" x-transition class="mb-5">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 relative overflow-hidden">
                        
                        <h3 class="text-base font-bold text-blue-800 mb-3 relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Pilih Barang yang Sudah Ada
                        </h3>
                        
                        <!-- Pencarian Barang -->
                        <div class="mb-4 relative z-10">
                            <div class="relative">
                                <input type="text" 
                                       x-model="searchQuery"
                                       @input.debounce.300ms="searchBarang()"
                                       class="w-full border border-blue-300 rounded-lg px-3 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm"
                                       placeholder="Cari barang berdasarkan nama atau kode...">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Barang -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 max-h-[30vh] overflow-y-auto p-1 relative z-10 custom-scrollbar">
                            <template x-for="barang in filteredBarang" :key="barang.id_barang">
                                <div class="bg-white border rounded-lg p-3 cursor-pointer transition-all duration-200 hover:shadow-md"
                                     :class="selectedBarang && selectedBarang.id_barang === barang.id_barang 
                                         ? 'border-blue-500 ring-2 ring-blue-200 transform scale-[1.01]' 
                                         : 'hover:border-blue-300'"
                                     @click="selectExistingBarang(barang)">
                                    <div class="flex justify-between items-start">
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-gray-900 text-sm truncate" x-text="barang.nama_barang" :title="barang.nama_barang"></h4>
                                            <div class="text-xs text-gray-500 mt-1 space-y-0.5">
                                                <div class="flex items-center gap-1">
                                                    <span class="bg-gray-100 px-1 rounded text-[10px]">Kode: <span x-text="barang.kode_barang"></span></span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                     <span>Stok: <span x-text="barang.stok" class="font-medium"></span></span>
                                                </div>
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
                             class="mt-4 pt-4 border-t border-blue-200 relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Info Barang Terpilih -->
                                <div class="bg-white/80 backdrop-blur rounded-lg p-3 border border-blue-100 shadow-sm">
                                    <h4 class="font-bold text-gray-900 mb-2 border-b pb-1 text-sm">📦 Info Barang</h4>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div class="flex justify-between"><span>Nama:</span> <span x-text="selectedBarang.nama_barang" class="font-medium text-gray-900 truncate ml-2"></span></div>
                                        <div class="flex justify-between"><span>Kategori:</span> <span x-text="selectedBarang.kategori.nama_kategori" class="font-medium text-gray-900"></span></div>
                                        <div class="flex justify-between"><span>Stok:</span> <span x-text="selectedBarang.stok" class="font-medium text-gray-900"></span></div>
                                        <div class="flex justify-between"><span>Harga Beli:</span> <span class="font-medium text-gray-900">Rp <span x-text="formatNumber(selectedBarang.harga_beli)"></span></span></div>
                                    </div>
                                </div>

                                <!-- Input Jumlah -->
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Jumlah Masuk <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex items-center h-10">
                                            <button type="button" 
                                                    @click="existingItem.jumlah > 1 ? existingItem.jumlah-- : null"
                                                    class="w-10 h-full border border-gray-300 rounded-l-lg bg-gray-50 hover:bg-gray-100 text-lg font-bold transition-colors">
                                                -
                                            </button>
                                            <input type="number" 
                                                   x-model="existingItem.jumlah"
                                                   min="1"
                                                   class="flex-1 h-full border-y border-gray-300 text-center text-base font-bold focus:ring-0 z-10"
                                                   placeholder="0">
                                            <button type="button" 
                                                    @click="existingItem.jumlah++"
                                                    class="w-10 h-full border border-gray-300 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-lg font-bold transition-colors">
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Harga Beli Baru (Opsional) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Harga Beli Baru <span class="text-xs text-gray-500 font-normal">(opsional)</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                                            <input type="number" 
                                                   x-model="existingItem.harga_beli_baru"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-8 focus:ring-2 focus:ring-blue-500 text-sm"
                                                   :placeholder="formatNumber(selectedBarang.harga_beli)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODE: BARANG BARU -->
                <div x-show="mode === 'new'" x-transition class="mb-5">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-5 relative overflow-hidden">
                        
                        <h3 class="text-base font-bold text-green-800 mb-4 relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Input Barang Baru
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 relative z-10">
                            <!-- Kode & Nama -->
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kode Barang <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" 
                                           x-model="newItem.kode_barang"
                                           class="w-full border-gray-300 border rounded-lg px-3 py-2 font-mono focus:ring-2 focus:ring-green-500 text-sm"
                                           placeholder="CONTOH: SN001">
                                    <button type="button" 
                                            @click="generateRandomCode()"
                                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg border border-gray-200 transition-colors tooltip"
                                            title="Generate Code">
                                        🎲
                                    </button>
                                </div>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Barang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       x-model="newItem.nama_barang"
                                       class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 text-sm"
                                       placeholder="Contoh: Indomie Goreng">
                            </div>

                            <!-- Kategori & Satuan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select x-model="newItem.id_kategori"
                                        class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 bg-white text-sm">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori ?? [] as $kat)
                                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <select x-model="newItem.satuan"
                                        class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 bg-white text-sm">
                                    <option value="">Pilih Satuan</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="bungkus">Bungkus</option>
                                    <option value="dus">Dus</option>
                                    <option value="pak">Pak</option>
                                    <option value="kg">Kg</option>
                                    <option value="liter">Liter</option>
                                </select>
                            </div>



                             <!-- Stok -->
                            <div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Awal <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" 
                                            x-model="newItem.stok_awal"
                                            min="0"
                                            class="w-full border-gray-300 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500"
                                            placeholder="0">
                                    </div>
                                    <div>
                                         <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Min <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" 
                                               x-model="newItem.stok_minimal"
                                               min="1"
                                               class="w-full border-gray-300 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500"
                                               placeholder="10">
                                    </div>
                                </div>
                            </div>

                            <!-- Harga Beli & Jual -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Beli <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                                    <input type="number" 
                                           x-model="newItem.harga_beli"
                                           min="0"
                                           class="w-full border-gray-300 border rounded-lg px-3 py-2 pl-8 focus:ring-2 focus:ring-green-500 text-sm"
                                           placeholder="0">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Jual <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                                    <input type="number" 
                                           x-model="newItem.harga_jual"
                                           min="0"
                                           class="w-full border-gray-300 border rounded-lg px-3 py-2 pl-8 focus:ring-2 focus:ring-green-500 text-sm"
                                           placeholder="0">
                                </div>
                            </div>

                             <!-- Upload Gambar Produk (Compact) -->
                            <div class="flex items-center gap-3">
                                <label class="flex-1 flex flex-col items-center justify-center h-16 border-2 border-green-300 border-dashed rounded-lg cursor-pointer bg-green-50 hover:bg-green-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[10px] text-green-700">Foto (Optional)</span>
                                    </div>
                                    <input type="file" accept="image/*" @change="previewImage($event)" class="hidden" />
                                </label>

                                <!-- Preview gambar -->
                                <div x-show="imagePreview" class="relative w-16 h-16 shrink-0">
                                    <img :src="imagePreview" class="w-full h-full object-cover rounded-lg border border-green-500 shadow-sm">
                                    <button @click="imagePreview = null; newItem.gambar = null" type="button" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs shadow-sm pb-0.5">×</button>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mt-4 relative z-10">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Deskripsi (Opsional)
                            </label>
                            <textarea x-model="newItem.deskripsi"
                                      class="w-full border-gray-300 border rounded-lg px-3 py-2 h-14 focus:ring-2 focus:ring-green-500 text-sm"
                                      placeholder="Deskripsi barang..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        📝 Keterangan (Opsional)
                    </label>
                    <textarea x-model="form.keterangan"
                              class="w-full border-gray-300 border rounded-lg px-3 py-2 h-16 focus:ring-2 focus:ring-blue-500 text-sm"
                              placeholder="Contoh: Pembelian dari supplier ABC..."></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end gap-3 border-t pt-4">
                    <button type="button" 
                            @click="closeModal()"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors text-sm">
                        Batal
                    </button>
                    
                    <button type="submit"
                            :disabled="!canSubmit"
                            :class="!canSubmit 
                                ? 'bg-gray-400 cursor-not-allowed' 
                                : mode === 'existing' 
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-blue-200' 
                                    : 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-green-200'"
                            class="px-6 py-2.5 text-white rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg transform active:scale-95 transition-all">
                        <template x-if="mode === 'existing'">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Simpan Stok
                            </span>
                        </template>
                        <template x-if="mode === 'new'">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Simpan Barang
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
