<div>
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Header --}}
        <div class="mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                <div id="Columna1" class="lg:col-span-2">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-gray-900">
                        Accesos Sistema Hospitalario
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-gray-500">
                        Puntos de acceso a las Apps Hospitalarias como Soul MV, PEP y HNApp
                    </p>
                </div>
                <div id="Columna2" class="lg:col-span-1">
                    {{-- Filters --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 ml-auto max-w-md">
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Ambiente --}}
                            <div>
                                <label class="sr-only">Ambiente</label>
                                <select wire:model.live="selectedAmbiente" class="w-full rounded-lg border-gray-200 text-sm focus:border-orange-hn focus:ring-orange-hn">
                                    @foreach($ambientes as $ambiente)
                                        <option value="{{ $ambiente['codigo'] }}">{{ $ambiente['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search --}}
                            <div class="relative">
                                <x-lucide-search class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchTerm"
                                    placeholder="Busca una Aplicación"
                                    class="w-full pl-10 rounded-lg border-gray-200 text-sm focus:border-orange-hn focus:ring-orange-hn"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 h-1 w-full bg-gradient-to-r from-orange-hn to-green-hn rounded-full"></div>
        </div>



        {{-- Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Dinamicos --}}
            @foreach($this->filteredTools as $tool)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-green-hn/10 flex items-center justify-center">
                                @if(!empty($tool->icono_url))
                                    <img src="{{ $tool->icono_url }}" alt="{{ $tool->nombre }}" class="w-6 h-6 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <x-lucide-external-link class="w-5 h-5 text-green-hn hidden" />
                                @else
                                    <x-lucide-external-link class="w-5 h-5 text-green-hn" />
                                @endif
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900">
                                {{ $tool->nombre }}
                            </h3>
                        </div>

                        <p class="text-sm sm:text-base text-gray-500">
                            {{ $tool->descripcion_corta }} <br>                     
                        </p>
                    </div>
                    <div class="mt-6">
                        @if($tool->browser == '1')
                            <button onclick="getHidden('{{ $tool->app_url }}&host={{ urlencode($hostname) }}')"
                                class="inline-flex items-center gap-2 bg-orange-hn text-white text-sm sm:text-base font-medium px-4 py-2 rounded-lg hover:opacity-90 transition cursor-pointer">
                                Ingresar
                                <x-lucide-arrow-right class="w-4 h-4" />
                            </button>
                        @else
                            <a href="{{ $tool->app_url }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 bg-orange-hn text-white text-sm sm:text-base font-medium px-4 py-2 rounded-lg hover:opacity-90 transition">
                                Ingresar
                                <x-lucide-arrow-right class="w-4 h-4" />
                            </a>
                        @endif
                    </div>


                </div>
            @endforeach
        </div>
    </section>

</div>

@push('scripts')
<script>
    function getHidden(url) {
        // Ejecutar GET sin abrir navegador
        fetch(`${url}`, {
            method: 'GET',
            mode: 'no-cors', // Si la API lo permite
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al procesar la solicitud');
        });
    }
</script>
@endpush

