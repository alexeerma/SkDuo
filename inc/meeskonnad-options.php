<?php
/**
 * Teams (meeskonnad) page hero image option.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function (): void {
	add_submenu_page(
		'skduo-page-options',
		__( 'Meeskonnad hero pilt', 'skduo' ),
		__( 'Meeskonnad hero pilt', 'skduo' ),
		'manage_options',
		'skduo-meeskonnad-options',
		'skduo_render_meeskonnad_options_page'
	);
} );

add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
	if ( str_contains( $hook, 'skduo-meeskonnad-options' ) ) {
		wp_enqueue_media();
	}
} );

function skduo_get_meeskonnad_hero_image(): string {
	$image_id = absint( get_option( 'skduo_meeskonnad_hero_image_id', 0 ) );
	return $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '';
}

function skduo_render_meeskonnad_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if (
		isset( $_POST['skduo_meeskonnad_options_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_meeskonnad_options_nonce'] ) ), 'skduo_save_meeskonnad_options' )
	) {
		$image_id = isset( $_POST['skduo_meeskonnad_hero_image_id'] ) ? absint( $_POST['skduo_meeskonnad_hero_image_id'] ) : 0;
		update_option( 'skduo_meeskonnad_hero_image_id', $image_id, false );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Meeskonnad options saved.', 'skduo' ) . '</p></div>';
	}

	$image_id = absint( get_option( 'skduo_meeskonnad_hero_image_id', 0 ) );

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Meeskonnad hero pilt', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Meeskonna grupipilt, mis paistab /meeskonnad lehe hero rohelise paneeli taustal läbi, sarnaselt avalehe hero piltidele.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_meeskonnad_options', 'skduo_meeskonnad_options_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Meeskonna pilt', 'skduo' ); ?></th>
						<td><?php skduo_render_meeskonnad_hero_image_picker( $image_id ); ?></td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( __( 'Salvesta', 'skduo' ) ); ?>
		</form>
	</div>
	<?php
	skduo_print_meeskonnad_hero_image_picker_script();
}

function skduo_render_meeskonnad_hero_image_picker( int $image_id ): void {
	$thumb = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';

	echo '<input type="hidden" id="skduo_meeskonnad_hero_image_id" name="skduo_meeskonnad_hero_image_id" value="' . esc_attr( (string) $image_id ) . '" />';
	echo '<div id="skduo-meeskonnad-hero-image-preview" style="margin-bottom:10px;">';
	if ( $thumb ) {
		echo '<img src="' . esc_url( $thumb ) . '" style="width:220px;height:140px;object-fit:cover;border:1px solid #ccd0d4;" alt="" />';
	}
	echo '</div>';
	echo '<button type="button" class="button" id="skduo-select-meeskonnad-hero-image">' . esc_html__( 'Vali pilt', 'skduo' ) . '</button> ';
	echo '<button type="button" class="button" id="skduo-clear-meeskonnad-hero-image">' . esc_html__( 'Tühjenda', 'skduo' ) . '</button>';
	echo '<p class="description">' . esc_html__( 'Soovitatavalt lai grupipilt (nt 1600×1200 või laiem). Kuvatakse tumerohelise paneeli taustal, õrnalt läbipaistvana.', 'skduo' ) . '</p>';
}

function skduo_print_meeskonnad_hero_image_picker_script(): void {
	?>
	<script>
	(function($) {
		const input = $('#skduo_meeskonnad_hero_image_id');
		const preview = $('#skduo-meeskonnad-hero-image-preview');

		$('#skduo-select-meeskonnad-hero-image').on('click', function(e) {
			e.preventDefault();

			const frame = wp.media({
				title: '<?php echo esc_js( __( 'Vali meeskonna pilt', 'skduo' ) ); ?>',
				button: { text: '<?php echo esc_js( __( 'Kasuta pilti', 'skduo' ) ); ?>' },
				multiple: false,
				library: { type: 'image' }
			});

			frame.on('select', function() {
				const attachment = frame.state().get('selection').first().toJSON();
				input.val(attachment.id);
				const src = attachment.sizes?.medium?.url || attachment.url;
				preview.html($('<img>', {
					src,
					alt: '',
					css: { width: '220px', height: '140px', objectFit: 'cover', border: '1px solid #ccd0d4' }
				}));
			});

			frame.open();
		});

		$('#skduo-clear-meeskonnad-hero-image').on('click', function(e) {
			e.preventDefault();
			input.val('');
			preview.empty();
		});
	})(jQuery);
	</script>
	<?php
}
