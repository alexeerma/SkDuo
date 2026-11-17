<?php
/**
 * SEO output: meta description, canonical (non-singular), Open Graph, Twitter card,
 * Organization JSON-LD, html lang, and small front-end performance tweaks.
 *
 * Skipped entirely when a dedicated SEO plugin is active, to avoid duplicate tags.
 * Match (SportsEvent) JSON-LD lives in skduo-core (includes/seo-schema.php).
 */

defined( 'ABSPATH' ) || exit;

function skduo_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/* -------------------------------------------------------------------------
 * Settings (Customizer > Site Identity)
 * ---------------------------------------------------------------------- */

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_setting( 'skduo_default_og_image_id', [
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	] );

	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'skduo_default_og_image_id', [
		'label'       => __( 'Vaikimisi jagamispilt (OG pilt)', 'skduo' ),
		'description' => __( 'Kuvatakse Facebookis, WhatsAppis jm jagamisel, kui lehel endal esiletõstetud pilti pole. Soovituslik suurus 1200 × 630 px.', 'skduo' ),
		'section'     => 'title_tagline',
		'mime_type'   => 'image',
		'priority'    => 90,
	] ) );

	$wp_customize->add_setting( 'skduo_seo_org_name', [
		'type'              => 'option',
		'default'           => 'SK DUO',
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'skduo_seo_org_name', [
		'label'       => __( 'Klubi ametlik nimi (otsingumootoritele)', 'skduo' ),
		'description' => __( 'Kasutatakse struktureeritud andmetes (Google).', 'skduo' ),
		'section'     => 'title_tagline',
		'type'        => 'text',
		'priority'    => 91,
	] );

	$wp_customize->add_setting( 'skduo_seo_street_address', [
		'type'              => 'option',
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'skduo_seo_street_address', [
		'label'       => __( 'Klubi aadress (tänav ja maja)', 'skduo' ),
		'description' => __( 'Nt "Ujula 4". Linn (Tartu) ja riik lisatakse automaatselt.', 'skduo' ),
		'section'     => 'title_tagline',
		'type'        => 'text',
		'priority'    => 92,
	] );

	$wp_customize->add_setting( 'skduo_seo_postal_code', [
		'type'              => 'option',
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'skduo_seo_postal_code', [
		'label'    => __( 'Postiindeks', 'skduo' ),
		'section'  => 'title_tagline',
		'type'     => 'text',
		'priority' => 93,
	] );
} );

/* -------------------------------------------------------------------------
 * Data helpers
 * ---------------------------------------------------------------------- */

function skduo_seo_org_name(): string {
	$name = trim( (string) get_option( 'skduo_seo_org_name', 'SK DUO' ) );
	return '' !== $name ? $name : 'SK DUO';
}

function skduo_seo_clean_text( string $text, int $max = 160 ): string {
	$text = wp_strip_all_tags( strip_shortcodes( $text ), true );
	$text = trim( (string) preg_replace( '/\s+/u', ' ', $text ) );

	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}

	$cut   = mb_substr( $text, 0, $max - 1 );
	$space = mb_strrpos( $cut, ' ' );
	if ( false !== $space && $space > $max * 0.6 ) {
		$cut = mb_substr( $cut, 0, $space );
	}

	return rtrim( $cut, " ,.;:-" ) . '…';
}

function skduo_seo_home_subtitle(): string {
	$saved = get_option( 'skduo_home_hero_text', [] );
	$saved = is_array( $saved ) ? $saved : [];
	$text  = trim( (string) ( $saved['subtitle'] ?? '' ) );

	if ( '' === $text && function_exists( 'skduo_home_hero_fields' ) ) {
		$text = (string) ( skduo_home_hero_fields()['subtitle'][2] ?? '' );
	}

	return $text;
}

/**
 * Hero copy from Page options for pages that have it.
 */
function skduo_seo_page_options_text( string $slug ): string {
	if ( 'noored' === $slug && function_exists( 'skduo_get_noored_options' ) ) {
		return (string) ( skduo_get_noored_options()['hero_subtitle'] ?? '' );
	}

	if ( function_exists( 'skduo_get_page_copy_options' ) ) {
		$options = skduo_get_page_copy_options( str_replace( '-', '_', $slug ) );
		return (string) ( $options['hero_text'] ?? '' );
	}

	return '';
}

function skduo_seo_description(): string {
	$text = '';

	if ( is_front_page() ) {
		$text = skduo_seo_home_subtitle();
	} elseif ( is_home() ) {
		$text = skduo_seo_page_options_text( 'uudised' );
	} elseif ( is_singular( 'mang' ) && function_exists( 'skduo_match_data' ) ) {
		$match = skduo_match_data( get_queried_object() );
		$parts = array_filter( [
			( '' !== $match->home && '' !== $match->away ) ? $match->home . ' vs ' . $match->away : $match->title,
			$match->date_ts ? wp_date( 'd.m.Y H:i', $match->date_ts ) : '',
			$match->venue,
			$match->league,
		] );
		$text = implode( ' · ', $parts );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			if ( has_excerpt( $post ) ) {
				$text = $post->post_excerpt;
			} elseif ( 'page' === $post->post_type ) {
				$text = skduo_seo_page_options_text( $post->post_name );
			}

			if ( '' === trim( $text ) ) {
				$text = $post->post_content;
			}
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$text = term_description();
	} elseif ( is_post_type_archive() ) {
		$text = (string) get_the_archive_description();
	}

	$text = skduo_seo_clean_text( (string) $text );

	if ( '' === $text ) {
		$text = skduo_seo_clean_text( get_bloginfo( 'description' ) ?: skduo_seo_home_subtitle() );
	}

	return $text;
}

