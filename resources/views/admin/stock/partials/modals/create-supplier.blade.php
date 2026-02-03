<!-- MODAL TAMBAH SUPPLIER -->
<div x-show="showSupplierModal" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-[70]"> <!-- High Z-Index for nested modal -->
    
    <div x-show="showSupplierModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="bg-white rounded-2xl w-full max-w-lg shadow-2xl"
         @click.away="showSupplierModal = false">
        
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Tambah Supplier Baru
            </h3>
            <button @click="showSupplierModal = false" class="text-gray-400 hover:text-red-500 transition-colors text-xl">×</button>
        </div>

        <form @submit.prevent="submitSupplier()" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" x-model="supplierBaru.nama" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="PT. Berkah Jaya">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Person</label>
                        <input type="text" x-model="supplierBaru.kontak" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm" placeholder="Bpk. Budi">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" x-model="supplierBaru.telepon" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm" placeholder="0812...">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea x-model="supplierBaru.alamat" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm" placeholder="Alamat lengkap..."></textarea>
                </div>
                
                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                     <textarea x-model="supplierBaru.catatan" rows="1" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="showSupplierModal = false" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition-colors font-bold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
