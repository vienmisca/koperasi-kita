@extends('layouts.admin')

@section('content')
<div x-data="reportAnalytics()" x-init="init()" class="space-y-8 animate-fade-in-down">

    <!-- Header & Interactive Filter -->
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Laporan & Analisis</h1>
            <p class="text-gray-500 mt-2 text-lg">Analisis performa penjualan, margin keuntungan, dan export data.</p>
        </div>
        
        <!-- Filter Bar Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5 items-end">
                
                <!-- Date Range -->
                <div class="lg:col-span-3 flex flex-col gap-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Periode Laporan</label>
                    <div class="flex items-center gap-2 bg-gray-50 p-1.5 border border-gray-200 rounded-xl focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-400 transition-all">
                        <input type="date" x-model="filters.start_date" class="border-0 bg-transparent text-sm focus:ring-0 text-gray-700 w-full font-medium">
                        <span class="text-gray-400 font-bold">→</span>
                        <input type="date" x-model="filters.end_date" class="border-0 bg-transparent text-sm focus:ring-0 text-gray-700 w-full font-medium">
                    </div>
                </div>

                <!-- Kasir -->
                <div class="lg:col-span-2 flex flex-col gap-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Petugas Kasir</label>
                    <select x-model="filters.user_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all cursor-pointer hover:bg-gray-100">
                        <option value="">Semua Kasir</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Metode Bayar -->
                <div class="lg:col-span-2 flex flex-col gap-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Metode Bayar</label>
                    <select x-model="filters.metode_bayar" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all cursor-pointer hover:bg-gray-100">
                        <option value="">Semua Metode</option>
                        <option value="tunai">Tunai / Cash</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="lg:col-span-3 flex flex-col gap-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Cari Transaksi</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" x-model="filters.q" placeholder="No Invoice / Pelanggan..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all">
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="lg:col-span-2 flex gap-2">
                    <a :href="getExportUrl()" target="_blank" class="flex-1 flex justify-center items-center gap-2 bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-700 active:scale-95 transition-all shadow-sm hover:shadow-emerald-200" title="Download Excel">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export
                    </a>
                    <button @click="fetchCharts()" class="flex-1 flex justify-center items-center gap-2 bg-white text-indigo-600 border border-indigo-200 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-50 hover:border-indigo-300 active:scale-95 transition-all shadow-sm" title="Refresh Data">
                         <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" style="display:none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards (Static Data from Controller) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs text-green-600 font-medium">
                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Accumulated</span>
            </div>
        </div>

        <!-- Total Profit -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Keuntungan Bersih</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400">Estimasi margin: <span class="text-gray-600 font-medium">{{ $totalPendapatan > 0 ? round(($totalKeuntungan / $totalPendapatan) * 100, 1) : 0 }}%</span></p>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalTransaksi) }}</h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400">Rata-rata: <span class="text-gray-600 font-medium">Rp {{ $totalTransaksi > 0 ? number_format($totalPendapatan / $totalTransaksi, 0, ',', '.') : 0 }}</span> / trx</p>
        </div>

        <!-- Items Sold -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Barang Terjual</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalBarangTerjual) }} <span class="text-sm font-normal text-gray-500">pcs</span></h3>
                </div>
                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400">Inventory Turnover: <span class="text-green-600 font-medium">High</span></p>
        </div>
    </div>

    <!-- ANALYTICS CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-show="hasData">
        
        <!-- Chart 1: Sales Trend (Wider) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                Tren Penjualan (Periode Terpilih)
            </h3>
            <div class="relative h-72">
                 <canvas id="reportSalesTrend"></canvas>
                 <div x-show="loading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10 transition-opacity">
                    <div class="flex items-center gap-2 text-indigo-600 font-medium">
                        <span class="animate-spin text-xl">✨</span> Memuat data...
                    </div>
                 </div>
            </div>
        </div>

        <!-- Chart 2: Sales by Category (Narrower) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-1">
             <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-purple-500 rounded-full"></span>
                Proporsi Penjualan per Kategori
            </h3>
            <div class="relative h-64 flex justify-center">
                 <canvas id="reportCatPie"></canvas>
                 <div x-show="loading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10"></div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
     <div x-show="!loading && !hasData" class="py-12 text-center bg-white rounded-2xl border border-dashed border-gray-300">
        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> 
        <p class="mt-2 text-gray-500 font-medium">Tidak ada data transaksi untuk periode ini.</p>
    </div>

    <!-- SALES HISTORY (Laporan Penjualan) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">Riwayat Penjualan</h3>
            <div x-show="tableLoading" class="text-sm text-indigo-600 font-medium animate-pulse">
                Memuat data...
            </div>
        </div>
        
        <!-- Table Container for AJAX Replacement -->
        <div id="sales-table-container" x-init="attachPaginationListeners()">
             @include('admin.partials.laporan-table')
        </div>
    </div>
