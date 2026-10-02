@props(['title'])

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <main id="contenido" class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="w-full max-w-sm" aria-labelledby="auth-heading">
            <div class="mb-8 text-center">
                <h1 id="auth-heading" class="text-2xl font-bold tracking-tight text-gray-900">{{ $title }}</h1>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                {{ $slot }}
            </div>
        </section>
    </main>
</body>

</html>
