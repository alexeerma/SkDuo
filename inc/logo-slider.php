<?php
/**
 * Customizer-driven partner logo slider.
 */

defined( 'ABSPATH' ) || exit;

function skduo_logo_slider_slot_count(): int {
	return 12;
}

function skduo_get_logo_slider_items(): array {
	$items = [];

	for ( $i = 1; $i <= skduo_logo_slider_slot_count(); $i++ ) {
		$logo  = trim( (string) get_theme_mod( "skduo_logo_slider_logo_{$i}", '' ) );
		$name  = trim( (string) get_theme_mod( "skduo_logo_slider_name_{$i}", '' ) );
		$link  = trim( (string) get_theme_mod( "skduo_logo_slider_url_{$i}", '' ) );

		if ( '' === $logo ) {
			continue;
		}

		$items[] = [
			'name' => '' !== $name ? $name : sprintf( __( 'Partner %d', 'skduo' ), $i ),
			'logo' => $logo,
			'url'  => $link,
		];
	}

	if ( ! empty( $items ) || ! post_type_exists( 'toetaja' ) ) {
		return $items;
	}

	$sponsors = get_posts( [
		'post_type'      => 'toetaja',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
		'order'          => 'ASC',
	] );

	foreach ( $sponsors as $sponsor ) {
		$logo = get_the_post_thumbnail_url( $sponsor, 'medium_large' ) ?: '';

		if ( '' === $logo ) {
			continue;
		}

		$items[] = [
			'name' => get_the_title( $sponsor ),
			'logo' => $logo,
			'url'  => (string) get_post_meta( $sponsor->ID, '_website', true ),
		];
	}

	return $items;
}

function skduo_get_footer_logo_slider_title(): string {
	return trim( (string) get_theme_mod( 'skduo_footer_logo_slider_title', __( 'Meie toetajad', 'skduo' ) ) );
}

function skduo_render_logo_slider( array $args = [] ): string {
	$defaults = [
		'eyebrow' => __( 'Partnerid', 'skduo' ),
		'title'   => __( 'Meie toetajad', 'skduo' ),
		'theme'   => 'paper',
	];
	$args  = wp_parse_args( $args, $defaults );
	$items = skduo_get_logo_slider_items();

	if ( empty( $items ) ) {
		return '';
	}

	$is_dark = 'dark' === $args['theme'];
	$theme_class = $is_dark
		? 'skduo-logo-slider--dark bg-ink text-paper'
		: 'skduo-logo-slider--paper bg-paper text-ink';
	$logo_count = count( $items );
	$track_items = $logo_count > 1 ? array_merge( $items, $items ) : $items;
	$has_heading = '' !== trim( (string) $args['eyebrow'] ) || '' !== trim( (string) $args['title'] );

	ob_start();
	?>
	<section class="skduo-logo-slider overflow-hidden <?php echo esc_attr( $theme_class ); ?>">
		<div class="mx-auto max-w-none px-0 py-10 md:py-12">
			<?php if ( $has_heading ) : ?>
				<div class="skduo-logo-slider__heading mx-auto mb-8 max-w-[1360px] px-4 text-center sm:px-6 md:px-10">
					<?php if ( '' !== trim( (string) $args['eyebrow'] ) ) : ?>
						<span class="skduo-logo-slider__eyebrow mb-2 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
							<span class="h-0.5 w-8 bg-green-deep"></span>
							<?php echo esc_html( (string) $args['eyebrow'] ); ?>
						</span>
					<?php endif; ?>
					<?php if ( '' !== trim( (string) $args['title'] ) ) : ?>
						<h2 class="skduo-logo-slider__title font-display text-3xl leading-none tracking-wide sm:text-4xl">
							<?php echo esc_html( (string) $args['title'] ); ?>
						</h2>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="skduo-logo-slider__viewport" style="--skduo-logo-count: <?php echo esc_attr( (string) max( 1, $logo_count ) ); ?>">
				<div class="skduo-logo-slider__track<?php echo $logo_count > 1 ? '' : ' skduo-logo-slider__track--static'; ?>">
					<?php foreach ( $track_items as $item ) : ?>
						<?php
						$name = (string) $item['name'];
						$link = trim( (string) $item['url'] );
						$host = '' !== $link ? parse_url( $link, PHP_URL_HOST ) : '';
						$home_host = parse_url( home_url( '/' ), PHP_URL_HOST );
						$is_external = $host && $host !== $home_host;
						?>
						<?php if ( '' !== $link ) : ?>
							<a
								href="<?php echo esc_url( $link ); ?>"
								class="skduo-logo-slider__item"
								aria-label="<?php echo esc_attr( $name ); ?>"
								<?php if ( $is_external ) : ?>target="_blank" rel="noopener"<?php endif; ?>
							>
						<?php else : ?>
							<div class="skduo-logo-slider__item" aria-label="<?php echo esc_attr( $name ); ?>">
						<?php endif; ?>

							<img src="<?php echo esc_url( (string) $item['logo'] ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">

						<?php if ( '' !== $link ) : ?>
							</a>
						<?php else : ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php

	return (string) ob_get_clean();
}

