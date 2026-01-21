<!-- ================= MODAL EDIT BARANG ================= -->
<div x-show="showEditModal" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-50">

    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl"
         x-show="showEditModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         @click.away="showEditModal = false">

        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold flex items-center gap-2 text-gray-900">
                <span>✏️</span> Edit Barang
            </h2>
            <button @click="showEditModal = false"
                    class="text-gray-400 hover:text-red-500 transition-colors text-2xl w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50">
                ×
            </button>
        </div>

        <form @submit.prevent="submitEditBarang()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Nama Barang</label>
                    <input type="text"
                           x-model="editItem.nama_barang"
                           class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Kategori</label>
                        <select x-model="editItem.id_kategori"
                                class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Satuan</label>
                        <select x-model="editItem.satuan"
                                class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="pcs">Pcs</option>
                            <option value="bungkus">Bungkus</option>
                            <option value="dus">Dus</option>
                            <option value="pak">Pak</option>
                            <option value="kg">Kg</option>
                            <option value="liter">Liter</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                     <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Harga Beli</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                            <input type="number"
                                   x-model="editItem.harga_beli"
                                   class="w-full border-gray-300 border rounded-lg px-4 py-2 pl-9 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Harga Jual</label>
                         <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                            <input type="number"
                                   x-model="editItem.harga_jual"
                                   class="w-full border-gray-300 border rounded-lg px-4 py-2 pl-9 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Stok Minimal</label>
                        <input type="number"
                               x-model="editItem.stok_minimal"
                               class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Status</label>
                        <select x-model="editItem.status"
                                class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t pt-4">
                <button type="button"
                        @click="showEditModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                    Batal
                </button>

                <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md font-medium transition-all transform active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
