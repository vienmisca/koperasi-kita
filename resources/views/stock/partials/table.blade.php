<div class="bg-white rounded-xl border shadow-sm">
    <!-- Header Tabel -->
    <div class="px-6 py-4 border-b flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">📋 Daftar Barang Koperasi</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $totalBarang ?? 0 }} barang terdaftar</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Search -->
            <div class="relative w-full md:w-64">
                <input type="text" 
                       x-model="tableSearch"
                       @input.debounce.300ms="filterTable()"
                       class="border rounded-lg px-4 py-2 pl-10 text-sm w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
                       placeholder="Cari nama atau kode barang...">
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            
            <!-- Filter Kategori -->
            <select x-model="tableCategory"
                    @change="filterTable()"
                    class="border rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow bg-white">
                <option value="">Semua Kategori</option>
                @foreach($barang->pluck('kategori.nama_kategori')->unique() as $kategori)
                    @if($kategori)
                        <option value="{{ $kategori }}">{{ $kategori }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="barangTableBody">
                @foreach($barang as $item)
                @php
                    $statusColor = match(true) {
                        $item->stok <= 0 => 'bg-red-100 text-red-800 border border-red-200',
                        $item->stok <= $item->stok_minimal => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                        default => 'bg-green-100 text-green-800 border border-green-200'
                    };
                    
                    $statusText = match(true) {
                        $item->stok <= 0 => 'Kosong',
                        $item->stok <= $item->stok_minimal => 'Hampir Habis',
                        default => 'Aman'
                    };
                @endphp
                
                <tr class="hover:bg-blue-50 transition-colors duration-150 group" data-name="{{ strtolower($item->nama_barang) }}" 
                    data-category="{{ strtolower($item->kategori->nama_kategori ?? '') }}">
                    <!-- Barang -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                                @switch($item->kategori->nama_kategori ?? '')
                                    @case('Snack') 🍿 @break
                                    @case('Minuman') 🥤 @break
                                    @case('ATK') ✏️ @break
                                    @case('Sembako') 🍚 @break
                                    @default 🛒
                                @endswitch
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $item->nama_barang }}</div>
                                <div class="text-xs text-gray-500 font-mono bg-gray-100 px-2 py-0.5 rounded inline-block mt-1">{{ $item->kode_barang }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Kategori -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-100 font-medium">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </span>
                    </td>
                    
                    <!-- Harga -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            Beli: Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                        </div>
                    </td>
                    
                    <!-- Stok -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-24 mr-3 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                @php
                                    $maxStok = max($item->stok_minimal * 3, $item->stok, 10);
                                    $percentage = min(100, ($item->stok / $maxStok) * 100);
                                @endphp
                                <div class="h-full {{ $item->stok <= 0 ? 'bg-red-500' : ($item->stok <= $item->stok_minimal ? 'bg-yellow-500' : 'bg-green-500') }} transition-all duration-500 ease-out" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-bold {{ $item->stok <= $item->stok_minimal ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $item->stok }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $item->satuan }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    
                    <!-- Aksi -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <button @click="tambahStokBarang({{ $item->id_barang }}, '{{ $item->nama_barang }}')"
                                    class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                    title="Tambah Stok">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                            <button @click="openEditModal({{ $item->id_barang }})"
                                    class="p-1.5 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                                    title="Edit Barang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click="hapusBarang({{ $item->id_barang }}, '{{ $item->nama_barang }}')"
                                    class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                    title="Hapus Barang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
