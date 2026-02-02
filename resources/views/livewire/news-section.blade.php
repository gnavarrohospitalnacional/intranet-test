<div class="lg:col-span-2 bg-white rounded shadow p-6">
    <div class="flex">
        <h4 class="text-xl font-bold text-orange-hn">Noticias</h4>
    </div>
    
    <div class="flex items-center justify-between mt-5 mb-5">
        <div class="flex gap-2">
            {{-- Tipos Todas --}}
            <button
                type="button"
                wire:click="seleccionarTipo('all')"
                class="py-2 px-5 rounded border text-sm cursor-pointer transition-colors duration-300
                    {{ $tipoSeleccionado === 'all'
                    ? 'text-green-hn border-green-hn'
                    : 'border-gray-200 hover:border-green-hn' }}">
                TodasS
            </button>

            {{-- Tipos dinámicos --}}
            @foreach($tiposEventos as $tipo)
                <button
                    type="button"
                    wire:key="tipo-{{ $tipo->codigo }}"
                    wire:click="seleccionarTipo('{{ $tipo->codigo }}')"
                    class="py-2 px-5 rounded border text-sm cursor-pointer transition-colors duration-300
                        {{ $tipoSeleccionado == $tipo->codigo
                        ? 'text-green-hn border-green-hn'
                        : 'border-gray-200 hover:border-green-hn' }}">
                    {{ $tipo->titulo }}
                </button>
            @endforeach
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="main-news">
        <div class="col-span-1">
            <img src="{{ $noticias->first()->imagen }}" alt="news" class="w-full h-40 object-cover rounded mb-3" />
            <h5 class="font-bold text-[#f16722]">{{ Illuminate\Support\Str::limit($noticias->first()->titulo, 40, '...') }}</h5>
            <p class="text-sm text-gray-600 mt-2 mb-2">{{ Illuminate\Support\Str::limit($noticias->first()->descripcion, 120, '...') }}</p>
            <a class="btn btn-secondary mt-5" href="{{ url('/news/' . $noticias->first()->codigo) }}">Leer noticia</a>
        </div>

        <div class="col-span-1 flex flex-col gap-3" id="other-news">
            @foreach($noticias->skip(1) as $noticia)
                <a href="{{ url('/news/' . $noticia->codigo) }}" class="flex items-center justify-between border border-gray-200 rounded p-3 mb-2 hover:border-green-hn cursor-pointer transition-colors duration-300">
                    <div>
                        <div class="text-sm font-semibold text-orange-hn">{{ Illuminate\Support\Str::limit($noticia->titulo, 40, '...') }}</div>
                        <div class="text-xs text-gray-500">{{ Illuminate\Support\Str::limit($noticia->descripcion, 50, '...') }}</div>
                    </div>
                    <div class="text-green-500 text-xl">&rarr;</div>
                </a>
            @endforeach
        </div>
    </div>
</div>