/**
 * Canonical URL for the current request. Core prints rel=canonical for singular views only.
 */
function skduo_seo_canonical_url(): string {
	if ( is_404() || is_search() ) {
		return '';
	}

	if ( is_singular() ) {
		return (string) wp_get_canonical_url();
	}

	$url = '';
	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_home() ) {
		$url = (string) get_permalink( (int) get_option( 'page_for_posts' ) );
	} elseif ( is_post_type_archive() ) {
		$type = get_query_var( 'post_type' );
		$url  = (string) get_post_type_archive_link( is_array( $type ) ? (string) reset( $type ) : (string) $type );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		$url  = is_wp_error( $link ) ? '' : $link;
	}

	if ( '' === $url ) {
		return '';
	}

	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		global $wp_rewrite;
		$url = $wp_rewrite->using_permalinks()
			? user_trailingslashit( trailingslashit( $url ) . $wp_rewrite->pagination_base . '/' . $paged, 'paged' )
			: add_query_arg( 'paged', $paged, $url );
	}

	return $url;
}

/**
 * Share image: featured image, then the default OG image, then the site logo.
 *
 * @return array{url:string,width:int,height:int,alt:string}|null
 */
function skduo_seo_image(): ?array {
	$ids = [];

	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$ids[] = (int) get_post_thumbnail_id( get_queried_object_id() );
	}

	$ids[] = absint( get_option( 'skduo_default_og_image_id', 0 ) );
	$ids[] = absint( get_theme_mod( 'custom_logo', 0 ) );

	foreach ( array_filter( $ids ) as $id ) {
		// Facebook, X and messengers do not render SVG share images.
		if ( 'image/svg+xml' === get_post_mime_type( $id ) ) {
			continue;
		}

		$src = wp_get_attachment_image_src( $id, 'large' );
		if ( $src ) {
			return [
				'url'    => (string) $src[0],
				'width'  => (int) $src[1],
				'height' => (int) $src[2],
				'alt'    => trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ),
			];
		}
	}

	return null;
}

function skduo_seo_logo_url(): string {
	foreach ( [ absint( get_theme_mod( 'custom_logo', 0 ) ), absint( get_option( 'site_icon', 0 ) ) ] as $id ) {
		$url = $id ? wp_get_attachment_image_url( $id, 'full' ) : '';
		if ( $url ) {
			return (string) $url;
		}
	}

	return '';
}

function skduo_seo_organization_schema(): array {
	$org = [
		'@type'        => 'SportsOrganization',
		'@id'          => home_url( '/#organization' ),
		'name'         => skduo_seo_org_name(),
		'url'          => home_url( '/' ),
		'sport'        => 'Volleyball',
		'foundingDate' => '1999',
	];

	$logo = skduo_seo_logo_url();
	if ( '' !== $logo ) {
		$org['logo'] = $logo;
	}

	if ( function_exists( 'skduo_get_social_links' ) ) {
		$same_as = array_values( array_filter( array_map(
			static fn ( array $link ): string => esc_url_raw( (string) $link['url'] ),
			skduo_get_social_links()
		) ) );
		if ( $same_as ) {
			$org['sameAs'] = $same_as;
		}
	}

	if ( function_exists( 'skduo_get_contact_bar_data' ) ) {
		$contact = skduo_get_contact_bar_data();
		if ( is_email( $contact->email ) ) {
			$org['email'] = $contact->email;
		}
		if ( '' !== $contact->phone ) {
			$org['telephone'] = $contact->phone;
		}
	}

	$address = [
		'@type'           => 'PostalAddress',
		'addressLocality' => 'Tartu',
		'addressCountry'  => 'EE',
	];
	$street = trim( (string) get_option( 'skduo_seo_street_address', '' ) );
	$postal = trim( (string) get_option( 'skduo_seo_postal_code', '' ) );
	if ( '' !== $street ) {
		$address['streetAddress'] = $street;
	}
	if ( '' !== $postal ) {
		$address['postalCode'] = $postal;
	}
	$org['address'] = $address;

	return $org;
}

