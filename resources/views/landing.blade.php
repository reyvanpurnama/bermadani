<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ coop_config('legal_name', 'Koperasi Konsumen Syariah Berkah Solusi Madani') }} — UMBandung</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=SF+Pro+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .text-apple-headline {
            letter-spacing: -0.035em;
            line-height: 1.04;
        }

        .apple-btn-blue {
            background-color: #155A6B;
            color: #ffffff;
            border-radius: 980px;
            box-shadow: 0 4px 14px 0 rgba(21, 90, 107, 0.3);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .apple-btn-blue:hover {
            background-color: #1a6b80;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px 0 rgba(21, 90, 107, 0.4);
            color: #ffffff;
        }

        /* Glassmorphism CTA Primary */
        .glass-cta-primary {
            background: rgba(255, 255, 255, 0) !important;
            backdrop-filter: blur(24px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.9) !important;
            border-radius: 980px !important;
            box-shadow: 
                inset 0 1.5px 2px 0 rgba(255, 255, 255, 0.8),
                0 10px 30px -4px rgba(0, 0, 0, 0.12) !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .glass-cta-primary:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border-color: #ffffff !important;
            transform: translateY(-2px) scale(1.03) !important;
            box-shadow: 
                inset 0 2px 4px 0 rgba(255, 255, 255, 1),
                0 16px 36px -4px rgba(0, 0, 0, 0.18) !important;
            color: #155A6B !important;
        }

        /* Glassmorphism CTA Secondary */
        .glass-cta-secondary {
            background: rgba(255, 255, 255, 0) !important;
            backdrop-filter: blur(24px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.7) !important;
            border-radius: 980px !important;
            box-shadow: 
                inset 0 1.5px 2px 0 rgba(255, 255, 255, 0.6),
                0 8px 24px -4px rgba(0, 0, 0, 0.08) !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .glass-cta-secondary:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 255, 255, 0.95) !important;
            transform: translateY(-2px) scale(1.03) !important;
            box-shadow: 
                inset 0 2px 3px 0 rgba(255, 255, 255, 0.9),
                0 12px 30px -4px rgba(0, 0, 0, 0.14) !important;
            color: #0f172a !important;
        }
    </style>
</head>
<body class="bg-[#f5f5f7] text-zinc-900 selection:bg-[#155A6B] selection:text-white transition-colors duration-300">

    <!-- Modular Sections -->
    @include('landing.partials.navbar')
    @include('landing.partials.hero')
    @include('landing.partials.features')
    @include('landing.partials.supplier')
    @include('landing.partials.advantages')
    @include('landing.partials.faq')
    @include('landing.partials.footer')

</body>
</html>
