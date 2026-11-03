<?php
/**
 * Global theme color options.
 */

defined( 'ABSPATH' ) || exit;

function skduo_theme_color_defaults(): array {
	return [
		'green_deep'   => [ 'label' => __( 'Primary green', 'skduo' ), 'value' => '#1F5A52', 'css' => '--color-green-deep' ],
		'green_mid'    => [ 'label' => __( 'Secondary green', 'skduo' ), 'value' => '#2A7A6F', 'css' => '--color-green-mid' ],
		'green_mint'   => [ 'label' => __( 'Accent mint', 'skduo' ), 'value' => '#6FC8B5', 'css' => '--color-green-mint' ],
		'green_pale'   => [ 'label' => __( 'Pale green', 'skduo' ), 'value' => '#C9E8DF', 'css' => '--color-green-pale' ],
		'ink'          => [ 'label' => __( 'Main text / dark background', 'skduo' ), 'value' => '#0A1518', 'css' => '--color-ink' ],
		'ink_2'        => [ 'label' => __( 'Secondary dark', 'skduo' ), 'value' => '#15282C', 'css' => '--color-ink-2' ],
		'paper'        => [ 'label' => __( 'Light background', 'skduo' ), 'value' => '#F8F5EE', 'css' => '--color-paper' ],
		'cream'        => [ 'label' => __( 'Soft background', 'skduo' ), 'value' => '#EDE7D7', 'css' => '--color-cream' ],
		'lime'         => [ 'label' => __( 'Highlight lime', 'skduo' ), 'value' => '#D4E84F', 'css' => '--color-lime' ],
		'coral'        => [ 'label' => __( 'Alert coral', 'skduo' ), 'value' => '#FF6B5B', 'css' => '--color-coral' ],
		'hero_bg'      => [ 'label' => __( 'Hero dark background', 'skduo' ), 'value' => '#0A1B30', 'css' => '--skduo-hero-bg' ],
		'section_dark' => [ 'label' => __( 'Dark section background', 'skduo' ), 'value' => '#07131F', 'css' => '--skduo-section-dark' ],
		'card_dark'    => [ 'label' => __( 'Dark card background', 'skduo' ), 'value' => '#0B1724', 'css' => '--skduo-card-dark' ],
	];
}

function skduo_get_theme_colors(): array {
	$saved    = get_option( 'skduo_theme_colors', [] );
	$defaults = skduo_theme_color_defaults();
	$colors   = [];

	foreach ( $defaults as $key => $field ) {
		$value = is_array( $saved ) && isset( $saved[ $key ] ) ? sanitize_hex_color( $saved[ $key ] ) : '';
		$colors[ $key ] = $value ?: $field['value'];
	}

	return $colors;
}

add_action( 'admin_menu', function (): void {
	add_submenu_page(
		'skduo-page-options',
		__( 'Theme colors', 'skduo' ),
		__( 'Theme colors', 'skduo' ),
		'manage_options',
		'skduo-theme-colors',
		'skduo_render_theme_colors_page'
	);
} );

add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
	if ( ! str_contains( $hook, 'skduo-theme-colors' ) ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
} );

function skduo_render_theme_colors_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$defaults = skduo_theme_color_defaults();

	if (
		isset( $_POST['skduo_theme_colors_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_theme_colors_nonce'] ) ), 'skduo_save_theme_colors' )
	) {
		$colors = [];
		$posted = isset( $_POST['skduo_theme_colors'] ) && is_array( $_POST['skduo_theme_colors'] )
			? wp_unslash( $_POST['skduo_theme_colors'] )
			: [];

		foreach ( $defaults as $key => $field ) {
			$value = isset( $posted[ $key ] ) ? sanitize_hex_color( $posted[ $key ] ) : '';
			$colors[ $key ] = $value ?: $field['value'];
		}

		update_option( 'skduo_theme_colors', $colors, false );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Theme colors saved.', 'skduo' ) . '</p></div>';
	}

	$colors = skduo_get_theme_colors();

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Theme colors', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Change the global colors used by backgrounds, headings, paragraph text, buttons, highlights, and dark homepage sections.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_theme_colors', 'skduo_theme_colors_nonce' ); ?>

			<table class="form-table" role="presentation">
				<tbody>
					<?php foreach ( $defaults as $key => $field ) : ?>
						<tr>
							<th scope="row">
								<label for="skduo-theme-color-<?php echo esc_attr( $key ); ?>">
									<?php echo esc_html( $field['label'] ); ?>
								</label>
							</th>
							<td>
								<input
									type="text"
									id="skduo-theme-color-<?php echo esc_attr( $key ); ?>"
									name="skduo_theme_colors[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( $colors[ $key ] ); ?>"
									class="skduo-color-field"
									data-default-color="<?php echo esc_attr( $field['value'] ); ?>"
								/>
								<p class="description"><?php echo esc_html( $field['css'] ); ?></p>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php submit_button( __( 'Save theme colors', 'skduo' ) ); ?>
		</form>
	</div>

	<script>
		jQuery(function($) {
			$('.skduo-color-field').wpColorPicker();
		});
	</script>
	<?php
}

add_action( 'wp_head', function (): void {
	$defaults = skduo_theme_color_defaults();
	$colors   = skduo_get_theme_colors();

	echo "<style id=\"skduo-theme-colors\">\n:root {\n";
	foreach ( $defaults as $key => $field ) {
		printf(
			"  %s: %s;\n",
			esc_html( $field['css'] ),
			esc_html( $colors[ $key ] )
		);
	}

	printf(
		"  --skduo-bigbank-gradient: linear-gradient(135deg, %s 0%%, %s 100%%);\n",
		esc_html( $colors['green_mint'] ),
		esc_html( $colors['green_deep'] )
	);
	printf(
		"  --skduo-hero-gradient: linear-gradient(135deg, %s 0%%, %s 100%%);\n",
		esc_html( $colors['green_deep'] ),
		esc_html( $colors['ink_2'] )
	);
	echo "}\n</style>\n";
}, 20 );
