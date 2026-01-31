<aside class="bg-white rounded shadow p-6">
    <h4 class="text-lg font-semibold text-[#ff5b00]">Accesos rápidos</h4>
    <div class="mt-4 space-y-3">
        {{-- Acceso rápido dinámicos --}}
        @foreach($shortcuts as $shortcut)
            @if($shortcut->browser == '1')
                <div onclick="getHidden('{{ $shortcut->app_url }}&host={{ urlencode($hostname) }}')" 
                    class="flex items-center justify-between border border-gray-200 rounded p-3 hover:border-green-hn cursor-pointer transition-colors duration-300">
                    <div>
                        <div class="text-sm font-semibold text-orange-hn">{{ $shortcut->nombre }}</div>
                        <div class="text-xs text-gray-500">Acceso rápido</div>
                    </div>
                    <div class="text-green-500 text-xl">&rarr;</div>
                </div>
            @else
                <a href="{{ $shortcut->app_url }}" target="_blank" class="flex items-center justify-between border border-gray-200 rounded p-3 hover:border-green-hn cursor-pointer transition-colors duration-300">
                    <div>
                        <div class="text-sm font-semibold text-orange-hn">{{ $shortcut->nombre }}</div>
                        <div class="text-xs text-gray-500">Acceso rápido</div>
                    </div>
                    <div class="text-green-500 text-xl">&rarr;</div>
                </a>
            @endif
        @endforeach
    </div>
</aside>
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