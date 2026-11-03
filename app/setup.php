<?php

/**
 * Theme setup.
 */

namespace App;

use function Roots\bundle;



/**
 * Register the initial theme setup.
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'footer_meeskonnad' => __('Footer: Meeskonnad', 'sage'),
        'footer_klubi' => __('Footer: Klubi', 'sage'),
        'footer_osavotta' => __('Footer: Osa võtta', 'sage'),
        'footer_kontakt' => __('Footer: Kontakt', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable the Site Identity logo picker.
     */
    add_theme_support('custom-logo', [
        'height'               => 84,
        'width'                => 84,
        'flex-height'          => true,
        'flex-width'           => true,
        'unlink-homepage-logo' => true,
    ]);

    /**
     * Enable responsive embed support.
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form', 'script', 'style']);

    /**
     * Enable selective refresh for widgets in customizer.
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Enable WooCommerce template support for the merch catalog.
     */
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}, 20);
