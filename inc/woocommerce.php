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

/**
 * Presentation data for one product card (archive grid + related products).
 *
 * @param WC_Product $product
 * @param array      $page_options E-pood page copy options.
 */
function skduo_product_card_data( $product, array $page_options = [] ): array {
	$id          = $product->get_id();
	$is_external = $product->is_type( 'external' );
	$image_id    = (int) $product->get_image_id();
	$terms       = get_the_terms( $id, 'product_cat' );
	$category    = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0]->name : '';

	$button_text = $is_external
		? ( $product->get_button_text() ?: ( $page_options['external_button_fallback'] ?? __( 'Osta partneri e-poest', 'skduo' ) ) )
		: ( $page_options['internal_button_fallback'] ?? __( 'Vaata toodet', 'skduo' ) );

	$image_html = $image_id ? wp_get_attachment_image(
		$image_id,
		'woocommerce_single',
		false,
		[
			'class'   => 'skduo-product-card__image',
			'alt'     => get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: $product->get_name(),
			'loading' => 'lazy',
			'sizes'   => '(min-width: 1280px) 22vw, (min-width: 1024px) 30vw, 46vw',
		]
	) : '';

	return [
		'id'          => $id,
		'title'       => $product->get_name(),
		'url'         => $is_external ? $product->get_product_url() : get_permalink( $id ),
		'is_external' => $is_external,
		'button_text' => $button_text,
		'price_html'  => $product->get_price_html(),
		'image_html'  => $image_html,
		'category'    => $category,
		'on_sale'     => $product->is_on_sale(),
		'sale_label'  => $page_options['sale_badge'] ?? __( 'Soodus', 'skduo' ),
	];
}

/**
 * Parse "Pealkiri | tekst" lines (one per line) into info rows for the single product page.
 */
function skduo_parse_product_info_items( string $raw ): array {
	$items = [];

	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			continue;
		}

		$parts   = array_map( 'trim', explode( '|', $line, 2 ) );
		$items[] = [
			'label' => $parts[0],
			'text'  => $parts[1] ?? '',
		];
	}

	return $items;
}
