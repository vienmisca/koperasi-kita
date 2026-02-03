@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
            <p class="text-gray-500 mt-1">Konfigurasi informasi koperasi dan parameter sistem.</p>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
        class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Navigation / Info (Optional context or just Sticky summary) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Card Info Toko -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg">Identitas Koperasi</h3>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Informasi ini akan ditampilkan pada header struk belanja dan laporan resmi yang dicetak. Pastikan data valid.
                    </p>
                </div>

                <!-- Card System Config -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg">Parameter Sistem</h3>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Pengaturan teknis yang mempengaruhi perilaku aplikasi, seperti notifikasi stok.
                    </p>
                </div>
            </div>

            <!-- Right Column: Form Inputs -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Section 1: Store Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="font-bold text-lg text-gray-800">Detail Koperasi</h2>
                        <span class="text-xs font-semibold bg-indigo-100 text-indigo-700 px-2 py-1 rounded">Umum</span>
                    </div>
                    <div class="p-6 space-y-5">
                        @foreach($settings as $setting)
                            @if(in_array($setting->key, ['toko_nama', 'toko_telepon', 'toko_alamat']))
                                <div>
                                    <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ $setting->label }}
                                    </label>
                                    <div class="relative">
                                        <!-- Icons based on key -->
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            @if($setting->key == 'toko_nama') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            @elseif($setting->key == 'toko_telepon') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            @elseif($setting->key == 'toko_alamat') <svg class="w-5 h-5 mt-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            @endif
                                        </div>

                                        @if($setting->type === 'textarea')
                                            <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3"
                                                class="w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">{{ $setting->value }}</textarea>
                                        @else
                                            <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}"
                                                class="w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Section 2: Configuration -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="font-bold text-lg text-gray-800">Konfigurasi</h2>
                        <span class="text-xs font-semibold bg-purple-100 text-purple-700 px-2 py-1 rounded">System</span>
                    </div>
                    <div class="p-6 space-y-5">
                        @foreach($settings as $setting)
                            @if(!in_array($setting->key, ['toko_nama', 'toko_telepon', 'toko_alamat']) && $setting->type !== 'hidden')
                                <div>
                                    <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ $setting->label }}
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-full md:w-1/2">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                            </div>
                                            <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}"
                                                class="w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                        </div>
                                        @if($setting->key == 'stok_min_default')
                                            <span class="text-sm text-gray-500">Unit (Pcs)</span>
                                        @endif
                                    </div>
                                    @if($setting->key == 'stok_min_default')
                                        <div class="mt-2 flex items-start gap-2 text-xs text-amber-600 bg-amber-50 p-2 rounded-lg border border-amber-100">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p>Jika stok barang turun di bawah angka ini, sistem akan memberikan notifikasi "Low Stock" di dashboard.</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="reset" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition-all shadow-sm">
                        Reset Data
                    </button>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
