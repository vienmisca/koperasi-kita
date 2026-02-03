@extends('layouts.admin')

@section('title', 'Mutasi Stok')

@section('content')
<div x-data="mutasiSystem()" x-init="init()" class="min-h-screen bg-gray-50 p-6">
    
    <!-- Success/Error Message (Toast) -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
        x-transition:enter="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        class="fixed top-5 right-5 z-50 bg-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-4 animate-fade-in-down border-l-4 border-green-800">
        <div class="bg-green-800 p-2 rounded-full">
            <i class="fas fa-check text-white"></i>
        </div>
        <div>
            <h4 class="font-bold text-sm">Berhasil!</h4>
            <p class="text-xs text-green-100">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="ml-auto hover:text-green-200 text-white transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Mutasi Stok</h1>
            <p class="text-gray-500 text-sm mt-1">Lacak semua pergerakan stok (Masuk, Keluar, Transfer)</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button @click="openModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg transition-all shadow-sm hover:shadow-md flex items-center gap-2 font-medium focus:ring-4 focus:ring-indigo-200">
                <i class="fas fa-plus-circle"></i>
                <span>Catat Mutasi</span>
            </button>
            <a :href="getExportUrl()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg transition-all shadow-sm hover:shadow-md flex items-center gap-2 font-medium focus:ring-4 focus:ring-emerald-200">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.stock.template') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-5 py-2.5 rounded-lg transition-all shadow-sm flex items-center gap-2 font-medium focus:ring-4 focus:ring-gray-100">
                <i class="fas fa-download text-gray-500"></i>
                <span>Template</span>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition cursor-default">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide">Mutasi Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $todayCount }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition cursor-default">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide">Stok Masuk</p>
                <h3 class="text-2xl font-bold text-green-600 mt-1">+{{ number_format($todayIn) }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition cursor-default">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide">Stok Keluar</p>
                <h3 class="text-2xl font-bold text-red-600 mt-1">-{{ number_format($todayOut) }}</h3>
            </div>
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
            </div>
        </div>
        
        <!-- Card 4 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition cursor-default">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide">Estimasi Nilai</p>
                <h3 class="text-xl font-bold text-indigo-600 mt-1">Rp {{ number_format($todayValue, 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar (Sticky) -->
    <div class="sticky top-4 z-20 mb-6 bg-white rounded-xl shadow-md border border-gray-100 p-4 transition-all duration-300" 
         :class="{ 'shadow-lg ring-1 ring-black/5': isSticky }">
        
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
            
            <!-- Search Global -->
            <div class="relative w-full md:w-1/3">
                <input type="text" x-model.debounce.500ms="params.q" @input="fetchData()"
                    placeholder="Cari transaksi, barang, atau ref..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all focus:bg-white">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                
                <!-- Loading Indicator Small -->
                <div x-show="loading" class="absolute right-3 top-2.5">
                    <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Filter Toggles -->
            <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                <button @click="toggleFilter()" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm font-medium transition whitespace-nowrap"
                    :class="showFilter ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <i class="fas fa-filter"></i>
                    <span>Filter Lanjutan</span>
                    <span x-show="activeFilterCount > 0" class="bg-indigo-600 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1" x-text="activeFilterCount"></span>
                    <i class="fas fa-chevron-down ml-1 transition-transform" :class="showFilter ? 'rotate-180' : ''"></i>
                </button>

                <template x-if="activeFilterCount > 0">
                    <button @click="resetFilters()" class="text-sm text-red-500 hover:underline px-2">Reset</button>
                </template>
            </div>
        </div>

        <!-- Collapsible Filter Panel -->
        <div x-show="showFilter" x-collapse
             class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" x-model="params.start_date" @change="fetchData()" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" x-model="params.end_date" @change="fetchData()"
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Jenis Mutasi</label>
                <select x-model="params.jenis" @change="fetchData()" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Jenis</option>
                    <option value="MASUK">Stok Masuk</option>
                    <option value="KELUAR">Stok Keluar</option>
                    <option value="ADJUSTMENT">Penyesuaian</option>
                    <option value="TRANSFER">Transfer</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">User</label>
                <select x-model="params.user" @change="fetchData()"
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua User</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE CONTENT (AJAX TARGET) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative min-h-[400px]">
        
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/70 z-30 flex items-center justify-center backdrop-blur-[1px]">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-indigo-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-medium text-gray-600">Memuat data...</p>
            </div>
        </div>

        <!-- Initial Content -->
        <div id="table-wrapper">
             @include('admin.stock.partials.mutasi-table')
        </div>
        
    </div>

    <!-- MODAL CREATE MUTATION -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                
                <form action="{{ route('admin.stock.mutasi.store') }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                             <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-edit"></i>
                             </div>
                             Input Mutasi Stok
                        </h3>
                        <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-100 rounded-lg p-1.5 transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-6 py-6 space-y-6">
                        
                        <!-- Type Selection -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Mutasi</label>
                                <div class="relative">
                                    <select x-model="form.jenis" name="jenis" class="block w-full pl-4 pr-10 py-2.5 border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50/50">
                                        <option value="MASUK">Stok Masuk (Pembelian)</option>
                                        <option value="KELUAR">Stok Keluar (Terjual)</option>
                                        <option value="ADJUSTMENT">Penyesuaian (Opname)</option>
                                        <option value="TRANSFER">Transfer Gudang</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mutasi</label>
                                <input type="date" name="tanggal" x-model="form.tanggal" 
                                    class="block w-full py-2.5 px-4 border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            </div>
                        </div>

                        <!-- Product Search -->
                        <div class="relative z-10">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Barang</label>
                            <input list="barangList" type="text" x-model="searchBarang" @change="selectBarang()" placeholder="Ketik nama / kode barang..."
                                class="block w-full py-2.5 px-4 border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50" autocomplete="off">
                            <datalist id="barangList">
                                @foreach($barangList as $b)
                                <option value="{{ $b->kode_barang }} - {{ $b->nama_barang }}" data-id="{{ $b->id_barang }}" data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}">
                                @endforeach
                            </datalist>
                            <input type="hidden" name="id_barang" x-model="selectedBarangId">
                            
                            <!-- Info Bar -->
                            <div x-show="selectedBarangId" class="mt-2 flex items-center gap-2 text-sm bg-blue-50 text-blue-800 px-3 py-2 rounded-lg border border-blue-100 animate-fade-in">
                                <i class="fas fa-info-circle"></i>
                                <span>Stok Saat Ini: <strong x-text="currentStok"></strong> <span x-text="currentSatuan"></span></span>
                            </div>
                        </div>
                        
                        <!-- Detailed Input -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-0.5">Jumlah <span x-text="form.jenis == 'ADJUSTMENT' ? '(Stok Sebenarnya)' : ''" class="text-xs text-indigo-500"></span></label>
                                <p x-show="form.jenis == 'ADJUSTMENT'" class="text-[10px] text-gray-400 mb-1">Masukkan jumlah hasil hitung fisik</p>
                                <input type="number" name="jumlah" x-model.number="form.jumlah" min="1" 
                                    class="block w-full py-2.5 px-4 border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-lg font-bold text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi / Gudang</label>
                                <select name="lokasi" class="block w-full py-2.5 px-4 border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="Gudang Utama">Gudang Utama</option>
                                    <option value="Gudang Depan">Gudang Depan</option>
                                    <option value="Display Toko">Display Toko</option>
                                </select>
                            </div>
                        </div>

                        <!-- Special Fields -->
                        <div x-show="form.jenis == 'TRANSFER'" class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                            <label class="block text-sm font-semibold text-purple-800 mb-2">Ke Gudang Tujuan</label>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-arrow-right text-purple-400"></i>
                                <input type="text" name="tujuan" placeholder="Contoh: Gudang Belakang" 
                                    class="block w-full border-purple-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / No. Referensi</label>
                            <textarea name="keterangan" rows="2" placeholder="Contoh: Pembelian Faktur #1234 atau Retur Pelanggan" 
                                class="block w-full py-2.5 px-4 border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none"></textarea>
                        </div>

                        <!-- Prediction Bar -->
                        <div class="bg-gray-100 p-4 rounded-xl flex justify-between items-center text-sm border border-gray-200">
                            <span class="text-gray-600 font-medium">Estimasi Stok Akhir</span>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-arrow-right text-gray-400"></i>
                                <span class="font-bold text-lg" 
                                    :class="calculateAkhir() < 0 ? 'text-red-500' : 'text-gray-900'" 
                                    x-text="calculateAkhir()"></span>
                                <span class="text-xs text-gray-500" x-text="currentSatuan"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="showModal = false" class="w-full sm:w-auto px-5 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition shadow-sm">
                            Batal
                        </button>
                        <button type="submit" 
                            :disabled="!selectedBarangId || form.jumlah < 1 || (form.jenis == 'KELUAR' && calculateAkhir() < 0)"
                            class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-medium transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Simpan Mutasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function mutasiSystem() {
        return {
            loading: false,
            showModal: false,
            showFilter: false,
            // Search Params
            params: {
                q: '{{ request("q") }}',
                start_date: '{{ request("start_date") }}',
                end_date: '{{ request("end_date") }}',
                jenis: '{{ request("jenis") }}',
                user: '{{ request("user") }}'
            },
            
            // Modal Data
            searchBarang: '',
            selectedBarangId: '',
            currentStok: 0,
            currentSatuan: '',
            form: {
                jenis: 'MASUK',
                tanggal: new Date().toISOString().split('T')[0],
                jumlah: 1
            },
            
            get activeFilterCount() {
                let count = 0;
                if(this.params.start_date) count++;
                if(this.params.end_date) count++;
                if(this.params.jenis) count++;
                if(this.params.user) count++;
                return count;
            },

            getExportUrl() {
                const url = new URL('{{ route("admin.stock.mutasi.export") }}');
                url.searchParams.set('q', this.params.q);
                url.searchParams.set('start_date', this.params.start_date);
                url.searchParams.set('end_date', this.params.end_date);
                url.searchParams.set('jenis', this.params.jenis);
                url.searchParams.set('user', this.params.user);
                return url.toString();
            },

            updateExportButton() {
                // Try to parse total from partial if valid
                // This is a hacky way since partial returns HTML
                // Better approach: Server returns X-Total-Count header
            },

            init() {
                // Listen for pagination clicks
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('.pagination-container a');
                    if (link) {
                        e.preventDefault();
                        this.fetchData(link.href);
                    }
                });
            },

            toggleFilter() {
                this.showFilter = !this.showFilter;
            },

            resetFilters() {
                this.params.start_date = '';
                this.params.end_date = '';
                this.params.jenis = '';
                this.params.user = '';
                this.fetchData();
            },
            
            async fetchData(url = null) {
                this.loading = true;
                
                // Build URL
                let targetUrl;
                if (url) {
                    targetUrl = new URL(url);
                } else {
                    targetUrl = new URL(window.location.href);
                    // Reset page when filtering
                    targetUrl.searchParams.delete('page');
                    
                    targetUrl.searchParams.set('q', this.params.q);
                    targetUrl.searchParams.set('start_date', this.params.start_date);
                    targetUrl.searchParams.set('end_date', this.params.end_date);
                    targetUrl.searchParams.set('jenis', this.params.jenis);
                    targetUrl.searchParams.set('user', this.params.user);
                }

                try {
                    // Update Browser URL (History)
                    window.history.pushState({}, '', targetUrl);
                    
                    const response = await fetch(targetUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const html = await response.text();
                        document.getElementById('table-wrapper').innerHTML = html;
                        // Update total count from hidden element if exists
                        const newTotal = document.getElementById('result-total');
                        if (newTotal) {
                            this.totalRecords = newTotal.innerText;
                        }
                    } else {
                        console.error('Fetch error');
                    }
                } catch (error) {
                    console.error('Error fetching data:', error);
                } finally {
                    this.loading = false;
                }
            },
            
            openModal() {
                this.showModal = true;
                this.form.jenis = 'MASUK';
                this.form.jumlah = 1;
                this.searchBarang = '';
                this.selectedBarangId = '';
                this.currentStok = 0;
            },
            
            selectBarang() {
                const input = this.searchBarang;
                const options = document.getElementById('barangList').options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === input) {
                        this.selectedBarangId = options[i].getAttribute('data-id');
                        this.currentStok = parseInt(options[i].getAttribute('data-stok'));
                        this.currentSatuan = options[i].getAttribute('data-satuan');
                        break;
                    }
                }
            },
            
            calculateAkhir() {
                let current = this.currentStok;
                let qty = this.form.jumlah;
                if (!qty) qty = 0;
                
                if (this.form.jenis == 'MASUK') return current + qty;
                if (this.form.jenis == 'KELUAR' || this.form.jenis == 'TRANSFER') return current - qty;
                if (this.form.jenis == 'ADJUSTMENT') return qty; 
                
                return current;
            }
        }
    }
</script>

<style>
    /* Smooth fades */
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection
