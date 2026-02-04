@extends('layouts.admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6 animate-fade-in-down">
    
    <!-- Welcome Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-gray-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}! Berikut ringkasan hari ini.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden md:flex flex-col items-end">
                <span class="text-[10px] text-gray-400 font-medium tracking-wide">Terakhir diperbarui: {{ date('H:i') }}</span>
            </div>
            <form action="{{ route('admin.dashboard') }}" method="GET">
                <select name="period" onchange="this.form.submit()" class="bg-indigo-50 border-none text-indigo-700 text-xs font-bold rounded-xl py-2.5 pl-4 pr-8 cursor-pointer focus:ring-2 focus:ring-indigo-100 outline-none hover:bg-indigo-100 transition-colors">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Stok -->
        <a href="{{ route('stock.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-lg hover:border-blue-100 transition-all cursor-pointer relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-0 group-hover:opacity-10 transition-opacity">
                <svg class="w-24 h-24 text-blue-600 transform translate-x-4 -translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium group-hover:text-blue-600 transition-colors">Total Stok Barang</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalStok) }}</h3>
            </div>
        </a>
        
        <!-- Card 2: Pendapatan -->
        <a href="{{ route('kasir.laporan') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-lg hover:border-green-100 transition-all cursor-pointer relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-0 group-hover:opacity-10 transition-opacity">
                <svg class="w-24 h-24 text-green-600 transform translate-x-4 -translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium group-hover:text-green-600 transition-colors">
                    {{ $period == 'today' ? 'Pendapatan Hari Ini' : ($period == 'week' ? 'Omzet 7 Hari' : 'Omzet Bulan Ini') }}
                </p>
                <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($pendapatan, 0, ',', '.') }}</h3>
            </div>
        </a>

        <!-- Card 3: Transaksi -->
        <a href="{{ route('kasir.laporan') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-lg hover:border-purple-100 transition-all cursor-pointer relative overflow-hidden">
             <div class="absolute right-0 top-0 opacity-0 group-hover:opacity-10 transition-opacity">
                <svg class="w-24 h-24 text-purple-600 transform translate-x-4 -translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium group-hover:text-purple-600 transition-colors">
                    {{ $period == 'today' ? 'Transaksi Hari Ini' : ($period == 'week' ? 'Transaksi 7 Hari' : 'Transaksi Bulan Ini') }}
                </p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($transaksiCount) }}</h3>
            </div>
        </a>

        <!-- Card 4: Produk Terjual -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
             <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Produk Terjual</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($produkTerjual) }} <span class="text-sm font-normal text-gray-500">item</span></h3>
            </div>
        </div>
    </div>



    <!-- Main Chart Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-lg text-gray-800 mb-4">Tren Penjualan</h3>
        <div class="relative h-64 w-full" x-data="{ hasData: {{ count($chartData) > 0 && array_sum($chartData) > 0 ? 'true' : 'false' }} }">
            <template x-if="!hasData">
                 <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-sm font-medium">Belum ada data penjualan</span>
                </div>
            </template>
            <canvas id="salesChart" x-show="hasData"></canvas>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-800">Aktivitas Penjualan Terbaru</h3>
                <a href="{{ route('kasir.laporan') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">Lihat Semua</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase border-b border-gray-50">
                            <th class="pb-3 pl-2 font-medium">User</th>
                            <th class="pb-3 font-medium">No. Penjualan</th>
                            <th class="pb-3 font-medium">Total</th>
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
                <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                    Peringatan Stok 
                    @if($lowStockCount > 0)
                        <span class="bg-red-100 text-red-600 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border border-red-200 uppercase tracking-wide">{{ $lowStockCount }} Item</span>
                    @endif
                </h3>
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
                     <a href="{{ route('stock.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua stok &rarr;</a>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only render if we have data
        if (!{{ count($chartData) > 0 && array_sum($chartData) > 0 ? 'true' : 'false' }}) return;

        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Gradient
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)'); // Indigo
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($chartData),
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3 // Smooth curves
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { size: 13 },
                        bodyFont: { size: 13, weight: 'bold' },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const value = 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                const count = @json($chartCount)[context.dataIndex];
                                return value + ' (' + count + ' Transaksi)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            callback: function(value) {
                                if(value >= 1000000) return (value/1000000) + 'jt';
                                if(value >= 1000) return (value/1000) + 'rb';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush