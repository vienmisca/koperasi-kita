<div class="bg-white rounded-xl border shadow-sm">
    <!-- Header Tabel -->
    <div class="px-6 py-4 border-b flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Daftar Barang Koperasi
            </h2>
            @if(request('search') || request('kategori') || request('stok_status'))
            <p class="text-sm text-indigo-600 mt-1 font-medium">{{ $barang->total() }} barang ditemukan (Filtered)</p>
            @else
            <p class="text-sm text-gray-600 mt-1">{{ $totalBarang ?? 0 }} barang terdaftar</p>
            @endif
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            
            <!-- Filter Status -->
            <div class="relative w-full sm:w-40">
                <select x-model="tableStatus" @change="filterTable()" class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="aman">Aman</option>
                    <option value="menipis">Menipis</option>
                    <option value="habis">Habis</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Filter Kategori -->
            <div class="relative w-full sm:w-48">
                <select x-model="tableCategory" @change="filterTable()" class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Search -->
            <div class="relative w-full sm:w-64">
                <input type="text" 
                       x-model="tableSearch"
                       @input.debounce.500ms="filterTable()"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                       placeholder="Cari barang...">
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Reset Filter Button (Conditional) -->
            <template x-if="tableSearch || tableCategory || tableStatus">
                <button @click="tableSearch=''; tableCategory=''; tableStatus=''; filterTable()" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Reset Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </template>
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
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
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
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center mr-3 shadow-sm text-gray-500">
                                @switch($item->kategori->nama_kategori ?? '')
                                    @case('Snack') 
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @break
                                    @case('Minuman')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                        @break
                                    @case('ATK')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        @break
                                    @case('Sembako')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        @break
                                    @default
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
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

                    <!-- Supplier -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600 truncate max-w-[150px] block" title="{{ $item->supplier->nama ?? '-' }}">
                            {{ $item->supplier->nama ?? '-' }}
                        </span>
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
                            <button @click="printBarcodeWithQty('{{ $item->kode_barang }}', '{{ $item->nama_barang }}')"
                                    class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors"
                                    title="Cetak Label">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
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
