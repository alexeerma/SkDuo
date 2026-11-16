import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';

if (! process.env.APP_URL) {
  process.env.APP_URL = 'http://example.test';
}

export default defineConfig({
  base: '/wp-content/themes/skduo/public/build/',
  assetsInclude: ['**/*.glb'],
  build: {
    // three.module (~560 kB) is only reached via dynamic imports gated on
    // canvas data attributes in app.js, so it never loads on pages without 3D.
    chunkSizeWarningLimit: 600,
  },
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
      ],
      refresh: true,
      assets: ['resources/images/**', 'resources/fonts/**'],
    }),

    wordpressPlugin(),

    wordpressThemeJson({
      disableTailwindColors: false,
      disableTailwindFonts: false,
      disableTailwindFontSizes: false,
      disableTailwindBorderRadius: false,
    }),
  ],
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
})