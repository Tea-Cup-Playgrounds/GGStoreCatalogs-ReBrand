<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>404 — Halaman Tidak Ditemukan | GG Case Store</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    @vite(['resources/css/app.css'])
    <style>
        body { margin: 0; }
    </style>
</head>
<body class="bg-background text-foreground font-sans antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-16 text-center">

        <!-- SVG Illustration -->
        <svg
            viewBox="0 0 480 320"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            class="mb-8 w-full max-w-sm"
            aria-hidden="true"
        >
            <!-- Ground shadow -->
            <ellipse cx="240" cy="295" rx="140" ry="12" fill="currentColor" class="text-muted/40" />

            <!-- Open box body -->
            <path d="M140 160 L240 110 L340 160 L340 260 L240 310 L140 260 Z"
                  stroke="currentColor" stroke-width="4" stroke-linejoin="round"
                  fill="none" class="text-border" />
            <!-- Box shading left face -->
            <path d="M140 160 L240 210 L240 310 L140 260 Z"
                  fill="currentColor" class="text-muted/30" />
            <!-- Box shading right face -->
            <path d="M340 160 L240 210 L240 310 L340 260 Z"
                  fill="currentColor" class="text-muted/20" />
            <!-- Box center spine -->
            <path d="M240 210 L240 310"
                  stroke="currentColor" stroke-width="4" stroke-linecap="round"
                  class="text-border" />
            <!-- Box top fold -->
            <path d="M140 160 L240 210 L340 160"
                  stroke="currentColor" stroke-width="4" stroke-linejoin="round"
                  fill="none" class="text-border" />

            <!-- Lid flaps open -->
            <path d="M240 110 L240 60"
                  stroke="currentColor" stroke-width="4" stroke-linecap="round"
                  class="text-border" />
            <path d="M190 135 L175 88"
                  stroke="currentColor" stroke-width="4" stroke-linecap="round"
                  class="text-border" />
            <path d="M290 135 L305 88"
                  stroke="currentColor" stroke-width="4" stroke-linecap="round"
                  class="text-border" />

            <!-- Magnifier circle -->
            <circle cx="330" cy="100" r="38"
                    stroke="currentColor" stroke-width="5"
                    fill="none" class="text-primary" />
            <!-- Magnifier handle -->
            <path d="M358 128 L385 155"
                  stroke="currentColor" stroke-width="6" stroke-linecap="round"
                  class="text-primary" />
            <!-- X inside magnifier -->
            <path d="M316 86 L344 114 M344 86 L316 114"
                  stroke="currentColor" stroke-width="4.5" stroke-linecap="round"
                  class="text-primary" />

            <!-- "404" text rendered as paths / rects for pure SVG -->
            <!-- 4 -->
            <path d="M60 220 L60 250 M60 235 L80 220 L80 260"
                  stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                  fill="none" class="text-foreground/20" />
            <!-- 0 -->
            <rect x="90" y="218" width="28" height="42" rx="8"
                  stroke="currentColor" stroke-width="5"
                  fill="none" class="text-foreground/20" />
            <!-- 4 -->
            <path d="M132 220 L132 250 M132 235 L152 220 L152 260"
                  stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                  fill="none" class="text-foreground/20" />

            <!-- Floating dots decoration -->
            <circle cx="80" cy="80" r="5" fill="currentColor" class="text-primary/40" />
            <circle cx="400" cy="200" r="4" fill="currentColor" class="text-primary/30" />
            <circle cx="420" cy="80" r="3" fill="currentColor" class="text-primary/20" />
            <circle cx="55" cy="170" r="3" fill="currentColor" class="text-primary/20" />
        </svg>

        <!-- Heading -->
        <h1 class="mb-2 text-4xl font-bold tracking-tight text-foreground">404</h1>
        <p class="mb-1 text-lg font-semibold text-foreground">Halaman Tidak Ditemukan</p>
        <p class="mb-8 max-w-xs text-sm text-muted-foreground leading-relaxed">
            Halaman yang kamu cari tidak ada atau sudah dipindahkan.
        </p>

        <!-- Actions -->
        <div class="flex flex-col items-center gap-3 sm:flex-row">
            <a
                href="/"
                class="inline-flex h-10 items-center justify-center rounded-full bg-primary px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90"
            >
                Kembali ke Beranda
            </a>
            <a
                href="/catalog"
                class="inline-flex h-10 items-center justify-center rounded-full border border-border bg-card px-6 text-sm font-medium text-foreground transition-colors hover:border-primary hover:text-primary"
            >
                Lihat Katalog
            </a>
        </div>
    </div>
</body>
</html>
