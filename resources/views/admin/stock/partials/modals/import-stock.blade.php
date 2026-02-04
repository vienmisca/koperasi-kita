<!-- ================= MODAL IMPORT STOCK ================= -->
<div x-show="showImportModal" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-50">

    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl"
         x-show="showImportModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         @click.away="showImportModal = false">

        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold flex items-center gap-2 text-gray-900">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Data Stok
            </h2>
            <button @click="showImportModal = false"
                    class="text-gray-400 hover:text-red-500 transition-colors text-2xl w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50">
                ×
            </button>
        </div>

        <form enctype="multipart/form-data" @submit.prevent="submitImport()">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex gap-3">
                    <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm text-blue-800 flex-1">
                        <p class="font-bold mb-1">Panduan Import:</p>
                        <ul class="list-disc list-inside space-y-1 ml-1 text-blue-700 mb-3">
                            <li>Gunakan file format <strong>.CSV</strong> (Comma Delimited).</li>
                            <li>Jika edit di Excel, pilih <strong>Save As > CSV (Comma delimited)</strong>.</li>
                            <li>Data akan <strong>diupdate</strong> jika kode barang sama.</li>
                            <li>Data baru akan <strong>ditambahkan</strong> jika kode belum ada.</li>
                        </ul>
                        
                        <a href="{{ route('admin.stock.template') }}" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-900 underline font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"/></svg>
                            Download Template CSV
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Pilih File CSV</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-colors cursor-pointer relative">
                        <input type="file" 
                               x-ref="fileInput"
                               accept=".csv, .txt"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               @change="fileSelected = $event.target.files[0]">
                        
                        <div x-show="!fileSelected">
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-sm text-gray-500">Klik atau drag file CSV ke sini</p>
                        </div>

                        <div x-show="fileSelected" class="flex items-center gap-2 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-medium text-sm" x-text="fileSelected ? fileSelected.name : ''"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t pt-4">
                <button type="button"
                        @click="showImportModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                    Batal
                </button>

                <button type="submit"
                        :disabled="!fileSelected || isImporting"
                        class="px-5 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-md font-medium transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="isImporting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isImporting ? 'Mengimport...' : 'Import Sekarang'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
