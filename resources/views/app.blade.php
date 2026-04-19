<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="index, follow" />

        {{-- Canonical is set per-page via Inertia <Head>, but provide a safe default --}}
        <link rel="canonical" href="{{ url()->current() }}" />

        {{-- Favicon --}}
        <link rel="icon" type="image/x-icon" href="/favicon.ico" />

        {{-- Default OG fallbacks (overridden per-page via Inertia <Head>) --}}
        <meta property="og:site_name" content="GG Case Store" />
        <meta property="og:locale" content="id_ID" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ asset('images/logoCatalog.png') }}" />
        <meta name="twitter:card" content="summary_large_image" />

        {{-- hreflang --}}
        <link rel="alternate" hreflang="id" href="{{ url('/') }}" />
        <link rel="alternate" hreflang="x-default" href="{{ url('/') }}" />

        <title inertia>{{ config('app.name', 'GG Case Store') }}</title>

        @routes
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
