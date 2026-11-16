<?php
/**
 * Helper functions for SK DUO.
 * Used by View Composers and templates.
 */

defined( 'ABSPATH' ) || exit;

function skduo_site_logo_markup( string $class = 'skduo-site-logo' ): string {
	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id ) {
		$image = wp_get_attachment_image( $custom_logo_id, 'full', false, [
			'class' => $class,
			'alt'   => get_bloginfo( 'name' ),
		] );

		if ( $image ) {
			return $image;
		}
	}

	$class = esc_attr( $class );

	return <<<HTML
<svg class="{$class}" viewBox="0 0 60 60" aria-hidden="true" focusable="false">
  <path d="M30 5 C18 8 8 18 8 30 C8 42 18 52 30 55 C30 38 22 30 22 22 C22 13 26 8 30 5 Z" fill="#6FC8B5"/>
  <path d="M30 5 C42 8 52 18 52 30 C52 42 42 52 30 55 C30 38 38 30 38 22 C38 13 34 8 30 5 Z" fill="#1F5A52"/>
</svg>
HTML;
}

function skduo_footer_logo_markup( string $class = 'skduo-site-logo' ): string {
	$footer_logo_id = (int) get_option( 'skduo_footer_logo_id', 0 );

	if ( $footer_logo_id ) {
		$image = wp_get_attachment_image( $footer_logo_id, 'full', false, [
			'class' => $class,
			'alt'   => get_bloginfo( 'name' ),
		] );

		if ( $image ) {
			return $image;
		}
	}

	return skduo_site_logo_markup( $class );
}

function skduo_header_logo_markup( string $class = 'skduo-site-logo skduo-header-logo' ): string {
	$class = esc_attr( $class );
	$src   = esc_url( get_theme_file_uri( 'assets/images/bigbank-tartu-volleyball-team.svg' ) );
	$alt   = esc_attr( get_bloginfo( 'name' ) ?: 'Bigbank Tartu' );

	return <<<HTML
<img class="{$class}" src="{$src}" alt="{$alt}" loading="eager" decoding="async">
HTML;
}

/**
 * Footer menu columns. Uses assigned WP menus when present, otherwise
 * falls back to sensible default links.
 */
function skduo_get_footer_menu_columns(): array {
	$columns = [
		'footer_meeskonnad' => [
			'title' => 'Meeskonnad',
			'items' => [
				[ 'title' => 'Bigbank Tartu', 'url' => home_url( '/meeskonnad' ), 'target' => '' ],
				[ 'title' => 'TÜ/Bigbank', 'url' => home_url( '/meeskonnad' ), 'target' => '' ],
				[ 'title' => 'DUO Noored', 'url' => home_url( '/noored' ), 'target' => '' ],
			],
		],
		'footer_klubi' => [
			'title' => 'Klubi',
			'items' => [
				[ 'title' => 'Klubist', 'url' => home_url( '/klubist' ), 'target' => '' ],
				[ 'title' => 'Ajalugu', 'url' => home_url( '/klubist' ), 'target' => '' ],
				[ 'title' => 'Kontakt', 'url' => home_url( '/kontakt' ), 'target' => '' ],
			],
		],
		'footer_osavotta' => [
			'title' => 'Osa võtta',
			'items' => [
				[ 'title' => 'Tule trenni', 'url' => home_url( '/noored' ), 'target' => '' ],
				[ 'title' => 'Osta pilet', 'url' => '#', 'target' => '' ],
				[ 'title' => 'E-pood', 'url' => home_url( '/epood' ), 'target' => '' ],
					[ 'title' => 'Saa partneriks', 'url' => home_url( '/tule-toetajaks' ), 'target' => '' ],
			],
		],
		'footer_kontakt' => [
			'title' => 'Kontakt',
			'items' => [
				[ 'title' => 'Ringtee 25, 50105 Tartu', 'url' => '#', 'target' => '' ],
				[ 'title' => 'info@skduo.ee', 'url' => 'mailto:info@skduo.ee', 'target' => '' ],
				[ 'title' => '+372 53 400 379', 'url' => 'tel:+37253400379', 'target' => '' ],
			],
		],
	];

	$locations = get_nav_menu_locations();

	foreach ( $columns as $location => $fallback ) {
		if ( empty( $locations[ $location ] ) ) {
			continue;
		}

		$menu_id = (int) $locations[ $location ];
		$menu    = wp_get_nav_menu_object( $menu_id );
		$items   = wp_get_nav_menu_items( $menu_id );

		if ( ! is_array( $items ) || empty( $items ) ) {
			continue;
		}

		$column_items = [];
		foreach ( $items as $item ) {
			if ( 0 !== (int) $item->menu_item_parent ) {
				continue;
			}

			$column_items[] = [
				'title' => (string) $item->title,
				'url' => (string) $item->url,
				'target' => (string) $item->target,
			];
		}

		if ( empty( $column_items ) ) {
			continue;
		}

		$columns[ $location ] = [
			'title' => $menu && ! empty( $menu->name ) ? (string) $menu->name : $fallback['title'],
			'items' => $column_items,
		];
	}

	return array_values( $columns );
}

