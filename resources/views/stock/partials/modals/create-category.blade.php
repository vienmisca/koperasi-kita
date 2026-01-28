<!-- ================= MODAL TAMBAH KATEGORI ================= -->
<div x-show="showKategoriModal" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-50">

    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl"
         x-show="showKategoriModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         @click.away="showKategoriModal = false">

        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold flex items-center gap-2 text-gray-900">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Tambah Kategori Baru
            </h2>
            <button @click="showKategoriModal = false"
                    class="text-gray-400 hover:text-red-500 transition-colors text-2xl w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50">
                ×
            </button>
        </div>

        <form @submit.prevent="submitKategori()">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Kode Kategori</label>
                    <input type="text"
                           x-model="kategoriBaru.kode_kategori"
                           class="w-full border-gray-300 border rounded-lg px-4 py-2 uppercase font-mono tracking-wider focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="CONTOH: SNACK"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Nama Kategori</label>
                    <input type="text"
                           x-model="kategoriBaru.nama_kategori"
                           class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Contoh: Snack & Makanan Ringan"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Deskripsi (opsional)</label>
                    <textarea x-model="kategoriBaru.deskripsi"
                              class="w-full border-gray-300 border rounded-lg px-4 py-2 h-24 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Deskripsi kategori..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t pt-4">
                <button type="button"
                        @click="showKategoriModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                    Batal
                </button>

                <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 shadow-md font-medium transition-all transform active:scale-95">
                    Simpan Kategori
                </button>
            </div>
        </form>

        <!-- Existing Categories List -->
        <div class="mt-8 pt-6 border-t border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Daftar Kategori</h3>
            <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-2">
                <template x-for="kat in kategoriList" :key="kat.id_kategori">
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100 group hover:border-purple-200 transition-colors">
                        <div>
                            <p class="font-bold text-gray-800 text-sm" x-text="kat.nama_kategori"></p>
                            <p class="text-xs text-gray-400 font-mono" x-text="kat.kode_kategori"></p>
                        </div>
                        <button @click="hapusKategori(kat.id_kategori, kat.nama_kategori)"
                                class="text-gray-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100 focus:opacity-100"
                                title="Hapus Kategori">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
                <div x-show="kategoriList.length === 0" class="text-center py-4 text-gray-400 text-sm">
                    Belum ada kategori data.
                </div>
            </div>
        </div>
    </div>
</div>