function skduo_seo_print_json_ld( array $data ): void {
	$json = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG );
	if ( $json ) {
		echo '<script type="application/ld+json">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/* -------------------------------------------------------------------------
 * Front-end output
 * ---------------------------------------------------------------------- */

// Site content is Estonian even when the WP admin locale is not.
add_filter( 'language_attributes', function ( string $output ): string {
	if ( is_admin() ) {
		return $output;
	}

	return (string) preg_replace( '/lang="[^"]*"/', 'lang="et"', $output );
} );

// Match events use the same organization node as the club schema.
// Match events without their own photo: default share image, then a raster logo.
add_filter( 'skduo_schema_default_image', function ( string $image ): string {
	if ( '' !== $image ) {
		return $image;
	}

	foreach ( [ absint( get_option( 'skduo_default_og_image_id', 0 ) ), absint( get_theme_mod( 'custom_logo', 0 ) ) ] as $id ) {
		if ( ! $id || 'image/svg+xml' === get_post_mime_type( $id ) ) {
			continue;
		}

		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( $url ) {
			return (string) $url;
		}
	}

	// Last resort: bundled team photo, so events never ship without an image.
	return function_exists( '\Roots\asset' ) ? (string) \Roots\asset( 'resources/images/hero/hero-1.jpg' ) : '';
} );

// Paid matches without their own ticket link point to the header "Piletid" target.
add_filter( 'skduo_schema_ticket_url', function ( string $url ): string {
	if ( function_exists( 'skduo_get_header_cta' ) ) {
		$cta = skduo_get_header_cta();
		if ( ! empty( $cta['url'] ) ) {
			return (string) $cta['url'];
		}
	}

	return $url;
} );

add_filter( 'skduo_schema_organizer', function ( array $organizer ): array {
	return [
		'@type' => 'SportsOrganization',
		'@id'   => home_url( '/#organization' ),
		'name'  => skduo_seo_org_name(),
		'url'   => home_url( '/' ),
	];
} );

add_action( 'wp_head', function (): void {
	if ( is_admin() || is_feed() || skduo_seo_plugin_active() ) {
		return;
	}

	$description = skduo_seo_description();
	$canonical   = skduo_seo_canonical_url();
	$title       = wp_get_document_title();
	$image       = skduo_seo_image();
	$is_article  = is_singular( 'post' );
	$og_url      = '' !== $canonical ? $canonical : home_url( add_query_arg( [] ) );

	if ( '' !== $description ) {
		printf( "<meta name=\"description\" content=\"%s\" />\n", esc_attr( $description ) );
	}

	if ( '' !== $canonical && ! is_singular() ) {
		printf( "<link rel=\"canonical\" href=\"%s\" />\n", esc_url( $canonical ) );
	}

	$og = [
		'og:locale'    => 'et_EE',
		'og:type'      => $is_article ? 'article' : 'website',
		'og:site_name' => get_bloginfo( 'name' ),
		'og:title'     => $title,
		'og:description' => $description,
		'og:url'       => $og_url,
	];

	if ( $image ) {
		$og['og:image']        = $image['url'];
		$og['og:image:width']  = (string) $image['width'];
		$og['og:image:height'] = (string) $image['height'];
		if ( '' !== $image['alt'] ) {
			$og['og:image:alt'] = $image['alt'];
		}
	}

	if ( $is_article ) {
		$og['article:published_time'] = (string) get_the_date( 'c', get_queried_object() );
		$og['article:modified_time']  = (string) get_the_modified_date( 'c', get_queried_object() );
	}

	foreach ( $og as $property => $content ) {
		if ( '' === (string) $content ) {
			continue;
		}
		$value = in_array( $property, [ 'og:url', 'og:image' ], true ) ? esc_url( $content ) : esc_attr( $content );
		printf( "<meta property=\"%s\" content=\"%s\" />\n", esc_attr( $property ), $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	printf( "<meta name=\"twitter:card\" content=\"%s\" />\n", $image ? 'summary_large_image' : 'summary' );
	printf( "<meta name=\"twitter:title\" content=\"%s\" />\n", esc_attr( $title ) );
	if ( '' !== $description ) {
		printf( "<meta name=\"twitter:description\" content=\"%s\" />\n", esc_attr( $description ) );
	}
	if ( $image ) {
		printf( "<meta name=\"twitter:image\" content=\"%s\" />\n", esc_url( $image['url'] ) );
	}

	if ( is_front_page() ) {
		skduo_seo_print_json_ld( [
			'@context' => 'https://schema.org',
			'@graph'   => [
				skduo_seo_organization_schema(),
				[
					'@type'      => 'WebSite',
					'@id'        => home_url( '/#website' ),
					'url'        => home_url( '/' ),
					'name'       => get_bloginfo( 'name' ),
					'inLanguage' => 'et',
					'publisher'  => [ '@id' => home_url( '/#organization' ) ],
				],
			],
		] );
	}
}, 2 );

/* -------------------------------------------------------------------------
 * Performance
 * ---------------------------------------------------------------------- */

// Emoji detection script + styles: modern browsers render emoji natively.
add_action( 'init', function (): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	add_filter( 'emoji_svg_url', '__return_false' );
} );

// WooCommerce "Coming soon" injects a ~320 KB Inter variable font into every page's
// @font-face list; Blade pages load Inter from Google Fonts already. Keep it only on
// the coming-soon screen itself.
add_action( 'wp_head', function (): void {
	if ( function_exists( 'skduo_is_woocommerce_coming_soon_request' ) && skduo_is_woocommerce_coming_soon_request() ) {
		return;
	}

	remove_action( 'wp_head', 'wp_print_font_faces', 50 );
}, 1 );
