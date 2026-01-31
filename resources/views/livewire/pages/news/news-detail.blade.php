<div>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <article class="lg:col-span-8">
                    @if($noticia->imagen)
                        <div class="relative rounded-2xl overflow-hidden mb-6">
                            <img
                                src="{{ $noticia->imagen }}"
                                alt="{{ $noticia->titulo }}"
                                class="w-full h-[220px] sm:h-[320px] lg:h-[380px] object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                            Noticia
                        </span>
                        @if($noticia->fecha_inicio)
                            <span class="text-sm text-orange-hn font-semibold">
                                {{ \Carbon\Carbon::parse($noticia->fecha_inicio)->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6">
                        <span class="flex items-center gap-1">
                            <x-lucide-user class="w-4 h-4" />
                            Hospital Nacional
                        </span>
                        @if($noticia->fecha_inicio)
                            <span class="flex items-center gap-1">
                                <x-lucide-calendar class="w-4 h-4" />
                                {{ \Carbon\Carbon::parse($noticia->fecha_inicio)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-gray-900 mb-6">
                        {{ $noticia->titulo }}
                    </h1>

                    <div class="prose max-w-none prose-gray">
                        <p class="text-lg text-gray-700 leading-relaxed">
                            {{ $noticia->descripcion }}
                        </p>
                    </div>
                </article>

                <aside class="lg:col-span-4 space-y-6">
                    <div class="bg-gradient-to-br from-green-hn to-green-600 text-white rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold mb-2">
                            Comunicación institucional
                        </h3>
                        <p class="text-sm opacity-90 mb-4">
                            Mantente informado sobre actividades, comunicados y eventos oficiales del Hospital Nacional.
                        </p>
                        <a href="{{ url('/news') }}"
                           class="inline-flex items-center gap-2 bg-white text-green-hn font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                            Ver todas las noticias
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </a>
                    </div>

                    @if($otrasNoticias && $otrasNoticias->count() > 0)
                        <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                            <div class="px-5 py-4 border-b">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                                    Otras noticias
                                </h3>
                            </div>

                            <ul class="divide-y">
                                @foreach($otrasNoticias as $otraNoticia)
                                    <li>
                                        <a href="{{ url('/news/' . $otraNoticia->codigo) }}" class="flex items-start gap-3 px-5 py-4 hover:bg-gray-50 transition">
                                            <span class="mt-1">
                                                <x-lucide-arrow-right class="w-4 h-4 text-green-hn" />
                                            </span>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-900">
                                                    {{ Illuminate\Support\Str::limit($otraNoticia->titulo, 50, '...') }}
                                                </h4>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ Illuminate\Support\Str::limit($otraNoticia->descripcion, 80, '...') }}
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>
            </div>
    </section>
</div>
