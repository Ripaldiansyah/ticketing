<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ticketing Pro') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-slate-50">

        <div
            class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white border border-gray-100 shadow-xl rounded-2xl relative z-10">
            <div class="flex flex-col items-center mb-8">

                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Admin Portal Access</h2>

            </div>

            {{ $slot }}
        </div>

        {{-- <div class="absolute bottom-6 text-center w-full text-slate-400 text-xs">
            &copy; {{ date('Y') }} Event Ticketing System. All rights reserved.
        </div> --}}
    </div>
</body>

</html>