function skduo_default_header_ticker_items(): array {
	return [
		'★ Järgmine mäng · 22.03 · TÜ Spordihoone',
		'Esimene trenn alati TASUTA',
		'Uus kollektsioon e-poes',
	];
}

function skduo_normalize_header_ticker_item( $item ): array {
	if ( is_array( $item ) ) {
		$text = trim( wp_strip_all_tags( (string) ( $item['text'] ?? '' ) ) );
		$url  = skduo_normalize_header_ticker_url( (string) ( $item['url'] ?? '' ) );
	} else {
		$raw = trim( wp_strip_all_tags( (string) $item ) );
		$url = '';

		if ( str_contains( $raw, '|' ) ) {
			$parts = array_map( 'trim', explode( '|', $raw, 2 ) );
			$text  = $parts[0] ?? '';
			$url   = isset( $parts[1] ) ? skduo_normalize_header_ticker_url( $parts[1] ) : '';
		} elseif ( preg_match( '~\b((?:https?://|www\.)[^\s<>"\']+)~i', $raw, $matches, PREG_OFFSET_CAPTURE ) ) {
			$url_match = $matches[1][0];
			$url_start = (int) $matches[1][1];
			$text      = trim( substr( $raw, 0, $url_start ) );
			$url       = skduo_normalize_header_ticker_url( $url_match );
		} else {
			$text = $raw;
		}
	}

	return [
		'text' => $text,
		'url'  => $url,
	];
}

function skduo_normalize_header_ticker_url( string $url ): string {
	$url = trim( wp_strip_all_tags( $url ) );
	$url = rtrim( $url, " \t\n\r\0\x0B.,;)" );

	if ( '' === $url ) {
		return '';
	}

	if (
		! preg_match( '~^[a-z][a-z0-9+.-]*:~i', $url )
		&& preg_match( '~^(?:www\.)?[a-z0-9.-]+\.[a-z]{2,}(?:[/:?#].*)?$~i', $url )
	) {
		$url = 'https://' . $url;
	}

	return esc_url_raw( $url, [ 'http', 'https', 'mailto', 'tel' ] );
}

/**
 * Get the announcement ticker items shown in the first header row.
 */
function skduo_get_header_ticker_items(): array {
	$items = get_option( 'skduo_header_ticker_items', [] );
	if ( is_string( $items ) ) {
		$items = preg_split( '/\R+/', $items ) ?: [];
	}
	$items = is_array( $items ) ? $items : [];
	$items = array_values( array_filter( array_map(
		function ( $item ): array {
			return skduo_normalize_header_ticker_item( $item );
		},
		$items
	), function ( array $item ): bool {
		return '' !== $item['text'];
	} ) );

	if ( ! empty( $items ) ) {
		return $items;
	}

	return array_values( array_filter( array_map(
		function ( $item ): array {
			return skduo_normalize_header_ticker_item( $item );
		},
		skduo_default_header_ticker_items()
	), function ( array $item ): bool {
		return '' !== $item['text'];
	} ) );
}

/**
 * Whether the top announcement ticker should be shown for the current page.
 */
function skduo_should_show_header_ticker( ?int $post_id = null ): bool {
	$post_id = $post_id ?: (int) get_queried_object_id();

	if ( ! $post_id && is_home() ) {
		$post_id = (int) get_option( 'page_for_posts' );
	}

	if ( ! $post_id ) {
		return true;
	}

	return '0' !== (string) get_post_meta( $post_id, '_show_header_ticker', true );
}

/**
 * Get the top contact bar settings (phone, email, live-match badge)
 * shown under the header ticker, from wp-admin > Page options > Contact bar.
 */
