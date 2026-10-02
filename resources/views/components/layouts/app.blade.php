@props(['title'])

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} </title>
    <script>
        try {
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch {}
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <header class="fixed inset-x-0 top-0 z-30 flex h-14 items-center border-b border-gray-200 bg-white px-4 sm:hidden">
        <button type="button"
            class="inline-flex size-10 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 focus:ring-2 focus:ring-gray-200"
            data-drawer-target="app-sidebar" data-drawer-toggle="app-sidebar" aria-controls="app-sidebar">
            <span class="sr-only">Abrir menú principal</span>
            <x-icon name="menu" />
        </button>
    </header>

    <aside id="app-sidebar"
        class="sidebar-shell fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full border-e border-gray-200 bg-white transition-[transform,width] sm:translate-x-0"
        aria-label="Navegación principal" data-sidebar>
        <div class="flex h-full flex-col overflow-y-auto px-3 py-4">
            <div class="mb-5 hidden items-center justify-between px-1 sm:flex">
                <span class="sidebar-label text-sm font-semibold tracking-tight text-gray-900">
                    Menú principal
                </span>

                <button type="button" class="sidebar-toggle" data-sidebar-toggle aria-controls="app-sidebar"
                    aria-expanded="true" aria-label="Contraer menú" title="Contraer menú">
                    <x-icon name="chevron-left" data-sidebar-icon="collapse" />
                    <x-icon name="chevron-right" data-sidebar-icon="expand" hidden />
                </button>
            </div>

            <nav class="flex-1" aria-label="Secciones">
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="{{ route('dashboard') }}" @class([
                            'sidebar-link',
                            'sidebar-link-active' => request()->routeIs('dashboard'),
                        ]) aria-label="Inicio" title="Inicio">
                            <x-icon name="home" />
                            <span class="sidebar-label">Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('collaborators.index') }}" @class([
                            'sidebar-link',
                            'sidebar-link-active' => request()->routeIs('collaborators.*'),
                        ]) aria-label="Colaboradores" title="Colaboradores">
                            <x-icon name="users" />
                            <span class="sidebar-label">Colaboradores</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users-and-services.index') }}" @class([
                            'sidebar-link',
                            'sidebar-link-active' => request()->routeIs('users-and-services.*'),
                        ]) aria-label="Usuarios y Servicios" title="Usuarios y Servicios">
                            <x-icon name="user" />
                            <span class="sidebar-label">Usuarios y Servicios</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('algorithm.index') }}" @class([
                            'sidebar-link',
                            'sidebar-link-active' => request()->routeIs('algorithm.*'),
                        ]) aria-label="Algoritmos" title="Algoritmos">
                            <x-icon name="code" />
                            <span class="sidebar-label">Algoritmos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.edit') }}" @class([
                            'sidebar-link',
                            'sidebar-link-active' => request()->routeIs('account.*'),
                        ]) aria-label="Configuración de Cuenta" title="Configuración de Cuenta">
                            <x-icon name="settings" />
                            <span class="sidebar-label">Configuración de Cuenta</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-account border-t border-gray-200 pt-3">
                <div class="sidebar-account-details mb-2 px-3 py-2">
                    <p class="truncate text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-link w-full" aria-label="Cerrar sesión" title="Cerrar sesión">
                        <x-icon name="logout" />
                        <span class="sidebar-label">Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="app-content sm:ml-64" data-sidebar-content>
        <main id="contenido" class="mx-auto w-full max-w-6xl px-5 pt-20 pb-10 sm:px-8 sm:py-10">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
