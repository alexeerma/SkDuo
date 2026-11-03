# SK DUO — Sage 11 (Laravel Blade) Starter

This is your starting point. Real working scaffold on top of Sage 11. You install Sage, drop these files in, run two commands, you're live.

## What you need installed locally

| Tool | Version | Why |
|---|---|---|
| **LocalWP** (free) | latest | Easiest local WordPress. Download at localwp.com |
| **PHP** | 8.3+ | This theme requires PHP 8.3+. LocalWP includes it. |
| **Composer** | 2.x | Sage's package manager. brew/scoop install. |
| **Node.js** | 20+ | Asset compilation. Use nvm to manage versions. |
| **VS Code** | latest | + Blade Formatter + PHP Intelephense extensions |

**Alternative to LocalWP:** Laravel Herd (free, Mac/Windows). Has native PHP + Composer + Node bundled. Cleaner if you're already in the Laravel world.

## Setup steps (one time)

### 1. Create a fresh WordPress site

In LocalWP: click `+`, name it `skduo`, choose PHP 8.3+, Apache or Nginx, your choice. Boot it up.

### 2. Install Sage 11 as your theme

Open terminal in the site folder:

```bash
cd ~/Local\ Sites/skduo/app/public/wp-content/themes
composer create-project roots/sage skduo
```

This downloads Sage and creates a `skduo` theme folder with all the boilerplate. Takes about 2 minutes.

### 3. Install Acorn (Laravel package manager for WordPress)

Acorn is what makes Blade work inside WordPress. Sage 11 needs it as a separate plugin.

In WP admin: Plugins → Add New → search "Acorn" → install + activate. Or via composer in the parent directory.

### 4. Drop in the SK DUO files

Copy everything from this starter into your new `skduo` theme folder. The structure matches Sage's layout, so files just slot in.

```
skduo/
├── app/
│   └── View/Composers/NextMatch.php       ← from starter
├── inc/                                     ← NEW folder (add it)
│   ├── cpt-mang.php
│   └── helpers.php
├── resources/
│   ├── views/                              ← from starter (overwrites Sage defaults)
│   ├── css/app.css                         ← from starter
│   └── js/
│       ├── app.js                          ← keep Sage default, just import animations
│       └── animations.js                   ← from starter
└── functions.php                            ← add 1 line (see step 5)
```

### 5. Hook the CPTs into functions.php

Add this line near the top of `functions.php`:

```php
foreach ( glob( get_theme_file_path( 'inc/*.php' ) ) as $file ) {
    require_once $file;
}
```

That auto-loads every file in `/inc/` (the CPTs and helpers).

### 6. Install JS dependencies and start the dev server

```bash
cd skduo
npm install
npm run dev
```

`npm run dev` watches your Blade and CSS files. Edit `front-page.blade.php`, browser hot-reloads. The Vite dev server runs on a separate port; Sage handles the wiring.

### 7. Activate the theme

WP Admin → Appearance → Themes → activate `skduo`.

### 8. Create your first match

WP Admin → Mängud → Add New. Fill in title, then the meta box fields below the editor (home team, away team, date, etc.).

Visit your homepage. The `next-match-card` component picks up the next upcoming match and renders the countdown. Done.

## What you get

| File | What it does |
|---|---|
| `resources/css/app.css` | Tailwind v4 entry with Bigbank green palette tokens and component classes. |
| `resources/js/animations.js` | All 7 motion patterns (tilt, reveal, ticker, countdown, magnetic, kinetic, scroll progress). ~150 lines, no dependencies. |
| `inc/cpt-mang.php` | Match CPT with meta box (real form, not the WP custom-fields UI nightmare). |
| `inc/helpers.php` | `skduo_get_next_match()`, `skduo_get_upcoming_matches()`, and `skduo_match_data()`. Cached with transients. |
| `app/View/Composers/NextMatch.php` | Injects the next match into the homepage view automatically. View Composer pattern — data lives in PHP, views stay pure markup. |
| `resources/views/layouts/app.blade.php` | Master layout: header + content + footer. |
| `resources/views/front-page.blade.php` | Homepage. References the components. |
| `resources/views/components/next-match-card.blade.php` | The hero countdown card. |
| `resources/views/components/game-card.blade.php` | Reusable game card. |

## Daily workflow

```bash
# Start the dev server (one terminal, keep running)
npm run dev

# Edit Blade files in resources/views/, they hot-reload
# Edit Tailwind classes inline, they hot-reload
# Edit JS in resources/js/, it hot-reloads
# Edit PHP/CPTs in inc/, reload browser

# When ready to deploy:
npm run build      # builds optimized assets to public/build/
composer install --no-dev --optimize-autoloader
```

## What you DON'T need

- ACF (you have Blade + custom meta boxes; ACF is dead weight here)
- Page builders (you're writing components)
- A separate plugin for the CPTs (this is your project, single-site, ship it as the theme)
- Yoast/Rank Math right away (add later when you're ready for SEO polish)

## Deploying to production

Best Estonian options for Sage:
- **Veebimajutus.ee** Pro plan or higher (SSH + Composer access required)
- **Hetzner** + RunCloud or Ploi for management
- **Cloudways** (Estonia datacenter via DigitalOcean Frankfurt is fine for ET audience)

DO NOT use shared cheap hosting. You need SSH for `composer install` and `npm run build`.

## When you're ready to grow

Add these in this order:
1. **WooCommerce** for the shop. Sage works with Woo out of the box; you customize templates by copying them into `resources/views/woocommerce/`.
2. **Polylang** if you need ET/EN.
3. **LiteSpeed Cache** for performance.
4. **Bigbank Pay** WooCommerce gateway (ask Bigbank's B2B team directly).

Welcome to writing real PHP again instead of fighting Gutenberg.