function skduo_get_contact_bar_data(): object {
	$defaults = [
		'phone'        => '+372 53 400 379',
		'email'        => 'info@skduo.ee',
		'live_enabled' => false,
		'live_text'    => 'Otseülekanne · L 22.03 19:00',
		'live_url'     => '',
	];

	$saved = get_option( 'skduo_contact_bar', [] );
	$saved = is_array( $saved ) ? $saved : [];

	return (object) [
		'phone'        => '' !== trim( (string) ( $saved['phone'] ?? '' ) ) ? trim( (string) $saved['phone'] ) : $defaults['phone'],
		'email'        => '' !== trim( (string) ( $saved['email'] ?? '' ) ) ? trim( (string) $saved['email'] ) : $defaults['email'],
		'live_enabled' => ! empty( $saved['live_enabled'] ),
		'live_text'    => '' !== trim( (string) ( $saved['live_text'] ?? '' ) ) ? trim( (string) $saved['live_text'] ) : $defaults['live_text'],
		'live_url'     => trim( (string) ( $saved['live_url'] ?? '' ) ),
	];
}

/**
 * Get homepage hero settings from wp-admin page meta.
 */
function skduo_get_home_hero_data( ?int $post_id = null ): object {
	$post_id = $post_id ?: (int) get_queried_object_id();

	$defaults = [
		'location'        => 'Tartu, Estonia',
		'season'          => 'Hooaeg 2025 / 26',
		'established'     => 'Est. 1999',
		'title_line_1'    => '25 aastat',
		'title_line_2'    => 'Tartu',
		'title_emphasis'  => 'võrkpalli',
		'subtitle'        => 'Üks Eesti suurimaid võrkpalliklubisid. Bigbank Tartu, esindusvõistkonnad ja sadu noori sportlasi ühe Tartu energia all.',
		'primary_label'   => 'Osta pilet',
		'primary_url'     => '/kalender',
		'secondary_label' => 'Tule trenni',
		'secondary_url'   => '/noored',
	];

	$global_text = get_option( 'skduo_home_hero_text', [] );
	$global_text = is_array( $global_text ) ? $global_text : [];
	$data        = [];

	foreach ( $defaults as $key => $default ) {
		$global_value = isset( $global_text[ $key ] ) ? (string) $global_text[ $key ] : '';
		$page_value   = $post_id ? get_post_meta( $post_id, '_' . $key, true ) : '';
		$value        = '' !== $global_value ? $global_value : $page_value;
		$data[ $key ] = '' !== $value ? $value : $default;
	}

	$global_image_ids = array_filter( array_map( 'absint', explode( ',', (string) get_option( 'skduo_hero_image_ids', '' ) ) ) );
	$page_image_ids   = $post_id
		? array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post_id, '_hero_image_ids', true ) ) ) )
		: [];
	$image_ids        = ! empty( $global_image_ids ) ? $global_image_ids : $page_image_ids;
	$image_ids        = array_slice( $image_ids, 0, 6 );

	$images = array_values( array_filter( array_map(
		function ( int $image_id ): string {
			return wp_get_attachment_image_url( $image_id, 'large' ) ?: '';
		},
		$image_ids
	) ) );

	if ( empty( $images ) ) {
		$images = array_map(
			function ( int $i ): string {
				return (string) \Roots\asset( "resources/images/hero/hero-{$i}.jpg" );
			},
			range( 1, 6 )
		);
	}

	$data['images'] = $images;

	return (object) $data;
}

/**
 * Get Arhiiv (gallery) seasons + images from the "Arhiivi galerii" settings page.
 *
 * @return object[] Each with `label`, `slug` (unique, for the season picker), and `images`.
 */
function skduo_get_arhiiv_data(): array {
	$seasons     = function_exists( 'skduo_get_arhiiv_seasons_raw' ) ? skduo_get_arhiiv_seasons_raw() : [];
	$used_slugs  = [];
	$result      = [];

	foreach ( $seasons as $index => $season ) {
		$label = (string) ( $season['label'] ?? '' );

		$images = array_values( array_filter( array_map(
			function ( int $image_id ): ?array {
				$url = wp_get_attachment_image_url( $image_id, 'large' );
				if ( ! $url ) {
					return null;
				}

				return [
					'id'  => $image_id,
					'url' => $url,
					'alt' => get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: '',
				];
			},
			(array) ( $season['image_ids'] ?? [] )
		) ) );

		if ( '' === $label && empty( $images ) ) {
			continue;
		}

		$slug = sanitize_title( $label );
		if ( '' === $slug || in_array( $slug, $used_slugs, true ) ) {
			$slug = 'hooaeg-' . ( $index + 1 );
		}
		$used_slugs[] = $slug;

		$result[] = (object) [
			'label'  => '' !== $label ? $label : sprintf( __( 'Hooaeg %d', 'skduo' ), $index + 1 ),
			'slug'   => $slug,
			'images' => $images,
		];
	}

	return $result;
}

