<div>
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-orange-hn">
            Directorio telefónico
        </h1>
        <p class="mt-2 text-sm sm:text-base text-gray-500">
            Números de teléfonos y datos de nuestros colaboradores
        </p>
        <div class="mt-4 h-1 w-full bg-linear-to-r from-orange-hn to-yellow-300 rounded-full"></div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Ubicación --}}
            <div>
                <label class="sr-only">Ubicación</label>
                <select wire:model="selectedCompany" wire:change="applyCompany($event.target.value)" class="w-full rounded-lg border-gray-200 text-sm focus:border-orange-hn focus:ring-orange-hn">
                    <option value="">Seleccione ubicación</option>
                    @foreach($companies as $company)
                        <option value="{{ $company }}">{{ $company }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Departamento --}}
            <div>
                <label class="sr-only">Departamento</label>
                <select wire:model="selectedDepartment" wire:change="applyDepartment($event.target.value)" class="w-full rounded-lg border-gray-200 text-sm focus:border-orange-hn focus:ring-orange-hn">
                    <option value="">Seleccione Departamento</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Search --}}
            <div class="relative">
                <x-lucide-search class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Busca una persona"
                    class="w-full pl-10 rounded-lg border-gray-200 text-sm focus:border-orange-hn focus:ring-orange-hn"
                />
            </div>
        </div>
    </div>

    {{-- Indicador de carga --}}
    <div wire:loading wire:target="search,applyCompany,applyDepartment" class="flex items-center justify-center py-16 min-h-[400px] w-full">
        <div class="flex flex-col items-center justify-center gap-4">
            <!-- Spinner -->
            <svg class="w-24 h-24 text-orange-hn hn-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>

            <!-- Texto y dots animados -->
            <div class="text-center">
                <p class="text-gray-600 text-lg font-medium">Cargando resultados</p>
            </div>
        </div>
    </div>

    {{-- Cards --}}
    @if(isset($paginatedUsers) && $paginatedUsers->count() > 0)
        <div wire:loading.class="opacity-30 pointer-events-none transition-opacity duration-300" wire:target="search,applyCompany,applyDepartment" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($paginatedUsers as $user)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        {{-- Header --}}
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-10 h-10 rounded-full bg-green-hn text-white flex items-center justify-center font-semibold">
                                {{ strtoupper(substr($user->ubicacion_personal, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900">
                                    {{ $user->ubicacion_personal }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-500">
                                    {{ $user->position }}
                                </p>
                            </div>
                        </div>

                        {{-- Tag --}}
                        <span class="inline-block mb-4 px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                            {{ $user->department }}
                        </span>

                        {{-- Info --}}
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex gap-2">
                                <x-lucide-map-pin class="w-5 h-5 text-gray-400" />
                                {{ $user->company }}
                            </li>
                            <li class="flex gap-2">
                                <x-lucide-phone class="w-5 h-5 text-gray-400" />
                                {{ ($user->no_directo ? $user->no_directo : 'N/A  ') }}
                                @if($user->extension)
                                    <span class="ml-3">EXT-{{ $user->extension }}</span>
                                @endif
                            </li>
                            <li class="flex gap-2">
                                <x-lucide-smartphone class="w-5 h-5 text-gray-400"/>
                                {{ ($user->cell_phone ? $user->cell_phone : 'N/A  ') }}
                            </li>
                            <li class="flex gap-2 break-all">
                                <x-lucide-mail class="w-5 h-5 text-gray-400" />
                                {{ ($user->email ? $user->email : 'N/A  ') }}
                            </li>
                        </ul>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 flex gap-3">
                        @if($user->email)
                            <a href="mailto:{{ $user->email }}"
                            class="flex-1 inline-flex justify-center items-center gap-2 bg-orange-hn text-white text-xs sm:text-sm font-medium px-3 py-2 rounded-lg hover:opacity-90 transition">
                                Email
                            </a>
                            <a href="https://teams.microsoft.com/l/chat/0/0?users={{ $user->email }}"
                            class="flex-1 inline-flex justify-center items-center gap-2 bg-green-hn text-white text-xs sm:text-sm font-medium px-3 py-2 rounded-lg hover:opacity-90 transition">
                                Microsoft Teams
                            </a>
                        @else
                            <span class="flex-1 inline-flex justify-center items-center gap-2 bg-gray-300 text-gray-500 text-xs sm:text-sm font-medium px-3 py-2 rounded-lg cursor-not-allowed opacity-60">
                                Email
                            </span>
                            <span class="flex-1 inline-flex justify-center items-center gap-2 bg-gray-300 text-gray-500 text-xs sm:text-sm font-medium px-3 py-2 rounded-lg cursor-not-allowed opacity-60">
                                Microsoft Teams
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Controles de paginación --}}
        @if($paginatedUsers->lastPage() > 1)
            <nav class="mt-6 flex items-center justify-center" aria-label="Paginación">
                <ul class="inline-flex items-center space-x-1">
                    {{-- Previous --}}
                    <li>
                        <button wire:click="setPage({{ max(1, $paginatedUsers->currentPage()-1) }})" @disabled($paginatedUsers->onFirstPage()) class="px-3 py-2 ml-0 leading-tight border border-gray-200 rounded-l-lg bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50 cursor-pointer">
                            &laquo;
                        </button>
                    </li>

                    {{-- Page numbers --}}
                    @php
                        $start = max(1, $paginatedUsers->currentPage() - 2);
                        $end = min($paginatedUsers->lastPage(), $paginatedUsers->currentPage() + 2);
                    @endphp

                    @if($start > 1)
                        <li>
                            <button wire:click="setPage(1)" class="px-3 py-2 border border-gray-200 bg-white text-gray-700 hover:bg-gray-100 rounded cursor-pointer">1</button>
                        </li>
                        @if($start > 2)
                            <li>
                                <span class="px-2 py-2 mx-0.5 border border-gray-200 bg-white text-gray-500 rounded">...</span>
                            </li>
                        @endif
                    @endif

                    @for($i = $start; $i <= $end; $i++)
                        <li>
                            <button wire:click="setPage({{ $i }})" class="px-3 py-2 border border-gray-200 mx-0.5 cursor-pointer {{ $paginatedUsers->currentPage() == $i ? 'bg-orange-hn text-white font-semibold' : 'bg-white text-gray-700 hover:bg-gray-100' }} rounded">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor

                    @if($end < $paginatedUsers->lastPage())
                        @if($end < $paginatedUsers->lastPage() - 1)
                            <li>
                                <span class="px-2 py-2 mx-0.5 border border-gray-200 bg-white text-gray-500 rounded">...</span>
                            </li>
                        @endif
                        <li>
                            <button wire:click="setPage({{ $paginatedUsers->lastPage() }})" class="px-3 py-2 border border-gray-200 bg-white text-gray-700 hover:bg-gray-100 rounded cursor-pointer">{{ $paginatedUsers->lastPage() }}</button>
                        </li>
                    @endif

                    {{-- Next --}}
                    <li>
                        <button wire:click="setPage({{ min($paginatedUsers->lastPage(), $paginatedUsers->currentPage()+1) }})" @disabled($paginatedUsers->currentPage() == $paginatedUsers->lastPage()) class="px-3 py-2 leading-tight border border-gray-200 rounded-r-lg bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50 cursor-pointer">
                            &raquo;
                        </button>
                    </li>
                </ul>
            </nav>
        @endif
    @else
        <div class="text-center py-16">
            <x-lucide-inbox class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                No conseguimos ningún resultado
            </h3>
            <p class="text-gray-600">
                Modifique su busqueda o intente mas adelante
            </p>
        </div>
    @endif
</section>

</div>