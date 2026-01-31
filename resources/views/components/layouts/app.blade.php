<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intranet Nacional</title>
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [data-epdf-i="document-menu-button"] {
            display: none !important;
        }
    </style>
</head>
<body>
  
    <div>
        <div class="min-h-screen bg-[#fbfbfb] text-gray-800">
            <header>
                <x-layouts.navbar />
            </header>
            {{ $slot }}
            <footer>
                @if(isset($social))
                <x-layouts.footer :social="$social" />
                @endif
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    @livewireScripts
</body>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-DK1YS9Q380"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-DK1YS9Q380');
</script>
@stack('scripts')
</html>