</div>

<script>
    function reportAnalytics() {
        return {
            filters: {
                start_date: '{{ date("Y-m-d", strtotime("-30 days")) }}',
                end_date: '{{ date("Y-m-d") }}',
                user_id: '',
                metode_bayar: '',
                q: ''
            },
            loading: false,
            tableLoading: false,
            hasData: true,
            charts: {},

            getExportUrl() {
                const url = new URL('{{ route("admin.laporan.export") }}');
                url.searchParams.set('start_date', this.filters.start_date);
                url.searchParams.set('end_date', this.filters.end_date);
                if(this.filters.user_id) url.searchParams.set('user_id', this.filters.user_id);
                if(this.filters.metode_bayar) url.searchParams.set('metode_bayar', this.filters.metode_bayar);
                if(this.filters.q) url.searchParams.set('q', this.filters.q);
                return url.toString();
            },

            init() {
                this.fetchCharts();
                // Initial table is loaded by Blade, but we want to make pagination inputs work via AJAX
                // So we attach listener to the table container
                
                // Watch for filter changes to auto-reload
                this.$watch('filters.start_date', () => this.debouncedFetch());
                this.$watch('filters.end_date', () => this.debouncedFetch());
                this.$watch('filters.user_id', () => this.debouncedFetch());
                this.$watch('filters.metode_bayar', () => this.debouncedFetch());
                this.$watch('filters.q', () => this.debouncedFetch());
            },

            debouncedFetch: _.debounce(function() {
                this.fetchCharts();
                this.fetchTable(1);
            }, 500),

            async fetchCharts() {
                // ... Existing chart fetch logic ...
                if (this.loading) return; 
                this.loading = true;
                this.hasData = true;
                const params = new URLSearchParams(this.filters).toString();
                
                try {
                    const res = await fetch(`{{ route('admin.laporan.stats') }}?${params}`);
                    if (!res.ok) throw new Error('Network response check failed');
                    const data = await res.json();
                    
                    this.hasData = data.has_data;

                    if (this.hasData) {
                         this.$nextTick(() => {
                            this.renderSalesTrend(data.sales_trend);
                            this.renderCatPie(data.sales_by_category);
                        });
                    }
                } catch (e) {
                    this.hasData = false;
                } finally {
                    this.loading = false;
                }
            },

            async fetchTable(page = 1) {
                this.tableLoading = true;
                // Add table_only param to tell controller to return partial
                const params = new URLSearchParams({ 
                    ...this.filters, 
                    page: page, 
                    table_only: 1 
                }).toString();

                try {
                    const res = await fetch(`{{ route('admin.laporan') }}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await res.text();
                    
                    document.getElementById('sales-table-container').innerHTML = html;
                    
                    // Re-attach pagination listeners after DOM update
                    this.attachPaginationListeners();

                } catch (e) {
                    console.error('Table fetch error:', e);
                } finally {
                    this.tableLoading = false;
                }
            },

            attachPaginationListeners() {
                const container = document.getElementById('sales-table-container');
                // Laravel Tailwind Pagination uses <nav role="navigation">...<a href="...">
                const links = container.querySelectorAll('nav[role="navigation"] a');
                
                links.forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const url = new URL(link.href);
                        const page = url.searchParams.get('page');
                        if (page) {
                            console.log('Navigating to page:', page);
                            this.fetchTable(page);
                        }
                    });
                });
            },
            
            // ... render methods ...
            renderSalesTrend(data) {
                const el = document.getElementById('reportSalesTrend');
                if(!el) return;
                const ctx = el.getContext('2d');
                if (this.charts.trend) this.charts.trend.destroy();

                this.charts.trend = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Pendapatan',
                            data: data.data,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            },

            renderCatPie(data) {
                const el = document.getElementById('reportCatPie');
                if(!el) return;
                const ctx = el.getContext('2d');
                if (this.charts.cat) this.charts.cat.destroy();

                this.charts.cat = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }
        }
    }
</script>
@endsection
