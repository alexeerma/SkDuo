<?php
/**
 * Site Identity customizer adjustments.
 */

defined( 'ABSPATH' ) || exit;

function skduo_sanitize_checkbox( mixed $value ): bool {
	return (bool) $value;
}

function skduo_get_header_brand(): array {
	$show     = (bool) get_theme_mod( 'skduo_header_brand_show_text', false );
	$title    = trim( (string) get_theme_mod( 'skduo_header_brand_title', get_bloginfo( 'name' ) ?: 'SK DUO' ) );
	$subtitle = trim( (string) get_theme_mod( 'skduo_header_brand_subtitle', get_bloginfo( 'description' ) ?: 'Tartu Võrkpalliklubi · Est. 1999' ) );

	return [
		'show_text' => $show,
		'title'     => '' !== $title ? $title : ( get_bloginfo( 'name' ) ?: 'SK DUO' ),
		'subtitle'  => $subtitle,
	];
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ): void {
	$section = $wp_customize->get_section( 'title_tagline' );
	if ( $section ) {
		$section->description = __( 'Manage the site logo, title, tagline, and favicon/site icon.', 'skduo' );
	}

	$custom_logo = $wp_customize->get_control( 'custom_logo' );
	if ( $custom_logo ) {
		$custom_logo->description = __( 'This logo is used in the footer, unless a footer logo override is set below.', 'skduo' );
	}

	$site_icon = $wp_customize->get_control( 'site_icon' );
	if ( $site_icon ) {
		$site_icon->description = __( 'This image is used as the browser favicon and app icon.', 'skduo' );
	}

	$wp_customize->add_setting( 'skduo_header_brand_show_text', [
		'default'           => false,
		'sanitize_callback' => 'skduo_sanitize_checkbox',
	] );

	$wp_customize->add_control( 'skduo_header_brand_show_text', [
		'label'       => __( 'Show header brand text next to logo', 'skduo' ),
		'description' => __( 'When disabled, the header shows only the logo on the left.', 'skduo' ),
		'section'     => 'title_tagline',
		'type'        => 'checkbox',
	] );

	$wp_customize->add_setting( 'skduo_footer_logo_id', [
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	] );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'skduo_footer_logo_id', [
		'label'       => __( 'Footer logo override', 'skduo' ),
		'description' => __( 'Optional. Leave empty to use the site logo above in the footer.', 'skduo' ),
		'section'     => 'title_tagline',
	] ) );

	$wp_customize->add_setting( 'skduo_header_brand_title', [
		'default'           => get_bloginfo( 'name' ) ?: 'SK DUO',
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'skduo_header_brand_title', [
		'label'   => __( 'Header brand title', 'skduo' ),
		'section' => 'title_tagline',
		'type'    => 'text',
	] );

	$wp_customize->add_setting( 'skduo_header_brand_subtitle', [
		'default'           => get_bloginfo( 'description' ) ?: 'Tartu Võrkpalliklubi · Est. 1999',
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'skduo_header_brand_subtitle', [
		'label'   => __( 'Header brand subtitle', 'skduo' ),
		'section' => 'title_tagline',
		'type'    => 'text',
	] );
} );
