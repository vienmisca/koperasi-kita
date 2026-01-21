<div class="mb-8">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <span>📦</span> Manajemen Stok Barang
            </h1>
            <p class="text-gray-600 mt-2">Kelola stok barang koperasi SMK dengan mudah</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Barang Masuk -->
            <button @click="showForm = true"
                    class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow-md hover:shadow-lg hover:from-blue-700 hover:to-blue-800 transform hover:-translate-y-0.5 transition-all duration-200 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Barang Masuk</span>
            </button>

            <!-- Kategori Baru -->
            <button @click="showKategoriModal = true"
                    class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg shadow-md hover:shadow-lg hover:from-purple-700 hover:to-purple-800 transform hover:-translate-y-0.5 transition-all duration-200 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span>Kategori Baru</span>
            </button>

            <!-- Mutasi Stok -->
            <a href="{{ route('stock.mutasi') }}" 
               class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:text-gray-900 transform hover:-translate-y-0.5 transition-all duration-200 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span>Mutasi Stok</span>
            </a>
        </div>
    </div>
</div>
