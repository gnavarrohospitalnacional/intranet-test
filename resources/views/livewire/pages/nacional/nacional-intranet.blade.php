<div>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- MAIN CONTENT --}}
            <article class="lg:col-span-8">
                <div class="mt-4">
                    <h2 id="pdf-title" class="text-xl font-semibold text-gray-900">
                        {{ $selectedTitle }}
                    </h2>
                </div>
                <div id="la-nacional" class="w-full h-[80vh]"></div>
            </article>

            {{-- SIDEBAR --}}
            <aside class="lg:col-span-4 space-y-6">

                {{-- Destacado / CTA --}}
                <div class="bg-gradient-to-br from-green-hn to-green-600 text-white rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold mb-2">
                        La nacional
                    </h3>
                    <p class="text-sm opacity-90 mb-4">
                        Enterate de todo el acontecer institucional y mantente informado con las últimas noticias
                        del Hospital Nacional.
                    </p>
                </div>

                {{-- Otras noticias --}}
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm" wire:ignore>
                    <div class="px-5 py-4 border-b">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                            Ediciones anteriores
                        </h3>
                    </div>
                        <ul class="divide-y" id="dynamic-nacional-list">
                            @foreach($nacionalItems as $item)
                                <li id="dynamic-item-{{ $item['codigo'] }}">
                                    <a
                                        href="{{ url('/nacional/' . $item['codigo']) }}"
                                        class="nacional-link w-full text-left flex items-start gap-3 px-5 py-4 hover:bg-gray-50 transition cursor-pointer block"
                                    >
                                        <span class="mt-1">
                                            <x-lucide-arrow-right
                                                class="w-4 h-4 arrow-icon {{ $selectedNacionalId == $item['codigo'] ? 'text-green-hn' : '' }}"
                                            />
                                        </span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                {{ \Illuminate\Support\Str::limit($item['titulo'], 40, '...') }}
                                            </h4>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                </div>
            </aside>
        </div>
    </section>

</div>
@push('styles')
<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .pdf-loader {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.95);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    if (!window.EmbedPDF) {
        console.error('EmbedPDF no está cargado');
        return;
    }

    const container = document.getElementById('la-nacional');

    const embedConfig = {
        type: "container",
        target: container,
        documentManager: { maxDocuments: 1 },
        plugins: { documentManager: false },
        disabledCategories: [
            'redaction',
            'annotation',
            'print',
            'annotation-highlight',
            'panel',
            'document-print',
            'document'
        ],
        pdfOpenParams: {
            zoom: 100,
            view: "Fit"
        },
        theme: {
            preference: 'light',
            light: {
                accent: {
                    primary: '#FF5B00',
                    primaryHover: '#FF5B00',
                    primaryActive: '#FF5B00',
                    primaryLight: '#FF5B00',
                    primaryForeground: '#fff'
                },
                background: {
                    app: '#ffffff'
                }
            }
        },
        disableDownload: true,
        disablePrint: true,
        disableOpenFile: true,
        i18n: {
            defaultLocale: 'es',
            fallbackLocale: 'en'
        }
    };

    function loadPdf(pdfUrl, title = '') {
        container.innerHTML = '';

        window.EmbedPDF.init({
            ...embedConfig,
            src: pdfUrl
        });

        const titleElement = document.getElementById('pdf-title');
        if (titleElement) {
            titleElement.textContent = title;
        }
    }

    // PDF inicial
    loadPdf(@json($selectedPdfUrl), @json($selectedTitle));
});
</script>
@endpush