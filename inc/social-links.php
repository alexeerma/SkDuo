<?php
/**
 * Social links and icons.
 */

defined( 'ABSPATH' ) || exit;

function skduo_social_platforms(): array {
	return [
		'facebook'  => __( 'Facebook', 'skduo' ),
		'x'         => __( 'X', 'skduo' ),
		'instagram' => __( 'Instagram', 'skduo' ),
		'youtube'   => __( 'YouTube', 'skduo' ),
	];
}

function skduo_get_social_links(): array {
	$links = [];

	foreach ( skduo_social_platforms() as $key => $label ) {
		$url = trim( (string) get_theme_mod( 'skduo_social_' . $key, '' ) );

		if ( '' === $url || '#' === $url ) {
			continue;
		}

		$links[ $key ] = [
			'label' => $label,
			'url'   => $url,
		];
	}

	return $links;
}

function skduo_get_header_cta(): array {
	$label = trim( (string) get_theme_mod( 'skduo_header_cta_label', __( 'Piletid', 'skduo' ) ) );
	$url   = trim( (string) get_theme_mod( 'skduo_header_cta_url', home_url( '/kalender' ) ) );

	return [
		'label' => '' !== $label ? $label : __( 'Piletid', 'skduo' ),
		'url'   => '' !== $url ? $url : home_url( '/kalender' ),
	];
}

function skduo_social_icon( string $platform ): string {
	$icons = [
		'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true" class="h-3.5 w-3.5 fill-current"><path d="M14 8.5h2.5V5.1A16.3 16.3 0 0 0 13.1 5c-3.4 0-5.7 2.1-5.7 5.9v3.3H4v3.8h3.4V24h4.1v-6h3.4l.6-3.8h-4v-3c0-1.1.3-2.7 2.5-2.7Z"/></svg>',
		'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true" class="h-3.5 w-3.5 fill-current"><path d="M18.9 2h3.4l-7.5 8.6L23.6 22h-6.9l-5.4-7-6.2 7H1.7l8-9.1L1.2 2h7.1l4.9 6.5L18.9 2Zm-1.2 18h1.9L7.3 3.9h-2L17.7 20Z"/></svg>',
		'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true" class="h-3.5 w-3.5 fill-current"><path d="M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2Zm0 2A3.8 3.8 0 0 0 4 7.8v8.4A3.8 3.8 0 0 0 7.8 20h8.4a3.8 3.8 0 0 0 3.8-3.8V7.8A3.8 3.8 0 0 0 16.2 4H7.8Zm4.2 3.1a4.9 4.9 0 1 1 0 9.8 4.9 4.9 0 0 1 0-9.8Zm0 2a2.9 2.9 0 1 0 0 5.8 2.9 2.9 0 0 0 0-5.8Zm5.1-2.4a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z"/></svg>',
		'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true" class="h-4 w-4 fill-current"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31.6 31.6 0 0 0 0 12a31.6 31.6 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31.6 31.6 0 0 0 24 12a31.6 31.6 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.3 3.6-6.3 3.6Z"/></svg>',
	];

	return $icons[ $platform ] ?? '';
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section( 'skduo_social_links', [
		'title'       => __( 'Header links', 'skduo' ),
		'description' => __( 'Add the ticket button URL and social profile URLs shown in the header and footer.', 'skduo' ),
		'priority'    => 45,
	] );

	$wp_customize->add_setting( 'skduo_header_cta_label', [
		'default'           => __( 'Piletid', 'skduo' ),
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'skduo_header_cta_label', [
		'label'   => __( 'Ticket button label', 'skduo' ),
		'section' => 'skduo_social_links',
		'type'    => 'text',
	] );

	$wp_customize->add_setting( 'skduo_header_cta_url', [
		'default'           => home_url( '/kalender' ),
		'sanitize_callback' => 'esc_url_raw',
	] );

	$wp_customize->add_control( 'skduo_header_cta_url', [
		'label'       => __( 'Ticket button URL', 'skduo' ),
		'section'     => 'skduo_social_links',
		'type'        => 'url',
		'input_attrs' => [
			'placeholder' => 'https://',
		],
	] );

	foreach ( skduo_social_platforms() as $key => $label ) {
		$setting_id = 'skduo_social_' . $key;

		$wp_customize->add_setting( $setting_id, [
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		] );

		$wp_customize->add_control( $setting_id, [
			'label'       => $label,
			'section'     => 'skduo_social_links',
			'type'        => 'url',
			'input_attrs' => [
				'placeholder' => 'https://',
			],
		] );
	}
} );
