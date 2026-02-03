<!-- ================= MODAL EDIT BARANG ================= -->
<div x-show="showEditModal" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-50">

    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl"
         x-show="showEditModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         @click.away="closeModal()">

        <div class="flex justify-between items-center mb-5 border-b pb-3">
            <h2 class="text-xl font-bold flex items-center gap-2 text-gray-900">
                <span>✏️</span> Edit Barang
            </h2>
            <button @click="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-2xl w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50">
                ×
            </button>
        </div>

        <form @submit.prevent="submitEditBarang()">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kode Barang -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Kode Barang</label>
                        <div class="flex gap-2">
                            <input type="text"
                                   x-model="editItem.kode_barang"
                                   class="w-full border-gray-300 border rounded-lg px-3 py-2 font-mono bg-gray-50 focus:ring-2 focus:ring-blue-500 text-sm"
                                   placeholder="Kode Barang..."
                                   required>
                            <button type="button" @click="generateRandomCode('edit')"
                                    class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg border border-gray-200 transition-colors" title="Generate New Code">
                                🎲
                            </button>
                            <button type="button" @click="printBarcode(editItem.kode_barang, editItem.nama_barang)"
                                    class="px-2.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg border border-indigo-100 transition-colors" title="Print Barcode">
                                🖨️
                            </button>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Kategori</label>
                        <select x-model="editItem.id_kategori"
                                class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>



                <!-- Nama Barang -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Nama Barang</label>
                    <input type="text"
                           x-model="editItem.nama_barang"
                           class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Satuan -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Satuan</label>
                        <select x-model="editItem.satuan"
                                class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                            <option value="pcs">Pcs</option>
                            <option value="bungkus">Bungkus</option>
                            <option value="dus">Dus</option>
                            <option value="pak">Pak</option>
                            <option value="kg">Kg</option>
                            <option value="liter">Liter</option>
                        </select>
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Harga Beli</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                            <input type="number"
                                   x-model="editItem.harga_beli"
                                   class="w-full border-gray-300 border rounded-lg px-3 py-2 pl-8 focus:ring-2 focus:ring-blue-500 text-sm"
                                   required>
                        </div>
                    </div>

                    <!-- Harga Jual -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Harga Jual</label>
                         <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                            <input type="number"
                                   x-model="editItem.harga_jual"
                                   class="w-full border-gray-300 border rounded-lg px-3 py-2 pl-8 focus:ring-2 focus:ring-blue-500 text-sm"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Stok Minimal -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Stok Minimal</label>
                        <input type="number"
                               x-model="editItem.stok_minimal"
                               class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 text-sm"
                               required>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Status</label>
                        <select x-model="editItem.status"
                                class="w-full border-gray-300 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Image Upload in Edit (Compact) -->
                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                     <div class="flex items-center gap-4">
                        <div class="relative w-16 h-16 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden shrink-0">
                             <template x-if="editItem.imagePreview">
                                <img :src="editItem.imagePreview" class="w-full h-full object-cover">
                             </template>
                             <template x-if="!editItem.imagePreview && editItem.gambar">
                                <img :src="'/storage/' + editItem.gambar" class="w-full h-full object-cover">
                             </template>
                             <template x-if="!editItem.imagePreview && !editItem.gambar">
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                             </template>
                        </div>
                        
                        <div class="flex-1">
                             <label class="inline-block px-4 py-2 border border-gray-300 rounded-lg cursor-pointer bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                                Pilih Gambar
                                <input type="file" accept="image/*" @change="previewEditImage($event)" class="hidden">
                            </label>
                            <span class="text-[10px] text-gray-500 ml-2">JPG/PNG Max 2MB</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                <button type="button"
                        @click="closeModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition-colors text-sm">
                    Batal
                </button>

                <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md font-medium transition-all transform active:scale-95 text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
