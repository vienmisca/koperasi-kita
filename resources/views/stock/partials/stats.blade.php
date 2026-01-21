<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <!-- Total Barang -->
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-blue-50 rounded-lg mr-4 text-blue-600">
                <span class="text-2xl">📦</span>
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
                <span class="text-2xl">📊</span>
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
                <span class="text-2xl">⚠️</span>
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
                <span class="text-2xl">💰</span>
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
