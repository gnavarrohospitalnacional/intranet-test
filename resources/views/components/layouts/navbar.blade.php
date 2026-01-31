<nav class="bg-neutral-primary w-full z-20 top-0 start-0 border-b border-default">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4 pr-2">
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="{{ url("images/logo.png") }}" class="h-12" alt="Flowbite Logo">
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <a href="https://helpdesk.hospitalnacional.com/"
                target="_blank"
                class="btn-primary btn inline-flex items-center gap-2" type="button">
                <x-lucide-headset class="w-4 h-4" />
                Mesa de ayuda
            </a>

            <button data-collapse-toggle="navbar-sticky" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-body rounded-base md:hidden hover:bg-neutral-secondary-soft hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary"
                aria-controls="navbar-sticky" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
            <ul
                class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-default rounded-base bg-neutral-secondary-soft md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-neutral-primary">
                <li>
                    <a href="{{url('/')}}"
                        class="text-gray-hn block py-2 px-3 text-sm  rounded md:hover:bg-transparent md:border-0 md:p-0 {{request()->is('/') ? 'text-green-hn' : ''}}"
                        aria-current="page">Inicio</a>
                </li>
                <li>
                    <a href="{{url('tools')}}"
                        class="block text-gray-hn py-2 px-3 text-sm rounded hover:text-green-hn md:hover:bg-transparent md:border-0 md:p-0 {{request()->is('tools') ? 'text-green-hn' : ''}}">Herramientas</a>
                </li>
                <li>
                    <a href="{{url('academy')}}"
                        class="block text-gray-hn py-2 px-3 text-sm rounded hover:text-green-hn md:hover:bg-transparent md:border-0 md:p-0 {{request()->is('academy') ? 'text-green-hn' : ''}}">HN
                        Academy</a>
                </li>
                <li>
                    <a href="{{url('nacional')}}"
                        class="block text-gray-hn py-2 px-3 text-sm rounded hover:text-green-hn md:hover:bg-transparent md:border-0 md:p-0 {{request()->is('nacional') ? 'text-green-hn' : ''}}">La
                        Nacional</a>
                </li>
                <li>
                    <a href="{{url('phones')}}"
                        class="block text-gray-hn py-2 px-3 text-sm rounded hover:text-green-hn md:hover:bg-transparent md:border-0 md:p-0 {{request()->is('phones') ? 'text-green-hn' : ''}}">Directorio</a>
                </li>
            </ul>
        </div>
    </div>
</nav>