<?php
/**
 * Homepage hero editor fields.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function (): void {
	add_menu_page(
		__( 'Page options', 'skduo' ),
		__( 'Page options', 'skduo' ),
		'manage_options',
		'skduo-page-options',
		'skduo_render_page_options_page',
		'dashicons-admin-page',
		58
	);

	add_submenu_page(
		'skduo-page-options',
		__( 'Home page options', 'skduo' ),
		__( 'Home page options', 'skduo' ),
		'manage_options',
		'skduo-home-options',
		'skduo_render_home_options_page'
	);

	add_submenu_page(
		'skduo-page-options',
		__( 'Header ticker', 'skduo' ),
		__( 'Header ticker', 'skduo' ),
		'manage_options',
		'skduo-header-ticker',
		'skduo_render_header_ticker_options_page'
	);

	add_submenu_page(
		'skduo-page-options',
		__( 'Hero section images', 'skduo' ),
		__( 'Hero section images', 'skduo' ),
		'manage_options',
		'skduo-hero-images',
		'skduo_render_hero_images_options_page'
	);

	add_submenu_page(
		'skduo-page-options',
		__( 'Contact bar', 'skduo' ),
		__( 'Contact bar', 'skduo' ),
		'manage_options',
		'skduo-contact-bar',
		'skduo_render_contact_bar_options_page'
	);

	add_submenu_page(
		'skduo-page-options',
		__( 'Footer logo', 'skduo' ),
		__( 'Footer logo', 'skduo' ),
		'manage_options',
		'skduo-footer-logo',
		'skduo_render_footer_logo_options_page'
	);
} );

add_action( 'add_meta_boxes_page', function ( WP_Post $post ): void {
	// Only relevant on the actual front page — skduo_get_home_hero_data() gives the
	// global Theme options ("Home page options") priority everywhere else, so showing
	// this override box on every page is just noise.
	if ( (int) $post->ID !== (int) get_option( 'page_on_front' ) ) {
		return;
	}

	add_meta_box(
		'skduo_home_hero',
		__( 'Avalehe hero', 'skduo' ),
		'skduo_render_home_hero_meta_box',
		'page',
		'normal',
		'high'
	);
} );

add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
	if ( str_contains( $hook, 'skduo-hero-images' ) || str_contains( $hook, 'skduo-footer-logo' ) ) {
		wp_enqueue_media();
		return;
	}

	if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( $screen && 'page' === $screen->post_type ) {
		wp_enqueue_media();
	}
} );

add_action( 'admin_init', function (): void {
	register_setting( 'skduo_header_ticker_settings', 'skduo_header_ticker_items', [
		'type'              => 'array',
		'sanitize_callback' => 'skduo_sanitize_header_ticker_setting',
		'default'           => [],
	] );
} );

function skduo_sanitize_header_ticker_setting( $value ): array {
	$raw_items = [];

	if ( is_string( $value ) ) {
		$decoded = base64_decode( $value, true );
		$data    = false !== $decoded ? json_decode( $decoded, true ) : null;

		if ( is_array( $data ) ) {
			$raw_items = $data;
		} else {
			$raw_items = preg_split( '/\R+/', $value ) ?: [];
		}
	} elseif ( is_array( $value ) ) {
		$raw_items = $value;
	}

	$items = array_values( array_filter( array_map(
		function ( $item ): array {
			return skduo_normalize_header_ticker_item( $item );
		},
		$raw_items
	), function ( array $item ): bool {
		return '' !== $item['text'];
	} ) );
	$items = array_slice( $items, 0, 20 );

	return $items;
}

function skduo_render_page_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Page options', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Manage reusable visual settings for the site pages.', 'skduo' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=skduo-hero-images' ) ); ?>">
				<?php echo esc_html__( 'Edit hero section images', 'skduo' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=skduo-home-options' ) ); ?>">
				<?php echo esc_html__( 'Edit home page options', 'skduo' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=skduo-header-ticker' ) ); ?>">
				<?php echo esc_html__( 'Edit header ticker', 'skduo' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=skduo-contact-bar' ) ); ?>">
				<?php echo esc_html__( 'Edit contact bar', 'skduo' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=skduo-meeskonnad-options' ) ); ?>">
				<?php echo esc_html__( 'Edit meeskonnad hero image', 'skduo' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=skduo-arhiiv-gallery' ) ); ?>">
				<?php echo esc_html__( 'Edit arhiivi galerii', 'skduo' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=skduo-footer-logo' ) ); ?>">
				<?php echo esc_html__( 'Edit footer logo', 'skduo' ); ?>
			</a>
		</p>
	</div>
	<?php
}

function skduo_home_hero_fields(): array {
	return [
		'location'        => [ 'Asukoht', 'text', 'Tartu, Estonia' ],
		'season'          => [ 'Hooaeg / staatus', 'text', 'Hooaeg 2025 / 26' ],
		'established'     => [ 'Asutatud', 'text', 'Est. 1999' ],
		'title_line_1'    => [ 'Pealkiri rida 1', 'text', '25 aastat' ],
		'title_line_2'    => [ 'Pealkiri rida 2', 'text', 'Tartu' ],
		'title_emphasis'  => [ 'Rõhutatud sõna', 'text', 'võrkpalli' ],
		'subtitle'        => [ 'Alatekst', 'textarea', 'Üks Eesti suurimaid võrkpalliklubisid. Bigbank Tartu, esindusvõistkonnad ja sadu noori sportlasi ühe Tartu energia all.' ],
		'primary_label'   => [ 'Peamine nupp', 'text', 'Osta pilet' ],
		'primary_url'     => [ 'Peamise nupu link', 'url', '/kalender' ],
		'secondary_label' => [ 'Teine nupp', 'text', 'Tule trenni' ],
		'secondary_url'   => [ 'Teise nupu link', 'url', '/noored' ],
	];
}

function skduo_render_home_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$fields = skduo_home_hero_fields();

	if (
		isset( $_POST['skduo_home_options_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_home_options_nonce'] ) ), 'skduo_save_home_options' )
	) {
		$options = [];
		$posted  = isset( $_POST['skduo_home_hero'] ) && is_array( $_POST['skduo_home_hero'] )
			? wp_unslash( $_POST['skduo_home_hero'] )
			: [];

		foreach ( $fields as $key => [ $label, $type ] ) {
			$value = isset( $posted[ $key ] ) ? (string) $posted[ $key ] : '';
			$options[ $key ] = str_ends_with( $key, '_url' )
				? esc_url_raw( $value )
				: sanitize_textarea_field( $value );
		}

		update_option( 'skduo_home_hero_text', $options, false );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Home page options saved.', 'skduo' ) . '</p></div>';
	}

	$options = get_option( 'skduo_home_hero_text', [] );
	$options = is_array( $options ) ? $options : [];

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Home page options', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Adjust the homepage hero text, buttons, and small labels from one place.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_home_options', 'skduo_home_options_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php skduo_render_home_hero_text_fields( $fields, $options, 'skduo_home_hero' ); ?>
				</tbody>
			</table>

			<?php submit_button( __( 'Save home page options', 'skduo' ) ); ?>
		</form>
	</div>
	<?php
}

function skduo_render_header_ticker_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$items = get_option( 'skduo_header_ticker_items', [] );
	if ( is_string( $items ) ) {
		$items = preg_split( '/\R+/', $items ) ?: [];
	}
	$items = is_array( $items ) ? $items : [];
	if ( empty( $items ) ) {
		$items = skduo_default_header_ticker_items();
	}
	$items = array_map(
		function ( $item ): array {
			return skduo_normalize_header_ticker_item( $item );
		},
		$items
	);

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Header ticker', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Add one ticker item per line. The first header row loops these items from right to left.', 'skduo' ); ?></p>
		<?php settings_errors(); ?>

		<form id="skduo-header-ticker-form" method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'skduo_header_ticker_settings' ); ?>
			<input type="hidden" id="skduo_header_ticker_items" name="skduo_header_ticker_items" value="">
			<table class="widefat striped" role="presentation" style="max-width: 1100px;">
				<thead>
					<tr>
						<th style="width: 45%;"><?php echo esc_html__( 'Text', 'skduo' ); ?></th>
						<th style="width: 45%;"><?php echo esc_html__( 'Link', 'skduo' ); ?></th>
						<th style="width: 10%;"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $items as $item ) : ?>
						<tr class="skduo-ticker-row">
							<td>
								<input type="text" class="large-text skduo-ticker-text" value="<?php echo esc_attr( $item['text'] ); ?>">
							</td>
							<td>
								<input type="text" class="large-text skduo-ticker-url" value="<?php echo esc_attr( $item['url'] ); ?>" placeholder="https://example.com">
							</td>
							<td>
								<button type="button" class="button skduo-remove-ticker-row"><?php echo esc_html__( 'Remove', 'skduo' ); ?></button>
							</td>
						</tr>
					<?php endforeach; ?>
					<tr class="skduo-ticker-row">
						<td>
							<input type="text" class="large-text skduo-ticker-text" value="">
						</td>
						<td>
							<input type="text" class="large-text skduo-ticker-url" value="" placeholder="https://example.com">
						</td>
						<td>
							<button type="button" class="button skduo-remove-ticker-row"><?php echo esc_html__( 'Remove', 'skduo' ); ?></button>
						</td>
					</tr>
				</tbody>
			</table>
			<p>
				<button type="button" class="button" id="skduo-add-ticker-row"><?php echo esc_html__( 'Add ticker row', 'skduo' ); ?></button>
			</p>
			<p class="description"><?php echo esc_html__( 'Leave the link empty for plain text. Links can be full URLs, mailto links, or telephone links.', 'skduo' ); ?></p>

			<?php submit_button( __( 'Save header ticker', 'skduo' ) ); ?>
		</form>
		<script>
			(function () {
				var form = document.getElementById('skduo-header-ticker-form');
				var payload = document.getElementById('skduo_header_ticker_items');
				var addButton = document.getElementById('skduo-add-ticker-row');
				var tbody = form ? form.querySelector('tbody') : null;

				function encodeUtf8Base64(value) {
					return window.btoa(unescape(encodeURIComponent(value)));
				}

				function createRow() {
					var row = document.createElement('tr');
					row.className = 'skduo-ticker-row';
					row.innerHTML =
						'<td><input type="text" class="large-text skduo-ticker-text" value=""></td>' +
						'<td><input type="text" class="large-text skduo-ticker-url" value="" placeholder="https://example.com"></td>' +
						'<td><button type="button" class="button skduo-remove-ticker-row"><?php echo esc_js( __( 'Remove', 'skduo' ) ); ?></button></td>';
					return row;
				}

				if (! form || ! payload || ! tbody) {
					return;
				}

				if (addButton) {
					addButton.addEventListener('click', function () {
						tbody.appendChild(createRow());
					});
				}

				tbody.addEventListener('click', function (event) {
					if (! event.target.classList.contains('skduo-remove-ticker-row')) {
						return;
					}

					var rows = tbody.querySelectorAll('.skduo-ticker-row');
					var row = event.target.closest('.skduo-ticker-row');
					if (rows.length > 1 && row) {
						row.remove();
					}
				});

				form.addEventListener('submit', function () {
					var items = [];
					tbody.querySelectorAll('.skduo-ticker-row').forEach(function (row) {
						var text = row.querySelector('.skduo-ticker-text').value.trim();
						var url = row.querySelector('.skduo-ticker-url').value.trim();
						if (text) {
							items.push({ text: text, url: url });
						}
					});
					payload.value = encodeUtf8Base64(JSON.stringify(items));
				});
			})();
		</script>
	</div>
	<?php
}

function skduo_render_hero_images_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if (
		isset( $_POST['skduo_hero_images_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_hero_images_nonce'] ) ), 'skduo_save_hero_images' )
	) {
		$image_ids = isset( $_POST['skduo_hero_image_ids'] )
			? implode( ',', array_slice( array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['skduo_hero_image_ids'] ) ) ) ) ), 0, 6 ) )
			: '';

		update_option( 'skduo_hero_image_ids', $image_ids, false );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Hero section images saved.', 'skduo' ) . '</p></div>';
	}

	$image_ids = array_filter( array_map( 'absint', explode( ',', (string) get_option( 'skduo_hero_image_ids', '' ) ) ) );

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Hero section images', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'These images control the hover-reveal image mosaic in the homepage hero section.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_hero_images', 'skduo_hero_images_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Hero pildid', 'skduo' ); ?></th>
						<td>
							<?php skduo_render_hero_image_picker( $image_ids ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<?php submit_button( __( 'Save hero images', 'skduo' ) ); ?>
		</form>
	</div>
	<?php

	skduo_print_hero_image_picker_script();
}

function skduo_render_contact_bar_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if (
		isset( $_POST['skduo_contact_bar_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_contact_bar_nonce'] ) ), 'skduo_save_contact_bar' )
	) {
		$posted = isset( $_POST['skduo_contact_bar'] ) && is_array( $_POST['skduo_contact_bar'] )
			? wp_unslash( $_POST['skduo_contact_bar'] )
			: [];

		update_option( 'skduo_contact_bar', [
			'phone'        => sanitize_text_field( (string) ( $posted['phone'] ?? '' ) ),
			'email'        => sanitize_email( (string) ( $posted['email'] ?? '' ) ),
			'live_enabled' => ! empty( $posted['live_enabled'] ),
			'live_text'    => sanitize_text_field( (string) ( $posted['live_text'] ?? '' ) ),
			'live_url'     => esc_url_raw( (string) ( $posted['live_url'] ?? '' ) ),
		], false );

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Contact bar saved.', 'skduo' ) . '</p></div>';
	}

	$data = skduo_get_contact_bar_data();

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Contact bar', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Phone, email, and the live-match badge shown under the header ticker on every page.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_contact_bar', 'skduo_contact_bar_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th><label for="skduo_contact_bar_phone"><?php echo esc_html__( 'Telefon', 'skduo' ); ?></label></th>
						<td><input type="text" id="skduo_contact_bar_phone" name="skduo_contact_bar[phone]" value="<?php echo esc_attr( $data->phone ); ?>" class="regular-text" placeholder="+372 53 400 379" /></td>
					</tr>
					<tr>
						<th><label for="skduo_contact_bar_email"><?php echo esc_html__( 'E-post', 'skduo' ); ?></label></th>
						<td><input type="email" id="skduo_contact_bar_email" name="skduo_contact_bar[email]" value="<?php echo esc_attr( $data->email ); ?>" class="regular-text" placeholder="info@skduo.ee" /></td>
					</tr>
					<tr>
						<th><?php echo esc_html__( 'Otseülekande märge', 'skduo' ); ?></th>
						<td>
							<label><input type="checkbox" name="skduo_contact_bar[live_enabled]" value="1" <?php checked( $data->live_enabled, true ); ?> /> <?php echo esc_html__( 'Näita otseülekande märget kontaktribal', 'skduo' ); ?></label>
							<p class="description"><?php echo esc_html__( 'Lülita sisse ainult siis, kui mäng on tegelikult otseülekandes / lähiajal.', 'skduo' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="skduo_contact_bar_live_text"><?php echo esc_html__( 'Otseülekande tekst', 'skduo' ); ?></label></th>
						<td><input type="text" id="skduo_contact_bar_live_text" name="skduo_contact_bar[live_text]" value="<?php echo esc_attr( $data->live_text ); ?>" class="large-text" placeholder="Otseülekanne · L 22.03 19:00" /></td>
					</tr>
					<tr>
						<th><label for="skduo_contact_bar_live_url"><?php echo esc_html__( 'Otseülekande link', 'skduo' ); ?></label></th>
						<td>
							<input type="url" id="skduo_contact_bar_live_url" name="skduo_contact_bar[live_url]" value="<?php echo esc_attr( $data->live_url ); ?>" class="large-text" placeholder="https://youtube.com/..." />
							<p class="description"><?php echo esc_html__( 'Valikuline. Kui täidetud, muutub märge klikitavaks lingiks (nt YouTube otseülekanne).', 'skduo' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<?php submit_button( __( 'Save contact bar', 'skduo' ) ); ?>
		</form>
	</div>
	<?php
}

function skduo_render_footer_logo_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if (
		isset( $_POST['skduo_footer_logo_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_footer_logo_nonce'] ) ), 'skduo_save_footer_logo' )
	) {
		$logo_id = isset( $_POST['skduo_footer_logo_id'] ) ? absint( wp_unslash( $_POST['skduo_footer_logo_id'] ) ) : 0;

		update_option( 'skduo_footer_logo_id', $logo_id, false );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Footer logo saved.', 'skduo' ) . '</p></div>';
	}

	$logo_id = (int) get_option( 'skduo_footer_logo_id', 0 );

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Footer logo', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Choose the logo shown in the site footer. Leave empty to use the Site Identity logo.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_footer_logo', 'skduo_footer_logo_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Jaluse logo', 'skduo' ); ?></th>
						<td>
							<?php skduo_render_footer_logo_picker( $logo_id ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<?php submit_button( __( 'Save footer logo', 'skduo' ) ); ?>
		</form>
	</div>
	<?php

	skduo_print_footer_logo_picker_script();
}

function skduo_render_footer_logo_picker( int $logo_id ): void {
	echo '<input type="hidden" id="skduo_footer_logo_id" name="skduo_footer_logo_id" value="' . esc_attr( (string) $logo_id ) . '" />';
	echo '<div id="skduo-footer-logo-preview" style="margin-bottom:10px;">';
	if ( $logo_id ) {
		$thumb = wp_get_attachment_image_url( $logo_id, 'medium' );
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" style="max-width:220px;max-height:120px;object-fit:contain;border:1px solid #ccd0d4;background:#fff;padding:8px;" alt="" />';
		}
	}
	echo '</div>';
	echo '<button type="button" class="button" id="skduo-select-footer-logo">' . esc_html__( 'Vali logo', 'skduo' ) . '</button> ';
	echo '<button type="button" class="button" id="skduo-clear-footer-logo">' . esc_html__( 'Tühjenda', 'skduo' ) . '</button>';
	echo '<p class="description">' . esc_html__( 'Kui logo puudub, kasutatakse üldist Site Identity logo (Kohanda > Saidi identiteet).', 'skduo' ) . '</p>';
}

function skduo_print_footer_logo_picker_script(): void {
	?>
	<script>
	(function($) {
		const input = $('#skduo_footer_logo_id');
		const preview = $('#skduo-footer-logo-preview');

		$('#skduo-select-footer-logo').on('click', function(e) {
			e.preventDefault();

			const frame = wp.media({
				title: '<?php echo esc_js( __( 'Vali jaluse logo', 'skduo' ) ); ?>',
				button: { text: '<?php echo esc_js( __( 'Kasuta logo', 'skduo' ) ); ?>' },
				multiple: false,
				library: { type: 'image' }
			});

			frame.on('select', function() {
				const attachment = frame.state().get('selection').first().toJSON();
				input.val(attachment.id);
				preview.empty();

				const src = attachment.sizes?.medium?.url || attachment.url;
				preview.append($('<img>', {
					src,
					alt: '',
					css: {
						maxWidth: '220px',
						maxHeight: '120px',
						objectFit: 'contain',
						border: '1px solid #ccd0d4',
						background: '#fff',
						padding: '8px'
					}
				}));
			});

			frame.open();
		});

		$('#skduo-clear-footer-logo').on('click', function(e) {
			e.preventDefault();
			input.val('');
			preview.empty();
		});
	})(jQuery);
	</script>
	<?php
}

function skduo_render_home_hero_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'skduo_home_hero_meta', 'skduo_home_hero_nonce' );

	$fields = skduo_home_hero_fields();
	$values = [];
	foreach ( $fields as $key => $field ) {
		$values[ $key ] = get_post_meta( $post->ID, '_' . $key, true );
	}

	echo '<table class="form-table"><tbody>';
	$show_ticker = '0' !== (string) get_post_meta( $post->ID, '_show_header_ticker', true );
	echo '<tr><th>' . esc_html__( 'Header ticker', 'skduo' ) . '</th><td>';
	echo '<label><input type="checkbox" name="skduo_show_header_ticker" value="1" ' . checked( $show_ticker, true, false ) . ' /> ';
	echo esc_html__( 'Show the looping top bar on this page', 'skduo' ) . '</label>';
	echo '<p class="description">' . esc_html__( 'Untick to hide the first header strip with the scrolling text on this page.', 'skduo' ) . '</p>';
	echo '</td></tr>';
	skduo_render_home_hero_text_fields( $fields, $values, 'skduo' );
	echo '<tr><th>' . esc_html__( 'Hero pildid', 'skduo' ) . '</th><td>';
	$image_ids = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post->ID, '_hero_image_ids', true ) ) ) );
	skduo_render_hero_image_picker( $image_ids );
	echo '</td></tr>';
	echo '</tbody></table>';

	skduo_print_hero_image_picker_script();
}

function skduo_render_home_hero_text_fields( array $fields, array $values, string $name_prefix ): void {
	foreach ( $fields as $key => [ $label, $type, $placeholder ] ) {
		$value = isset( $values[ $key ] ) ? (string) $values[ $key ] : '';
		$id    = str_replace( [ '[', ']' ], '_', $name_prefix ) . '_' . $key;
		$name  = 'skduo' === $name_prefix ? 'skduo_' . $key : $name_prefix . '[' . $key . ']';

		echo '<tr><th><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';

		if ( 'textarea' === $type ) {
			printf(
				'<textarea id="%1$s" name="%2$s" rows="4" class="large-text" placeholder="%4$s">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( (string) $value ),
				esc_attr( $placeholder )
			);
		} else {
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s" class="regular-text" />',
				esc_attr( $type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value ),
				esc_attr( $placeholder )
			);
		}

		echo '</td></tr>';
	}
}

function skduo_render_hero_image_picker( array $image_ids ): void {
	$image_ids = array_slice( $image_ids, 0, 6 );

	echo '<input type="hidden" id="skduo_hero_image_ids" name="skduo_hero_image_ids" value="' . esc_attr( implode( ',', $image_ids ) ) . '" />';
	echo '<div id="skduo-hero-image-preview" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; max-width:720px;">';
	foreach ( $image_ids as $image_id ) {
		$thumb = wp_get_attachment_image_url( $image_id, 'thumbnail' );
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" style="width:72px;height:72px;object-fit:cover;border:1px solid #ccd0d4;" alt="" />';
		}
	}
	echo '</div>';
	echo '<button type="button" class="button" id="skduo-select-hero-images">' . esc_html__( 'Vali hero pildid', 'skduo' ) . '</button> ';
	echo '<button type="button" class="button" id="skduo-clear-hero-images">' . esc_html__( 'Tühjenda', 'skduo' ) . '</button>';
	echo '<p class="description">' . esc_html__( 'Vali kuni 6 mängupilti. Kui pildid puuduvad, kasutatakse theme fallback pilte.', 'skduo' ) . '</p>';
}

function skduo_print_hero_image_picker_script(): void {
	?>
	<script>
	(function($) {
		const input = $('#skduo_hero_image_ids');
		const preview = $('#skduo-hero-image-preview');

		$('#skduo-select-hero-images').on('click', function(e) {
			e.preventDefault();

			const frame = wp.media({
				title: '<?php echo esc_js( __( 'Vali hero pildid', 'skduo' ) ); ?>',
				button: { text: '<?php echo esc_js( __( 'Kasuta pilte', 'skduo' ) ); ?>' },
				multiple: true,
				library: { type: 'image' }
			});

			frame.on('select', function() {
				const attachments = frame.state().get('selection').toJSON();
				const ids = attachments.slice(0, 6).map((attachment) => attachment.id);
				input.val(ids.join(','));
				preview.empty();

				attachments.slice(0, 6).forEach((attachment) => {
					const src = attachment.sizes?.thumbnail?.url || attachment.url;
					preview.append($('<img>', {
						src,
						alt: '',
						css: {
							width: '72px',
							height: '72px',
							objectFit: 'cover',
							border: '1px solid #ccd0d4'
						}
					}));
				});
			});

			frame.open();
		});

		$('#skduo-clear-hero-images').on('click', function(e) {
			e.preventDefault();
			input.val('');
			preview.empty();
		});
	})(jQuery);
	</script>
	<?php
}

add_action( 'save_post_page', function ( int $post_id ): void {
	if ( ! isset( $_POST['skduo_home_hero_nonce'] ) || ! wp_verify_nonce( $_POST['skduo_home_hero_nonce'], 'skduo_home_hero_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$keys = [
		'location',
		'season',
		'established',
		'title_line_1',
		'title_line_2',
		'title_emphasis',
		'subtitle',
		'primary_label',
		'primary_url',
		'secondary_label',
		'secondary_url',
	];

	foreach ( $keys as $key ) {
		if ( ! isset( $_POST[ 'skduo_' . $key ] ) ) {
			continue;
		}

		$value = str_ends_with( $key, '_url' )
			? esc_url_raw( $_POST[ 'skduo_' . $key ] )
			: sanitize_textarea_field( $_POST[ 'skduo_' . $key ] );

		update_post_meta( $post_id, '_' . $key, $value );
	}

	update_post_meta( $post_id, '_show_header_ticker', isset( $_POST['skduo_show_header_ticker'] ) ? '1' : '0' );

	$image_ids = isset( $_POST['skduo_hero_image_ids'] )
		? implode( ',', array_slice( array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['skduo_hero_image_ids'] ) ) ) ) ), 0, 6 ) )
		: '';

	update_post_meta( $post_id, '_hero_image_ids', $image_ids );
} );
