<!-- Page Heading -->
<header class="bg-blue-800 shadow">
    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 flex justify-between">
        <h2 class="px-4  text-white text-sm">
            Hola <span class="font-bold">{{ auth()->user()->nombre }}</span>
        </h2>
        <h2 class="px-4 text-white text-sm">Personal Collect</h2>
    </div>
</header>
<nav x-data="{ open: false }" class="bg-gray-200 p-2">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-2">
        <div class="flex justify-between h-10">
            <div class="flex">
                <div class="-mr-2 flex items-center sm:hidden">
                    <h1 class="px-4 font-bold ">Plataforma de gestión de cobranzas</h1>
                </div>
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @if(auth()->user()->rol === 'Administrador')
                        <x-nav-link :href="route('usuarios')" :active="request()->routeIs('usuarios')">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                        <x-nav-link :href="route('clientes')"
                            :active="request()->
                            routeIs('clientes')
                            || request()->routeIs('perfil.cliente')
                            || request()->routeIs('perfil.producto')
                            ">
                            {{ __('Clientes') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link :href="route('cartera')"
                            :active="request()->
                            routeIs('cartera')
                            || request()->routeIs('deudor.perfil')
                            ">
                        {{ __('Cartera') }}
                    </x-nav-link>
                    <x-nav-link :href="route('gestiones')"
                            :active="request()->
                            routeIs('gestiones')
                            || request()->routeIs('gestiones')
                            || request()->routeIs('operacion.perfil')
                            ">
                        {{ __('Gestiones') }}
                    </x-nav-link>
                    <x-nav-link :href="route('acuerdos')"
                            :active="request()->
                            routeIs('acuerdos')
                            || request()->routeIs('acuerdo.perfil')
                            "                            >
                        {{ __('Acuerdos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('cuotas')"
                            :active="request()->
                            routeIs('cuotas')
                            || request()->routeIs('cuota.perfil')
                            "                            >
                        {{ __('Cuotas') }}
                    </x-nav-link>
                    <x-nav-link :href="route('perfil')" :active="request()->routeIs('perfil')">
                        {{ __('Perfil') }}
                    </x-nav-link>
                </div>
            </div>
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-800 hover:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                            <div>Opciones</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Salir') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->rol === 'Administrador')
                <x-responsive-nav-link :href="route('usuarios')" :active="request()->routeIs('usuarios')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('clientes')" :active="request()->routeIs('clientes')">
                    {{ __('Clientes') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link 
                    :href="route('cartera')"
                    :active="request()->
                    routeIs('cartera')
                    || request()->
                    routeIs('deudor.perfil')
                    ">
                {{ __('Cartera') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('gestiones')" :active="request()->routeIs('gestiones')">
                {{ __('Gestiones') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('acuerdos')" :active="request()->routeIs('acuerdos')">
                {{ __('Acuerdos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cuotas')" :active="request()->routeIs('cuotas')">
                {{ __('Cuotas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('perfil')" :active="request()->routeIs('perfil')">
                {{ __('Perfil') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->nombre }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Salir') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