/**
 * Get sponsor logo URLs shown as ad boards in the homepage 3D rally animation.
 * Looks up the "Tartu Linn" sponsor entry (toetaja CPT) for its logo; falls
 * back to '' when no such sponsor exists or has no logo set yet.
 */
function skduo_get_court_sponsor_logos(): object {
	$city_logo = '';

	if ( post_type_exists( 'toetaja' ) ) {
		$city_sponsor = get_posts( [
			'post_type'      => 'toetaja',
			'name'           => 'tartu-linn',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
		] );

		if ( ! empty( $city_sponsor ) ) {
			$city_logo = get_the_post_thumbnail_url( $city_sponsor[0], 'medium_large' ) ?: '';
		}
	}

	return (object) [
		'bigbank' => esc_url( get_theme_file_uri( 'assets/images/bigbank-wordmark.svg' ) ),
		'city'    => $city_logo ? esc_url( $city_logo ) : '',
	];
}

/**
 * Get latest WordPress posts for the homepage news bento.
 */
function skduo_get_latest_posts( int $limit = 6 ): array {
	$posts = get_posts( [
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $limit,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
	] );

	return array_map( 'skduo_post_card_data', $posts );
}

/**
 * Get post data in a Blade-friendly shape.
 */
function skduo_post_card_data( WP_Post $post ): object {
	$categories = get_the_category( $post->ID );
	$category   = ! empty( $categories ) ? $categories[0]->name : __( 'Uudis', 'skduo' );
	$excerpt_source = has_excerpt( $post )
		? get_the_excerpt( $post )
		: get_the_content( null, false, $post );
	$excerpt = wp_trim_words(
		wp_strip_all_tags( strip_shortcodes( $excerpt_source ) ),
		18,
		'...'
	);

	return (object) [
		'id'        => $post->ID,
		'title'     => get_the_title( $post ),
		'permalink' => get_permalink( $post ),
		'date'      => get_the_date( 'c', $post ),
		'date_label' => get_the_date( 'd.m.Y', $post ),
		'category'  => $category,
		'excerpt'   => $excerpt,
		'image'     => get_the_post_thumbnail_url( $post, 'large' ) ?: '',
	];
}

add_action( 'admin_notices', function (): void {
	if ( defined( 'SKDUO_CORE_VERSION' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html__( 'SK DUO theme requires the SK DUO Core plugin for matches, teams, players, sponsors, and API endpoints. Please install and activate it.', 'skduo' )
	);
} );

if ( ! function_exists( 'skduo_get_next_match' ) ) {
	function skduo_get_next_match(): ?WP_Post {
		return null;
	}
}

if ( ! function_exists( 'skduo_get_upcoming_matches' ) ) {
	function skduo_get_upcoming_matches( int $limit = 4 ): array {
		return [];
	}
}

if ( ! function_exists( 'skduo_match_data' ) ) {
	function skduo_match_data( WP_Post $post ): object {
		return (object) [];
	}
}

if ( ! function_exists( 'skduo_get_training_groups' ) ) {
	function skduo_get_training_groups(): array {
		return [];
	}
}

if ( ! function_exists( 'skduo_get_teams_with_players' ) ) {
	function skduo_get_teams_with_players(): array {
		return [];
	}
}

if ( ! function_exists( 'skduo_player_data' ) ) {
	function skduo_player_data( WP_Post $post ): object {
		return (object) [
			'id'          => $post->ID,
			'name'        => get_the_title( $post ),
			'permalink'   => get_permalink( $post ),
			'number'      => '',
			'position'    => '',
			'height'      => '',
			'weight'      => '',
			'spike'       => '',
			'block'       => '',
			'year'        => '',
			'profile_url' => '',
			'stats_url'   => '',
			'seasons'     => [],
			'season_slugs' => '',
			'season_images' => [],
			'team_id'     => 0,
			'team'        => '',
			'image'       => get_the_post_thumbnail_url( $post, 'medium_large' ) ?: '',
			'image_large' => get_the_post_thumbnail_url( $post, 'large' ) ?: '',
			'description' => apply_filters( 'the_content', $post->post_content ),
			'initials'    => '',
		];
	}
}
