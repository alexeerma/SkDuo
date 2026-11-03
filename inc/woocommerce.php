<?php
/**
 * WooCommerce theme integration.
 *
 * `add_theme_support('woocommerce')` in app/setup.php tells WooCommerce this theme styles
 * its own templates, so we drop the plugin's classic stylesheets (cart/my-account shortcode
 * markup) and restyle everything to match the site's design tokens in app.css.
 *
 * The Cart and Checkout pages use WooCommerce Blocks (React), so their stylesheets are left
 * enqueued — those aren't just visual, they're load-bearing for the block markup/interactivity.
 * Only the visual layer on top (resources/css/app.css) is overridden for those.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function (): void {
	wp_dequeue_style( 'woocommerce-general' );
	wp_dequeue_style( 'woocommerce-layout' );
	wp_dequeue_style( 'woocommerce-smallscreen' );
}, 20 );
