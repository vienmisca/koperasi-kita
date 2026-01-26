<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <!-- Total Barang -->
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-blue-50 rounded-lg mr-4 text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Barang</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalBarang ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Total Stok -->
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-green-50 rounded-lg mr-4 text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Stok</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalStok ?? 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Stok Hampir Habis -->
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-50 rounded-lg mr-4 text-yellow-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Hampir Habis</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $barangHampirHabis ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Nilai Stok -->
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-purple-50 rounded-lg mr-4 text-purple-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Nilai Stok</p>
                <p class="text-2xl font-bold text-gray-900">
                    Rp {{ number_format($totalNilaiStok ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>
