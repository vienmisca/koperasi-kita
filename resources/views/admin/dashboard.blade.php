@extends('layouts.admin')

@section('content')
<div x-data="dashboardStats()" x-init="init()" class="space-y-6 animate-fade-in-down">
    
    <!-- Welcome Header & Date Filter -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ringkasan Dashboard</h1>
            <p class="text-gray-500 mt-1">Ringkasan penjualan, transaksi, dan kondisi stok</p>
        </div>
        <div class="flex items-center gap-3">
            <select x-model="range" @change="fetchStats()" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                <option value="7">7 Hari Terakhir</option>
                <option value="14">14 Hari Terakhir</option>
                <option value="30">30 Hari Terakhir</option>
            </select>
            <div class="hidden md:block">
                <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg font-medium text-sm border border-indigo-100">
                    📅 {{ date('d M Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- KPI Cards (Realtime + Calculated) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Stok -->
        <div title="Total semua barang yang masih ada" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center relative z-10">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium">Total Stok Barang</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalStok) }}</h3>
            </div>
        </div>
        
        <!-- Card 2: Pendapatan -->
        <div title="Jumlah uang dari penjualan hari ini" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center relative z-10">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium">Total Penjualan Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Card 3: Margin % (Dynamic) -->
        <div title="Persen keuntungan dari penjualan" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center relative z-10">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium">Margin Laba</p>
                <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <span x-text="kpi.margin + '%'">0%</span>
                    <span class="text-xs font-normal px-2 py-0.5 rounded-full bg-green-100 text-green-700" x-show="kpi.margin > 0">+ROI</span>
                </h3>
            </div>
        </div>

         <!-- Card 4: Avg Transaksi -->
        <div title="Rata-rata jumlah transaksi per hari" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow relative overflow-hidden group">
             <div class="absolute right-0 top-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center relative z-10">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium">Rata-rata Transaksi Harian</p>
                <h3 class="text-2xl font-bold text-gray-800" x-text="kpi.avg_trans + ' /hari'">0</h3>
            </div>
        </div>
    </div>

    <!-- CHARTS ROW 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-show="hasData">
        <!-- Sales Trend Line Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                Tren Penjualan
            </h3>
            <div class="relative h-72">
                <canvas id="salesTrendChart"></canvas>
                <!-- Skeleton Loader -->
                <div x-show="loading" class="absolute inset-0 bg-white flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                </div>
            </div>
        </div>

        <!-- Revenue vs Profit Bar Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
             <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-green-500 rounded-full"></span>
                Penjualan dan Laba
            </h3>
            <div class="relative h-72">
                <canvas id="profitChart"></canvas>
                <div x-show="loading" class="absolute inset-0 bg-white flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && !hasData" class="py-12 text-center bg-white rounded-2xl border border-dashed border-gray-300">
        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <p class="mt-2 text-gray-500 font-medium">Belum ada data visual untuk periode ini.</p>
    </div>

    <!-- Recent Activity & Low Stock -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-800">Transaksi Terbaru</h3>
                <a href="{{ route('admin.laporan') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">Lihat Laporan Lengkap →</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase border-b border-gray-50">
                            <th class="pb-3 pl-2 font-medium">Petugas</th>
                            <th class="pb-3 font-medium">No. Transaksi</th>
                            <th class="pb-3 font-medium">Total Nilai</th>
                            <th class="pb-3 font-medium">Waktu</th>
                            <th class="pb-3 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($recentActivities as $activity)
                         <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                            <td class="py-4 pl-2 font-medium text-gray-800">{{ $activity->user->name ?? 'Unknown' }}</td>
                            <td class="py-4 text-gray-500 font-mono">{{ $activity->no_penjualan }}</td>
                            <td class="py-4 text-gray-800 font-bold">Rp {{ number_format($activity->total, 0, ',', '.') }}</td>
                            <td class="py-4 text-gray-400 text-xs">{{ $activity->created_at->diffForHumans() }}</td>
                            <td class="py-4 text-right">
                                <span class="px-2.5 py-1 
                                    {{ $activity->status == 'selesai' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} 
                                    rounded-full text-xs font-medium uppercase">
                                    {{ $activity->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Belum ada aktivitas.</td>
                        </tr>
                        @endforelse
                     </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Low Stock Alerts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
             <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-800">Peringatan Stok Minimum</h3>
                @if($lowStockItems->count() > 0)
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                @endif
            </div>
            
            <div class="space-y-4">
                @forelse($lowStockItems as $item)
                <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm font-bold">!</div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ $item->nama_barang }}</h4>
                        <p class="text-xs text-red-500 font-medium">Sisa Stok: {{ $item->stok }} {{ $item->satuan ?? 'pcs' }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <p class="text-sm">Stok aman.</p>
                </div>
                @endforelse
                
                 <div class="text-center mt-4">
                     <a href="{{ route('admin.stock.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat Detail Stok &rarr;</a>
                 </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboardStats() {
        return {
            range: '7',
            loading: false,
            hasData: true, // Default true to prevent blinking empty state
            kpi: { margin: 0, avg_trans: 0 },
            charts: {},
            requestId: 0, // Request Guard

            init() {
                // Prevent double fetch if x-init and watcher collide
                this.fetchStats();
            },

            async fetchStats() {
                // Request Guard: Increment ID
                const currentId = ++this.requestId;
                
                this.loading = true;
                // DO NOT reset this.hasData = true here to avoid flickering empty state if it was false
                
                console.log(`[Fetch ${currentId}] Starting... Range: ${this.range}`);

                try {
                    const res = await fetch(`{{ route('admin.dashboard.stats') }}?range=${this.range}`);
                    
                    if (!res.ok) throw new Error('Network response was not ok');
                    const data = await res.json();
                    
                    // Race Condition Check: If newer request started, ignore this one
                    if (currentId !== this.requestId) {
                        console.warn(`[Fetch ${currentId}] Ignored (Stale). Current: ${this.requestId}`);
                        return;
                    }

                    console.log(`[Fetch ${currentId}] Success. Records: ${data.dates.length}`);

                    this.kpi.margin = data.kpi.margin || 0;
                    this.kpi.avg_trans = data.kpi.avg_trans || 0;
                    
                    // Only update hasData state based on actual response
                    this.hasData = data.has_data;

                    if (this.hasData) {
                        this.$nextTick(() => {
                            this.renderSalesChart(data);
                            this.renderProfitChart(data);
                        });
                    }

                } catch (e) {
                    if (currentId === this.requestId) {
                        console.error(`[Fetch ${currentId}] Error:`, e);
                        // Do NOT force hasData=false on error, keep previous state or show specific error
                        // For now, if error, we might want to keep showing what we have or nothing.
                    }
                } finally {
                    if (currentId === this.requestId) {
                        this.loading = false;
                    }
                }
            },

            renderSalesChart(data) {
                const el = document.getElementById('salesTrendChart');
                if(!el) return;
                
                // Update Existing Chart (Prevent flickering)
                if (this.charts.sales) {
                    this.charts.sales.data.labels = data.dates;
                    this.charts.sales.data.datasets[0].data = data.transactions;
                    this.charts.sales.data.datasets[1].data = data.revenue;
                    this.charts.sales.update();
                    return;
                }

                const ctx = el.getContext('2d');
                this.charts.sales = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.dates,
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: data.transactions,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        }, {
                            label: 'Nilai Penjualan (Rp)',
                            data: data.revenue,
                            borderColor: '#10b981',
                            borderWidth: 2,
                            tension: 0.4,
                            borderDash: [5, 5],
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            y: { type: 'linear', display: true, position: 'left', beginAtZero: true },
                            y1: { type: 'linear', display: false, position: 'right', grid: { drawOnChartArea: false } }
                        }
                    }
                });
            },

            renderProfitChart(data) {
                const el = document.getElementById('profitChart');
                if(!el) return;

                // Update Existing Chart
                if (this.charts.profit) {
                    this.charts.profit.data.labels = data.dates;
                    this.charts.profit.data.datasets[0].data = data.revenue;
                    this.charts.profit.data.datasets[1].data = data.profit;
                    this.charts.profit.update();
                    return;
                }

                const ctx = el.getContext('2d');
                this.charts.profit = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.dates,
                        datasets: [{
                            label: 'Penjualan',
                            data: data.revenue,
                            backgroundColor: '#10b981',
                            borderRadius: 4
                        }, {
                            label: 'Laba',
                            data: data.profit,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        }
    }
</script>
@endsection