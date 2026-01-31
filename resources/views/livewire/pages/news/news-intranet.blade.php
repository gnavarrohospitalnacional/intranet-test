<div>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">
                Noticias y Comunicados
            </h1>
            <p class="text-lg text-gray-600">
                Mantente informado sobre las últimas actividades y comunicados del Hospital Nacional.
            </p>
        </div>
        @if($noticias && $noticias->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($noticias as $noticia)
                    <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <a href="{{ url('/news/' . $noticia->codigo) }}" class="block">
                            @if($noticia->imagen)
                                <div class="relative h-48 overflow-hidden bg-gray-200">
                                    <img 
                                        src="{{ $noticia->imagen }}" 
                                        alt="{{ $noticia->titulo }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-green-hn to-green-600 flex items-center justify-center">
                                    <x-lucide-newspaper class="w-16 h-16 text-white/50" />
                                </div>
                            @endif
                            <div class="p-5">
                                @if($noticia->fecha_inicio)
                                    <div class="flex items-center gap-1 text-xs text-gray-500 mb-3">
                                        <x-lucide-calendar class="w-3 h-3" />
                                        <time>{{ \Carbon\Carbon::parse($noticia->fecha_inicio)->format('d/m/Y') }}</time>
                                    </div>
                                @endif

                                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-green-hn transition-colors">
                                    {{ $noticia->titulo }}
                                </h3>

                                
                                <p class="text-sm text-gray-600 line-clamp-3 mb-4">
                                    {{ $noticia->descripcion }}
                                </p>

                                
                                <div class="flex items-center gap-2 text-green-hn font-medium text-sm">
                                    <span>Leer más</span>
                                    <x-lucide-arrow-right class="w-4 h-4" />
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>


            <div class="mt-8">
                {{ $noticias->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <x-lucide-inbox class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    No hay noticias disponibles
                </h3>
                <p class="text-gray-600">
                    En este momento no hay noticias publicadas. Vuelve pronto para ver las últimas actualizaciones.
                </p>
            </div>
        @endif
    </section>
</div>
