<!-- ================= MODAL TAMBAH KATEGORI (SIMPLIFIED) ================= -->
<div x-show="showKategoriModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-[70]">

    <div x-show="showKategoriModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden"
         @click.away="showKategoriModal = false">
        
        <!-- Header -->
        <div class="bg-purple-600 px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kelola Kategori
            </h3>
            <button @click="showKategoriModal = false" class="text-purple-200 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <form @submit.prevent="submitKategori()">
                <div class="space-y-4">
                    <!-- Kode Kategori -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Suffix</label>
                        <input type="text" 
                               x-model="kategoriBaru.kode_kategori" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 uppercase"
                               placeholder="Contoh: SNACK"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Kode unik untuk kategori ini (ex: ELEKTRONIK)</p>
                    </div>

                    <!-- Nama Kategori -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" 
                               x-model="kategoriBaru.nama_kategori" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                               placeholder="Contoh: Makanan Ringan"
                               required>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea x-model="kategoriBaru.deskripsi" 
                                  rows="3"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                  placeholder="Keterangan opsional..."></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                    <button type="button" @click="showKategoriModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium shadow-md transition-colors">
                        Simpan Kategori
                    </button>
                </div>
            </form>

            <!-- Mini List (Optional, simplified) -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Daftar Kategori Terbaru</h4>
                <div class="max-h-32 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                    <template x-for="kat in kategoriList" :key="kat.id_kategori">
                        <div class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded border border-gray-100">
                            <div>
                                <span class="font-bold text-gray-800" x-text="kat.nama_kategori"></span>
                                <span class="text-xs text-gray-500 ml-1" x-text="'(' + kat.kode_kategori + ')'"></span>
                            </div>
                            <button type="button" @click="hapusKategori(kat.id_kategori)" class="text-red-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
