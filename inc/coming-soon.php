<?php
/**
 * On-brand override for WooCommerce's "Coming soon" store screen.
 *
 * WooCommerce (Launch Your Store) renders that screen via its own block-template
 * pipeline, bypassing our Blade layout entirely (no header.php in this theme, so
 * get_header()/wp_head() run through WordPress's block-template canvas instead of
 * layouts/app.blade.php). templates/coming-soon.html in the theme root is WordPress's
 * own override point for that block template (core checks the active theme's
 * templates/ directory before falling back to the plugin's copy) — see
 * WooCommerce's ComingSoonRequestHandler::handle_template_include() and
 * BlockTemplatesController::get_block_file_template().
 *
 * Since that canvas still fires wp_head()/wp_enqueue_scripts, we enqueue the theme's
 * compiled app.css + fonts here so templates/coming-soon.html can use the same
 * Tailwind classes as the rest of the site.
 */

defined( 'ABSPATH' ) || exit;

function skduo_get_vite_asset_url( string $entry ): string {
	static $manifest = null;

	if ( null === $manifest ) {
		$path     = get_template_directory() . '/public/build/manifest.json';
		$manifest = file_exists( $path ) ? json_decode( (string) file_get_contents( $path ), true ) : [];
		$manifest = is_array( $manifest ) ? $manifest : [];
	}

	if ( empty( $manifest[ $entry ]['file'] ) ) {
		return '';
	}

	return get_template_directory_uri() . '/public/build/' . $manifest[ $entry ]['file'];
}

/**
 * Mirrors ComingSoonHelper::is_current_page_coming_soon() without depending on WC internals
 * — used only to avoid double-enqueuing app.css on normal (Blade-rendered) pages, which already
 * load it via @vite() in layouts/app.blade.php.
 */
function skduo_is_woocommerce_coming_soon_request(): bool {
	if ( ! function_exists( 'is_shop' ) || 'yes' !== get_option( 'woocommerce_coming_soon' ) ) {
		return false;
	}

	if ( current_user_can( 'manage_woocommerce' ) ) {
		return false;
	}

	if ( 'yes' !== get_option( 'woocommerce_store_pages_only' ) ) {
		return true; // Whole-site coming soon mode.
	}

	return is_shop() || is_product_taxonomy() || is_singular( 'product' ) || is_cart() || is_checkout() || is_account_page();
}

add_action( 'wp_enqueue_scripts', function (): void {
	if ( ! skduo_is_woocommerce_coming_soon_request() ) {
		return;
	}

	wp_enqueue_style(
		'skduo-fonts',
		'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;700;800&display=swap',
		[],
		null
	);

	$app_css = skduo_get_vite_asset_url( 'resources/css/app.css' );
	if ( '' !== $app_css ) {
		wp_enqueue_style( 'skduo-app', $app_css, [], null );
	}
}, 20 );
