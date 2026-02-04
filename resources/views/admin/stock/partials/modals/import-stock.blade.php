<!-- ================= MODAL IMPORT STOCK (SIMPLIFIED) ================= -->
<div x-show="showImportModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-[70]">

    <div x-show="showImportModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden"
         @click.away="showImportModal = false">
         
        <!-- Header -->
        <div class="bg-green-600 px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Data Stok
            </h3>
            <button @click="showImportModal = false" class="text-green-200 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-800 mb-4">
                <p class="font-semibold mb-1">Catatan:</p>
                <p>Gunakan format <strong>.CSV</strong>. Kolom wajib: kode_barang, nama_barang, harga_beli, stok.</p>
                <div class="mt-2 text-right">
                     <a href="{{ route('admin.stock.template') }}" class="text-green-700 underline text-xs font-bold hover:text-green-900">Download Template</a>
                </div>
            </div>

            <form enctype="multipart/form-data" @submit.prevent="submitImport()">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV</label>
                    <input type="file" 
                           accept=".csv, .txt"
                           @change="fileSelected = $event.target.files[0]"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-green-50 file:text-green-700
                                  hover:file:bg-green-100
                                  cursor-pointer border border-gray-300 rounded-lg p-1">
                </div>

                <div x-show="fileSelected" class="text-sm text-gray-600 mb-4 bg-gray-50 p-2 rounded border border-gray-200">
                    📂 <span x-text="fileSelected ? fileSelected.name : ''"></span>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="!fileSelected || isImporting"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium shadow-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                         <svg x-show="isImporting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isImporting ? 'Mengupload...' : 'Import Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
