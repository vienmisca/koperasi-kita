@extends('layouts.app')

@section('content')
<div x-data="stockViewer()" class="animate-fade-in-down">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Cek Stok Barang
            </h1>
            <p class="text-gray-600 mt-1">Lihat ketersediaan dan harga barang.</p>
        </div>
        
        <!-- Search & Filter -->
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative">
                <input type="text" 
                       x-model="search" 
                       class="w-full md:w-64 pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" 
                       placeholder="Cari nama / kode barang...">
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            
            <select x-model="category" 
                    class="border rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->nama_kategori }}">{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Barang</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Kategori</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Harga</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Stok</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="item in filteredItems" :key="item.id_barang">
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-medium text-gray-900" x-text="item.nama_barang"></div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5" x-text="item.kode_barang"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600" x-text="item.kategori ? item.kategori.nama_kategori : '-'"></span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                Rp <span x-text="formatNumber(item.harga_jual)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold" x-text="item.stok"></span>
                                <span class="text-xs text-gray-500" x-text="item.satuan"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-bold"
                                      :class="{
                                          'bg-green-100 text-green-700': item.stok > item.stok_minimal,
                                          'bg-yellow-100 text-yellow-700': item.stok <= item.stok_minimal && item.stok > 0,
                                          'bg-red-100 text-red-700': item.stok <= 0
                                      }"
                                      x-text="getStatusText(item)">
                                </span>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="filteredItems.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada barang yang ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function stockViewer() {
        return {
            search: '',
            category: '',
            items: @json($barang),
            
            get filteredItems() {
                return this.items.filter(item => {
                    const matchesSearch = item.nama_barang.toLowerCase().includes(this.search.toLowerCase()) || 
                                          item.kode_barang.toLowerCase().includes(this.search.toLowerCase());
                    const matchesCategory = this.category === '' || (item.kategori && item.kategori.nama_kategori === this.category);
                    
                    return matchesSearch && matchesCategory;
                });
            },
            
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            },
            
            getStatusText(item) {
                if (item.stok <= 0) return 'Habis';
                if (item.stok <= item.stok_minimal) return 'Low';
                return 'Ready';
            }
        }
    }
</script>
@endsection
