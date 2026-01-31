<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-gray-900">
            HN Academy
        </h1>
        <p class="mt-2 text-sm sm:text-base text-gray-500">
            Plataforma de Capacitación del Hospital Nacional - Accede a nuestros sistemas de entrenamiento Moodle
        </p>
        <div class="mt-4 h-1 w-full bg-gradient-to-r from-orange-hn to-green-hn rounded-full"></div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        @forelse($courses as $course)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-green-hn/10 flex items-center justify-center">
                            <x-dynamic-component :component="$course['icono']" class="w-5 h-5 text-green-hn" />
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-900">
                            {{ $course['nombre'] }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-base text-gray-500">
                        {{ $course['descripcion'] }}
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ $course['enlace'] }}" target="_blank"
                        class="inline-flex items-center gap-2 bg-orange-hn text-white text-sm sm:text-base font-medium px-4 py-2 rounded-lg hover:opacity-90 transition">
                        Ingresar
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12">
                <p class="text-gray-500">No hay cursos disponibles en este momento.</p>
            </div>
        @endforelse
    </div>
</div>
