<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        @php($company = \App\Models\Company::first())
        @php($brandName = $company->name ?? config('app.name', 'ASLM'))
        @php($brandLogo = null)
        @php(
            $brandLogo = ($company && !empty($company->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo))
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo)
                : null
        )

        <title inertia>{{ $brandName }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            window.Laravel = { csrfToken: '{{ csrf_token() }}' };
            window.__BRAND_NAME__ = @json($brandName);
            window.__BRAND_LOGO__ = @json($brandLogo);
        </script>

        @if($brandLogo)
            <link rel="icon" href="{{ $brandLogo }}">
        @else
            <link rel="icon" href="/favicon.ico" sizes="any">
            <link rel="icon" href="/favicon.svg" type="image/svg+xml">
            <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        @endif

        @php($brandColor = $company->brand_color ?? null)
        @php($brandSecondary = $company->secondary_color ?? null)
        @php($brandLogoH = $company->brand_logo_height ?? 32)
        @php($brandTitleSize = $company->brand_title_size ?? 14)
        @php($brandPrintLogoH = $company->brand_print_logo_height ?? ($brandLogoH * 1.5))
        @php($brandLogoW = ($company->brand_logo_width && $company->brand_logo_width > 0) ? $company->brand_logo_width : $brandLogoH)
        @php($sidebarLogoH = $company->sidebar_logo_height ?? null)
        @php($sidebarLogoW = $company->sidebar_logo_width ?? null)
        <style>
            :root {
                --brand-logo-size: {{ $brandLogoH }}px;
                --brand-logo-width: {{ $brandLogoW }}px;
                --brand-title-size: {{ $brandTitleSize }}px;
                --brand-print-logo-size: {{ (int) $brandPrintLogoH }}px;
                --brand-logo-padding: {{ (int) ($company->brand_logo_padding ?? 0) }}px;
                --brand-logo-scale: {{ (int) ($company->brand_logo_scale ?? 100) }};
                --sidebar-logo-size: {{ $sidebarLogoH ? (int) $sidebarLogoH . 'px' : 'var(--brand-logo-size)' }};
                --sidebar-logo-width: {{ $sidebarLogoW ? (int) $sidebarLogoW . 'px' : 'var(--brand-logo-width, var(--brand-logo-size))' }};
            }
            /* Common print logo height used across print headers */
            .print-logo { height: var(--brand-print-logo-size); width: auto; }
        </style>
        @if($brandColor)
            <style>
                :root {
                    --brand: {{ $brandColor }};
                    --brand-secondary: {{ $brandSecondary ?? '#64748b' }};
                }
                .btn-glass.btn-variant-primary { background: var(--brand) !important; border-color: var(--brand) !important; color: #fff !important; }
                .dark .btn-glass.btn-variant-primary { background: var(--brand) !important; border-color: var(--brand) !important; color: #fff !important; }
                .btn-glass.btn-variant-secondary { background: var(--brand-secondary) !important; border-color: var(--brand-secondary) !important; color: #fff !important; }
                .dark .btn-glass.btn-variant-secondary { background: var(--brand-secondary) !important; border-color: var(--brand-secondary) !important; color: #fff !important; }
                .print-divider { border-top-color: {{ $brandColor }} !important; }
                a.text-indigo-600, .text-indigo-600 { color: var(--brand) !important; }
                .hover\:text-indigo-600:hover { color: var(--brand) !important; }
                .focus\:border-indigo-500 { border-color: var(--brand) !important; }
                .focus\:ring-indigo-500 { --tw-ring-color: var(--brand) !important; }
            </style>
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        {{-- Include our manually created CSS file --}}
        <link rel="stylesheet" href="/css/app.css">

        {{-- Ziggy routes --}}
        @routes

        {{-- Conditionally include Vite assets if manifest exists --}}
        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @endif

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
