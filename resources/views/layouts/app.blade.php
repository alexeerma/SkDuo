{{--
  Master layout — every page extends this.
  Sage 11 ships with a similar app.blade.php; this replaces it.
--}}
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    {{-- Non render-blocking Google Fonts (display=swap); noscript fallback keeps fonts without JS. --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700;800&display=swap" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
      wp_head();
    @endphp
  </head>

  <body <?php body_class('font-sans bg-paper text-ink antialiased'); ?>>
    @php
      wp_body_open();
    @endphp

    <a href="#main" class="skduo-skip-link" data-menu-inert>Liigu põhisisu juurde</a>

    @include('sections.header')

    <main id="main" class="min-h-screen" tabindex="-1" data-menu-inert>
      @yield('content')
    </main>

    @include('sections.footer')

    @php
      wp_footer();
    @endphp
  </body>
</html>
