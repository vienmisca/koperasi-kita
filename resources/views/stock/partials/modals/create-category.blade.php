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
                <span>🏷️</span> Tambah Kategori Baru
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
    </div>
</div>
