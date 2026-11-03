<?php
/**
 * Arhiiv (gallery) settings page: one or more seasons, each with its own
 * image gallery, selectable via a season picker on the front end.
 */

defined( 'ABSPATH' ) || exit;

// Priority 20: the "skduo-page-options" parent menu is registered in
// inc/home-hero.php at the default priority. Since inc/*.php loads via
// glob() in alphabetical order, "arhiiv.php" runs before "home-hero.php" —
// registering this submenu at the default priority would fire before its
// parent exists, which makes WordPress compute the wrong page hookname and
// the resulting admin link 404s. Running later guarantees the parent is
// already registered.
add_action( 'admin_menu', function (): void {
	add_submenu_page(
		'skduo-page-options',
		__( 'Arhiivi galerii', 'skduo' ),
		__( 'Arhiivi galerii', 'skduo' ),
		'manage_options',
		'skduo-arhiiv-gallery',
		'skduo_render_arhiiv_gallery_options_page'
	);
}, 20 );

add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
	if ( str_contains( $hook, 'skduo-arhiiv-gallery' ) ) {
		wp_enqueue_media();
	}
} );

/**
 * Raw, unsanitized-for-display season rows: [ ['label' => string, 'image_ids' => int[]], ... ].
 * Falls back to the old single season/gallery options so nothing already
 * saved gets lost when this moved from one gallery to many.
 */
function skduo_get_arhiiv_seasons_raw(): array {
	$seasons = get_option( 'skduo_arhiiv_seasons', null );

	if ( is_array( $seasons ) ) {
		return array_values( array_map(
			function ( $season ): array {
				return [
					'label'     => (string) ( $season['label'] ?? '' ),
					'image_ids' => array_filter( array_map( 'absint', (array) ( $season['image_ids'] ?? [] ) ) ),
				];
			},
			$seasons
		) );
	}

	$legacy_label = (string) get_option( 'skduo_arhiiv_season', '' );
	$legacy_ids   = array_filter( array_map( 'absint', explode( ',', (string) get_option( 'skduo_arhiiv_gallery_ids', '' ) ) ) );

	if ( '' === $legacy_label && empty( $legacy_ids ) ) {
		return [];
	}

	return [
		[
			'label'     => $legacy_label,
			'image_ids' => $legacy_ids,
		],
	];
}

function skduo_render_arhiiv_gallery_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if (
		isset( $_POST['skduo_arhiiv_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_arhiiv_nonce'] ) ), 'skduo_save_arhiiv' )
	) {
		$posted  = isset( $_POST['skduo_arhiiv_seasons'] ) && is_array( $_POST['skduo_arhiiv_seasons'] )
			? wp_unslash( $_POST['skduo_arhiiv_seasons'] )
			: [];
		$seasons = [];

		foreach ( $posted as $row ) {
			$label = isset( $row['label'] ) ? sanitize_text_field( (string) $row['label'] ) : '';
			$ids   = isset( $row['image_ids'] )
				? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( (string) $row['image_ids'] ) ) ) )
				: [];

			if ( '' === $label && empty( $ids ) ) {
				continue;
			}

			$seasons[] = [
				'label'     => $label,
				'image_ids' => array_values( $ids ),
			];
		}

		update_option( 'skduo_arhiiv_seasons', $seasons, false );
		// Superseded by skduo_arhiiv_seasons; delete so skduo_get_arhiiv_seasons_raw() doesn't fall back to them.
		delete_option( 'skduo_arhiiv_season' );
		delete_option( 'skduo_arhiiv_gallery_ids' );

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Arhiivi galerii salvestatud.', 'skduo' ) . '</p></div>';
	}

	$seasons = skduo_get_arhiiv_seasons_raw();
	if ( empty( $seasons ) ) {
		$seasons = [ [ 'label' => '', 'image_ids' => [] ] ];
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Arhiivi galerii', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Lisa üks rida hooaja kohta. Külastaja saab "Arhiiv" lehemalli kasutaval lehel hooaja valida ja vastava galerii üles leida.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_arhiiv', 'skduo_arhiiv_nonce' ); ?>

			<div id="skduo-arhiiv-seasons">
				<?php foreach ( $seasons as $index => $season ) : ?>
					<?php skduo_render_arhiiv_season_row( $index, $season ); ?>
				<?php endforeach; ?>
			</div>

			<p>
				<button type="button" class="button" id="skduo-add-arhiiv-season"><?php echo esc_html__( 'Lisa hooaeg', 'skduo' ); ?></button>
			</p>

			<?php submit_button( __( 'Salvesta arhiivi galerii', 'skduo' ) ); ?>
		</form>

		<template id="skduo-arhiiv-season-template">
			<?php skduo_render_arhiiv_season_row( '__INDEX__', [ 'label' => '', 'image_ids' => [] ] ); ?>
		</template>
	</div>
	<?php

	skduo_print_arhiiv_gallery_picker_script();
}

