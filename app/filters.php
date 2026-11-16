<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Estonian <title> for 404 pages (core default is "Page not found" when no et locale is installed).
 */
add_filter('document_title_parts', function (array $parts) {
    if (is_404()) {
        $parts['title'] = __('Lehte ei leitud', 'skduo');
    }

    return $parts;
});

/**
 * Force the player detail template for single Mängija posts.
 */
add_filter('single_template_hierarchy', function (array $templates) {
    if (is_singular('mangija')) {
        array_unshift($templates, 'single-mangija.php');
    }

    return $templates;
}, 5);

/**
 * Ensure /mangija/{slug}/ resolves as a single player request even if
 * permalinks have not been refreshed since the CPT was registered.
 */
add_filter('request', function (array $queryVars) {
    if (! empty($queryVars['post_type']) || ! empty($queryVars['name'])) {
        return $queryVars;
    }

    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if (! preg_match('#^mangija/([^/]+)/?$#', $path, $matches)) {
        return $queryVars;
    }

    $queryVars['post_type'] = 'mangija';
    $queryVars['name'] = sanitize_title($matches[1]);

    return $queryVars;
});

/**
 * Last-resort Acorn view override for player singles.
 */
add_filter('template_include', function ($template) {
    if (is_singular('mangija')) {
        app()['sage.view'] = 'single-mangija';

        return get_template_directory() . '/index.php';
    }

    if (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
        app()['sage.view'] = 'archive-product';

        return get_template_directory() . '/index.php';
    }

    if (function_exists('is_product') && is_product()) {
        app()['sage.view'] = 'single-product';

        return get_template_directory() . '/index.php';
    }

    return $template;
}, 101);
