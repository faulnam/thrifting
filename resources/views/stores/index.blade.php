@extends('layouts.app')

@section('title', 'Lokasi Toko Fisik fifa — Temukan Gerai Terdekat')

@section('content')
<!-- Header Hero -->
<div class="bg-sand/20 border-b border-sand py-12 sm:py-16 text-center">
    <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8">
        <span class="text-caption font-bold uppercase tracking-wide10 text-stone block mb-2">Lokasi Toko Fisik</span>
        <h1 class="font-display font-normal text-3xl sm:text-4xl lg:text-5xl text-charcoal tracking-tight">
            Temukan Toko fifa
        </h1>
        <p class="text-body text-iron max-w-xl mx-auto mt-4 leading-relaxed">
            Kunjungi langsung gerai fisik FIFA Vintage Vault untuk melihat koleksi grail 1-of-1, mencoba fitting ukuran PxL nyata, dan merasakan kualitas bahan vintage otentik.
        </p>
    </div>
</div>

<div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <!-- City Filter Tabs -->
    @if ($cities->isNotEmpty())
        <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
            <a href="{{ route('stores.index') }}" class="px-5 py-2 rounded-pill text-caption font-medium {{ !request('city') ? 'bg-charcoal text-canvas' : 'bg-sand/30 text-charcoal hover:bg-sand' }}">
                Semua Kota ({{ $stores->count() }})
            </a>
            @foreach ($cities as $cityName)
                <a href="{{ route('stores.index', ['city' => $cityName]) }}" class="px-5 py-2 rounded-pill text-caption font-medium {{ request('city') === $cityName ? 'bg-charcoal text-canvas' : 'bg-sand/30 text-charcoal hover:bg-sand' }}">
                    {{ $cityName }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- Stores Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        @forelse ($stores as $store)
            <div class="bg-canvas border border-sand rounded-card p-6 sm:p-8 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-caption font-bold uppercase tracking-wide10 text-stone px-2.5 py-1 rounded-pill bg-sand/30">
                            {{ $store->city }}
                        </span>
                        <span class="text-caption text-green-700 font-bold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Buka
                        </span>
                    </div>

                    <h2 class="font-sans font-bold text-xl text-charcoal">
                        {{ $store->name }}
                    </h2>

                    <div class="text-body-sm text-iron space-y-2">
                        <p class="leading-relaxed">{{ $store->address }}</p>
                        
                        @if ($store->phone)
                            <p class="flex items-center gap-2 text-stone">
                                <span>📞</span> {{ $store->phone }}
                            </p>
                        @endif

                        <p class="flex items-center gap-2 text-stone text-caption">
                            <span>🕒</span> {{ $store->opening_hours ?: 'Setiap hari 10:00 - 22:00 WIB' }}
                        </p>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-sand">
                    @php
                        $mapsUrl = ($store->latitude && $store->longitude) 
                            ? "https://www.google.com/maps/search/?api=1&query={$store->latitude},{$store->longitude}" 
                            : "https://www.google.com/maps/search/?api=1&query=" . urlencode($store->name . ' ' . $store->address);
                    @endphp
                    <a href="{{ $mapsUrl }}" target="_blank" class="btn-pill-dark text-caption w-full py-2.5 flex items-center justify-center gap-2 text-center">
                        <span>Petunjuk Arah (Google Maps) ↗</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-stone bg-sand/10 rounded-card border border-sand">
                <p class="text-body font-medium">Belum ada data toko untuk kota ini.</p>
                <a href="{{ route('stores.index') }}" class="btn-pill-light text-caption px-5 py-2 mt-4 inline-block">
                    Lihat Semua Toko
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
