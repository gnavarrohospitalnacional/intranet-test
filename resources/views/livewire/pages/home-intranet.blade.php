@php
    $assets = $assets ?? [];
    $hero = $assets['hero'] ?? null;
    $logo = $assets['logo'] ?? null;
    $images1 = $assets['images1'] ?? null;
    $social = [
        'facebook' => $assets['facebook'] ?? null,
        'instagram' => $assets['instagram'] ?? null,
        'youtube' => $assets['youtube'] ?? null,
        'linkedin' => $assets['linkedin'] ?? null,
    ];
    view()->share('social', $social);
@endphp
<div>
    {{-- Hero ( carousel ) --}}
    <section class="max-w-7xl mx-auto px-6 mt-6">
        <div id="gallery" class="relative w-full" data-carousel="slide" data-carousel-interval="10000">
            <!-- Carousel wrapper -->
            <div class="relative h-56 overflow-hidden rounded-lg md:h-120">
                <!-- Item 1 -->
                @if(!empty($carouselNoticias) && count($carouselNoticias) > 0)
                    @foreach($carouselNoticias as $n)
                        <div class="{{ $loop->first ? 'block' : 'hidden' }} duration-2000 ease-in-out" data-carousel-item="{{ $loop->first ? 'active' : '' }}">
                            <img src="{{ $n['imagen'] ?? ($n->imagen ?? '') }}"
                                class="absolute block w-full h-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 object-cover" alt="{{ $n['titulo'] ?? ($n->titulo ?? '') }}">
                        </div>
                    @endforeach
                @else
                    <div class="hidden duration-2000 ease-in-out" data-carousel-item>
                        <img src="{{ $hero }}"
                            class="absolute block w-full h-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="">
                    </div>
                @endif
            </div>
            <!-- Slider controls -->
            <button type="button"
                class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-prev>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m15 19-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Atras</span>
                </span>
            </button>
            <button type="button"
                class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-next>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m9 5 7 7-7 7" />
                    </svg>
                    <span class="sr-only">Siguiente</span>
                </span>
            </button>
        </div>
    </section>
    <div class="max-w-7xl mx-auto px-6 py-3 grid grid-cols-1">
        <x-home.home-ethics-section />
    </div>
    {{-- Main layout --}}
    <div class="max-w-7xl mx-auto px-6 py-3 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <livewire:news-section :images1="$images1" :news="$news" />
                <x-home.home-quick-access-section :shortcuts="$shortcuts" :hostname="$hostname" />
            </div>
            </aside>
        </div>
    </div>
</div>    