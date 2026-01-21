@extends('layouts.admin')

@section('content')
<style>
    /* Custom Scrollbar for Modals and Tables */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8; 
    }
    [x-cloak] { display: none !important; }
</style>

<div x-data="stockSystem()" x-init="init()" class="min-h-screen bg-gray-50 p-6">
    
    <!-- HEADER & ACTIONS -->
    @include('stock.partials.actions')

    <!-- STATS GRID -->
    @include('stock.partials.stats')

    <!-- TABLE LIST -->
    @include('stock.partials.table')

    <!-- MODALS -->
    @include('stock.partials.modals.create-stock')
    @include('stock.partials.modals.edit-stock')
    @include('stock.partials.modals.create-category')

</div>

<!-- SCRIPTS -->
@include('stock.partials.scripts')

@endsection