/**
 * @param int|string $index Numeric row index, or the literal placeholder
 *                          "__INDEX__" when rendering the <template> used
 *                          to stamp out new rows client-side.
 */
function skduo_render_arhiiv_season_row( $index, array $season ): void {
	$label     = (string) ( $season['label'] ?? '' );
	$image_ids = array_filter( array_map( 'absint', (array) ( $season['image_ids'] ?? [] ) ) );

	echo '<div class="skduo-arhiiv-season-row" data-arhiiv-season-row style="border:1px solid #ccd0d4;background:#fff;padding:16px;margin-bottom:16px;max-width:760px;">';

	echo '<p><label>' . esc_html__( 'Hooaeg', 'skduo' ) . '<br />';
	printf(
		'<input type="text" name="skduo_arhiiv_seasons[%1$s][label]" value="%2$s" placeholder="%3$s" class="regular-text" /></label></p>',
		esc_attr( (string) $index ),
		esc_attr( $label ),
		esc_attr__( 'Hooaeg 2024 / 25', 'skduo' )
	);

	echo '<p>' . esc_html__( 'Galerii pildid', 'skduo' ) . '</p>';
	printf(
		'<input type="hidden" class="skduo-arhiiv-image-ids" name="skduo_arhiiv_seasons[%1$s][image_ids]" value="%2$s" />',
		esc_attr( (string) $index ),
		esc_attr( implode( ',', $image_ids ) )
	);
	echo '<div class="skduo-arhiiv-preview" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px;">';
	foreach ( $image_ids as $image_id ) {
		$thumb = wp_get_attachment_image_url( $image_id, 'thumbnail' );
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" style="width:72px;height:72px;object-fit:cover;border:1px solid #ccd0d4;" alt="" />';
		}
	}
	echo '</div>';
	echo '<button type="button" class="button skduo-select-arhiiv-images">' . esc_html__( 'Vali pildid', 'skduo' ) . '</button> ';
	echo '<button type="button" class="button skduo-clear-arhiiv-images">' . esc_html__( 'Tühjenda pildid', 'skduo' ) . '</button> ';
	echo '<button type="button" class="button skduo-remove-arhiiv-season" style="color:#b32d2e;">' . esc_html__( 'Eemalda hooaeg', 'skduo' ) . '</button>';

	echo '</div>';
}

function skduo_print_arhiiv_gallery_picker_script(): void {
	?>
	<script>
	(function($) {
		const container = document.getElementById('skduo-arhiiv-seasons');
		const template = document.getElementById('skduo-arhiiv-season-template');
		const addButton = document.getElementById('skduo-add-arhiiv-season');
		let nextIndex = container ? container.querySelectorAll('[data-arhiiv-season-row]').length : 0;

		function bindRow(row) {
			const input = row.querySelector('.skduo-arhiiv-image-ids');
			const preview = row.querySelector('.skduo-arhiiv-preview');
			const selectBtn = row.querySelector('.skduo-select-arhiiv-images');
			const clearBtn = row.querySelector('.skduo-clear-arhiiv-images');
			const removeBtn = row.querySelector('.skduo-remove-arhiiv-season');

			selectBtn.addEventListener('click', function (e) {
				e.preventDefault();

				const frame = wp.media({
					title: '<?php echo esc_js( __( 'Vali galerii pildid', 'skduo' ) ); ?>',
					button: { text: '<?php echo esc_js( __( 'Kasuta pilte', 'skduo' ) ); ?>' },
					multiple: true,
					library: { type: 'image' }
				});

				frame.on('select', function () {
					const attachments = frame.state().get('selection').toJSON();
					const ids = attachments.map((attachment) => attachment.id);
					input.value = ids.join(',');
					preview.innerHTML = '';

					attachments.forEach((attachment) => {
						const src = (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) || attachment.url;
						const img = document.createElement('img');
						img.src = src;
						img.alt = '';
						img.style.cssText = 'width:72px;height:72px;object-fit:cover;border:1px solid #ccd0d4;';
						preview.appendChild(img);
					});
				});

				frame.open();
			});

			clearBtn.addEventListener('click', function (e) {
				e.preventDefault();
				input.value = '';
				preview.innerHTML = '';
			});

			removeBtn.addEventListener('click', function (e) {
				e.preventDefault();
				if (container.querySelectorAll('[data-arhiiv-season-row]').length <= 1) {
					input.value = '';
					preview.innerHTML = '';
					row.querySelector('input[type="text"]').value = '';
					return;
				}
				row.remove();
			});
		}

		if (container) {
			container.querySelectorAll('[data-arhiiv-season-row]').forEach(bindRow);
		}

		if (addButton && template && container) {
			addButton.addEventListener('click', function () {
				const html = template.innerHTML.replace(/__INDEX__/g, String(nextIndex));
				nextIndex += 1;

				const wrapper = document.createElement('div');
				wrapper.innerHTML = html.trim();
				const row = wrapper.firstElementChild;
				container.appendChild(row);
				bindRow(row);
			});
		}
	})(jQuery);
	</script>
	<?php
}