add_shortcode( 'skduo_logo_slider', function ( $atts = [] ): string {
	$atts = shortcode_atts( [
		'eyebrow' => __( 'Partnerid', 'skduo' ),
		'title'   => __( 'Meie toetajad', 'skduo' ),
		'theme'   => 'paper',
	], (array) $atts, 'skduo_logo_slider' );

	return skduo_render_logo_slider( [
		'eyebrow' => sanitize_text_field( (string) $atts['eyebrow'] ),
		'title'   => sanitize_text_field( (string) $atts['title'] ),
		'theme'   => 'dark' === $atts['theme'] ? 'dark' : 'paper',
	] );
} );

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section( 'skduo_logo_slider', [
		'title'       => __( 'Logo slider', 'skduo' ),
		'description' => __( 'Add partner logos and partner URLs for the reusable logo slider component. Use it in page content with [skduo_logo_slider].', 'skduo' ),
		'priority'    => 47,
	] );

	$wp_customize->add_setting( 'skduo_footer_logo_slider_title', [
		'default'           => __( 'Meie toetajad', 'skduo' ),
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'skduo_footer_logo_slider_title', [
		'label'       => __( 'Footer slider title', 'skduo' ),
		'section'     => 'skduo_logo_slider',
		'type'        => 'text',
		'description' => __( 'Shown above the logo slider at the top of the footer. Leave empty to hide it.', 'skduo' ),
	] );

	for ( $i = 1; $i <= skduo_logo_slider_slot_count(); $i++ ) {
		$wp_customize->add_setting( "skduo_logo_slider_logo_{$i}", [
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		] );

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				"skduo_logo_slider_logo_{$i}",
				[
					'label'       => sprintf( __( 'Partner %d logo', 'skduo' ), $i ),
					'section'     => 'skduo_logo_slider',
					'description' => __( 'Choose a logo from the Media Library.', 'skduo' ),
				]
			)
		);

		$wp_customize->add_setting( "skduo_logo_slider_name_{$i}", [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		] );

		$wp_customize->add_control( "skduo_logo_slider_name_{$i}", [
			'label'   => sprintf( __( 'Partner %d name', 'skduo' ), $i ),
			'section' => 'skduo_logo_slider',
			'type'    => 'text',
		] );

		$wp_customize->add_setting( "skduo_logo_slider_url_{$i}", [
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		] );

		$wp_customize->add_control( "skduo_logo_slider_url_{$i}", [
			'label'       => sprintf( __( 'Partner %d URL', 'skduo' ), $i ),
			'section'     => 'skduo_logo_slider',
			'type'        => 'url',
			'input_attrs' => [
				'placeholder' => 'https://',
			],
		] );
	}
} );
