<?php
/**
 * Editable page copy options for archive and landing pages.
 */

defined( 'ABSPATH' ) || exit;

function skduo_page_copy_defaults(): array {
	return [
		'toetajad' => [
			'menu_title' => __( 'Toetajad', 'skduo' ),
			'page_title' => __( 'Toetajad page options', 'skduo' ),
			'option' => 'skduo_toetajad_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'SK DUO · Partnerid' ],
				'hero_title' => [ 'Hero pealkiri', 'textarea', "Partnerid, kes\nusuvad Tartu\nvõrkpalli." ],
				'hero_emphasis_line' => [ 'Rõhutatud hero rida', 'number', '2' ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'SK DUO toetajad teevad võimalikuks esindusmeeskonna mängud, noortearengu ja kogukonnaspordiga seotud tegevused.' ],
				'partner_count_label' => [ 'Partnerite loenduri silt', 'text', 'koostööpartnerit' ],
				'name_tier_label' => [ 'Nimisponsorite silt', 'text', 'Nimisponsorid' ],
				'sponsors_tier_label' => [ 'Sponsorite silt', 'text', 'Sponsorid' ],
				'name_section_label' => [ 'Nimisponsorite ploki pealkiri', 'text', 'NIMISPONSORID' ],
				'sponsors_eyebrow' => [ 'Sponsorite väike pealkiri', 'text', 'Toetajad' ],
				'sponsors_title' => [ 'Sponsorite pealkiri', 'text', 'SPONSORID' ],
				'partners_tier_label' => [ 'Partnerite silt', 'text', 'Partnerid' ],
				'partners_eyebrow' => [ 'Partnerite väike pealkiri', 'text', 'Partnerid' ],
				'partners_title' => [ 'Partnerite pealkiri', 'text', 'PARTNERID' ],
				'empty_eyebrow' => [ 'Tühja vaate väike pealkiri', 'text', 'Sisu puudub' ],
				'empty_title' => [ 'Tühja vaate pealkiri', 'text', 'Lisa esimene toetaja wp-administ.' ],
				'empty_text' => [ 'Tühja vaate tekst', 'textarea', 'Loo kirje menüüst Toetajad. Vali tase (nimisponsor, sponsor või partner), lisa logo piltide sektsioonis ja täida URL.' ],
			],
		],
		'tule_toetajaks' => [
			'menu_title' => __( 'Tule toetajaks', 'skduo' ),
			'page_title' => __( 'Tule toetajaks page options', 'skduo' ),
			'option' => 'skduo_tule_toetajaks_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'Saa partneriks' ],
				'hero_title' => [ 'Hero pealkiri', 'textarea', "Toeta Tartu\nvõrkpalli." ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'Pakume nähtavust, koostöövõimalusi ja otsest seost kogukonnaspordi arenguga. Kirjuta meile - leiame sobiva koostöövormi.' ],
				'cta_primary_label' => [ 'Hero peamine nupp', 'text', 'Kirjuta meile' ],
				'cta_primary_url' => [ 'Hero peamise nupu link', 'url', 'mailto:info@skduo.ee' ],
				'cta_secondary_label' => [ 'Hero teine nupp', 'text', 'Vaata toetajaid' ],
				'cta_secondary_url' => [ 'Hero teise nupu link', 'url', '/toetajad' ],
				'timeline_eyebrow' => [ 'Ajajoone väike pealkiri', 'text', 'Tule toetajaks' ],
				'timeline_title' => [ 'Ajajoone pealkiri', 'text', 'Kuidas koostöö algab' ],
				'timeline_intro' => [ 'Ajajoone sissejuhatus', 'textarea', 'Toetajaks tulemine on lihtne: lepime kokku sobiva koostöövormi, paneme nähtavuse paika ja hoiame partnerluse hooaja jooksul nähtavana.' ],
				'timeline_items' => [ 'Ajajoone sammud', 'textarea', "1 | Võta ühendust\nKirjuta meile ja räägi, millist koostööd või nähtavust otsid.\n\n2 | Leiame sobiva paketi\nPaneme koos paika toetuse mahu, nähtavuse ja hooaja tegevused.\n\n3 | Alustame koostööd\nLisame partneri materjalid lehele ning toome koostöö nähtavale mängudel ja klubi kanalites." ],
			],
		],
		'hooajapilet' => [
			'menu_title' => __( 'Hooajapilet', 'skduo' ),
			'page_title' => __( 'Hooajapilet page options', 'skduo' ),
			'option' => 'skduo_hooajapilet_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'Bigbank Tartu' ],
				'hero_title' => [ 'Hero pealkiri', 'textarea', "Hooajapilet\n2025 / 26" ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'Hooajapilet annab mugava sissepääsu Bigbank Tartu kodumängudele ning aitab olla meeskonnale lähedal terve hooaja vältel.' ],
				'cta_label' => [ 'Ostunupu tekst', 'text', 'Osta hooajapilet' ],
				'cta_url' => [ 'Ostunupu link', 'url', home_url( '/kalender' ) ],
				'info_title' => [ 'Infoploki pealkiri', 'text', 'Üks pilet. Terve hooaeg.' ],
				'info_text' => [ 'Infoploki tekst', 'textarea', 'Vali hooajapilet, kinnita ost partneri keskkonnas ning tule kodumängudele kohale ilma iga mängu jaoks eraldi piletit otsimata.' ],
				'note_text' => [ 'Märkuse tekst', 'textarea', 'Ostmine toimub piletipartneri keskkonnas. Vajutades ostunupule suunatakse sind edasi hooajapileti ostulehele.' ],
			],
		],
		'kkk' => [
			'menu_title' => __( 'KKK', 'skduo' ),
			'page_title' => __( 'KKK page options', 'skduo' ),
			'option' => 'skduo_kkk_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'Korduma kippuvad küsimused' ],
				'hero_title' => [ 'Hero pealkiri', 'textarea', "KKK\nküsimused ja vastused" ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'Leia kiirelt vastused klubi, treeningute, mängude ja piletitega seotud küsimustele.' ],
				'intro_title' => [ 'Sisu pealkiri', 'text', 'Kuidas saame aidata?' ],
				'intro_text' => [ 'Sisu tekst', 'textarea', 'Kui sa ei leia siit sobivat vastust, kirjuta meile ja aitame hea meelega edasi.' ],
				'faq_items' => [ 'KKK küsimused ja vastused', 'textarea', "Kuidas saan klubiga ühendust?\nKirjuta meile aadressil info@skduo.ee või kasuta lehel toodud kontaktandmeid.\n\nKust saab osta pileteid?\nPiletite ostmiseks kasuta lehel olevat piletinuppu või ava mängukalender.\n\nKuidas liituda noortetreeningutega?\nAva Noored leht, tutvu treeninggruppidega ning saada liitumissoov läbi seal oleva vormi." ],
			],
		],
		'klubist' => [
			'menu_title' => __( 'Klubist', 'skduo' ),
			'page_title' => __( 'Klubist page options', 'skduo' ),
			'option' => 'skduo_klubist_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'SK DUO' ],
				'hero_title' => [ 'Hero pealkiri', 'textarea', "Klubi lugu\nja saavutused" ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'SK DUO on Tartu võrkpalli kodu - klubi, kus kasvavad mängijad, sünnivad võidud ja kogukond tuleb saali kokku.' ],
				'timeline_eyebrow' => [ 'Ajajoone väike pealkiri', 'text', 'Meie teekond' ],
				'timeline_title' => [ 'Ajajoone pealkiri', 'text', 'Mida oleme teinud ja võitnud' ],
				'timeline_intro' => [ 'Ajajoone sissejuhatus', 'textarea', 'Lisa siia klubi olulisemad hetked, tiitlid, saavutused ja verstapostid läbi aastate.' ],
				'timeline_items' => [ 'Ajajoone sündmused', 'textarea', "1999 | Klubi algus\nSK DUO alustas tegevust Tartus ja pani aluse järjepidevale võrkpallitööle.\n\n2012 | Noortetöö kasv\nKlubi treeninggrupid laienesid ning noored said rohkem võimalusi võistlemiseks.\n\n2025 | Uus hooaeg\nBigbank Tartu jätkab kodumängude, kogukonna ja noorte võrkpalli arendamist." ],
			],
		],
		'epood' => [
			'menu_title' => __( 'E-pood', 'skduo' ),
			'page_title' => __( 'E-pood page options', 'skduo' ),
			'option' => 'skduo_epood_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'SK DUO merch' ],
				'hero_emphasis' => [ 'Hero rõhutatud rida', 'text', 'otse partnerilt.' ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'SK DUO valitud fännitooted ja klubi merch. Tooted liiguvad ostmiseks edasi partneri e-poodi.' ],
				'catalog_eyebrow' => [ 'Kataloogi väike pealkiri', 'text', 'Kataloog' ],
				'catalog_text' => [ 'Kataloogi tekst', 'textarea', 'toodet on lisatud WooCommerce’i. Ostunupp suunab välisesse e-poodi.' ],
				'external_badge' => [ 'Välise poe silt', 'text', 'Partneri e-pood' ],
				'external_button_fallback' => [ 'Välise toote nupu vaiketekst', 'text', 'Osta partneri e-poest' ],
				'internal_button_fallback' => [ 'Tavalise toote nupu vaiketekst', 'text', 'Vaata toodet' ],
				'empty_title' => [ 'Tühja vaate pealkiri', 'text', 'Tooteid ei ole veel lisatud.' ],
				'empty_text' => [ 'Tühja vaate tekst', 'textarea', 'Lisa merch WooCommerce’i alt tootena ning vali vajadusel tüübiks External/Affiliate product.' ],
			],
		],
		'kontakt' => [
			'menu_title' => __( 'Kontakt', 'skduo' ),
			'page_title' => __( 'Kontakt page options', 'skduo' ),
			'option' => 'skduo_kontakt_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'Kontakt' ],
				'hero_title' => [ 'Hero pealkiri', 'textarea', "Räägime\nära." ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'Küsimusi treeningute, liikmemaksude, koostöö või millegi muu kohta? Kirjuta või helista - vastame kahe tööpäeva jooksul.' ],
				'contact_eyebrow' => [ 'Kontaktide ploki väike pealkiri', 'text', 'Kontaktid' ],
				'form_eyebrow' => [ 'Vormi ploki väike pealkiri', 'text', 'Saada sõnum' ],
				'cf7_shortcode' => [ 'Contact Form 7 shortcode', 'text', '' ],
			],
		],
		'uudised' => [
			'menu_title' => __( 'Uudised', 'skduo' ),
			'page_title' => __( 'Uudised page options', 'skduo' ),
			'option' => 'skduo_uudised_options',
			'fields' => [
				'hero_eyebrow' => [ 'Hero väike pealkiri', 'text', 'Uudised' ],
				'hero_meta_label' => [ 'Hero meta silt', 'text', 'Klubi uudised' ],
				'post_count_label' => [ 'Postituste loenduri silt', 'text', 'postitust' ],
				'hero_text' => [ 'Hero tekst', 'textarea', 'Värsked lood, mängude kokkuvõtted, klubielu ja teated SK DUO tegemistest.' ],
			],
		],
	];
}

/**
 * Optional per-entry images for the Klubist timeline. Stored separately from the plain-text
 * timeline_items field since images need the media library, not a textarea. Slot N (0-indexed)
 * pairs with timeline entry N in the same order as "Ajajoone sündmused".
 */
function skduo_klubist_timeline_image_slots(): int {
	return 10;
}

function skduo_get_klubist_timeline_image_ids(): array {
	$ids = explode( ',', (string) get_option( 'skduo_klubist_timeline_images', '' ) );
	$ids = array_map( 'absint', $ids );
	$ids = array_pad( $ids, skduo_klubist_timeline_image_slots(), 0 );
	return array_slice( $ids, 0, skduo_klubist_timeline_image_slots() );
}

function skduo_get_klubist_timeline_images(): array {
	return array_map(
		function ( int $image_id ): string {
			return $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '';
		},
		skduo_get_klubist_timeline_image_ids()
	);
}

function skduo_get_page_copy_options( string $page ): array {
	$config = skduo_page_copy_defaults()[ $page ] ?? null;
	if ( ! $config ) {
		return [];
	}

	$defaults = [];
	foreach ( $config['fields'] as $key => $field ) {
		$defaults[ $key ] = $field[2];
	}

	$saved = get_option( $config['option'], [] );
	$saved = is_array( $saved ) ? $saved : [];

	return array_merge( $defaults, array_intersect_key( $saved, $defaults ) );
}

add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
	if ( str_contains( $hook, 'skduo-klubist-options' ) ) {
		wp_enqueue_media();
	}
} );

add_action( 'admin_menu', function (): void {
	foreach ( skduo_page_copy_defaults() as $page => $config ) {
		add_submenu_page(
			'skduo-page-options',
				$config['page_title'],
				$config['menu_title'],
				'manage_options',
				'skduo-' . $page . '-options',
				function () use ( $page ): void {
					skduo_render_page_copy_options_page( $page );
				}
			);
	}
} );

function skduo_render_page_copy_options_page( string $page ): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$all = skduo_page_copy_defaults();
	if ( ! isset( $all[ $page ] ) ) {
		return;
	}

	$config = $all[ $page ];
	$nonce_action = 'skduo_save_' . $page . '_options';
	$nonce_name = 'skduo_' . $page . '_options_nonce';

	if (
		isset( $_POST[ $nonce_name ] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $nonce_action )
	) {
		$options = [];
		$posted = isset( $_POST['skduo_page_options'] ) && is_array( $_POST['skduo_page_options'] )
			? wp_unslash( $_POST['skduo_page_options'] )
			: [];

		foreach ( $config['fields'] as $key => [ $label, $type, $default ] ) {
			$value = isset( $posted[ $key ] ) ? (string) $posted[ $key ] : '';
			$options[ $key ] = 'url' === $type ? esc_url_raw( $value ) : sanitize_textarea_field( $value );
		}

		update_option( $config['option'], $options, false );

		if ( 'klubist' === $page ) {
			$image_ids = isset( $_POST['skduo_klubist_timeline_images'] ) && is_array( $_POST['skduo_klubist_timeline_images'] )
				? array_map( 'absint', wp_unslash( $_POST['skduo_klubist_timeline_images'] ) )
				: [];
			$image_ids = array_pad( $image_ids, skduo_klubist_timeline_image_slots(), 0 );
			$image_ids = array_slice( $image_ids, 0, skduo_klubist_timeline_image_slots() );
			update_option( 'skduo_klubist_timeline_images', implode( ',', $image_ids ), false );
		}

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Page options saved.', 'skduo' ) . '</p></div>';
	}

	$options = skduo_get_page_copy_options( $page );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( $config['page_title'] ); ?></h1>
		<p><?php echo esc_html__( 'Adjust hero, CTA, labels, and helper copy for this page.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( $nonce_action, $nonce_name ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php foreach ( $config['fields'] as $key => [ $label, $type ] ) : ?>
						<tr>
							<th scope="row"><label for="skduo_page_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<?php if ( 'textarea' === $type ) : ?>
									<textarea id="skduo_page_<?php echo esc_attr( $key ); ?>" name="skduo_page_options[<?php echo esc_attr( $key ); ?>]" rows="<?php echo esc_attr( in_array( $key, [ 'faq_items', 'timeline_items' ], true ) ? 18 : 5 ); ?>" class="large-text"><?php echo esc_textarea( (string) $options[ $key ] ); ?></textarea>
									<?php if ( 'faq_items' === $key ) : ?>
										<p class="description">
											<?php echo esc_html__( 'Lisa nii palju KKK ridu kui vaja. Iga ploki esimene rida on küsimus, järgnevad read on vastus. Eralda küsimused tühja reaga.', 'skduo' ); ?>
										</p>
									<?php elseif ( 'timeline_items' === $key ) : ?>
										<p class="description">
											<?php echo esc_html__( 'Lisa nii palju sündmuseid kui vaja. Iga ploki esimene rida on "Aasta | pealkiri", järgnevad read on kirjeldus. Eralda sündmused tühja reaga.', 'skduo' ); ?>
										</p>
									<?php endif; ?>
								<?php else : ?>
									<input type="<?php echo esc_attr( $type ); ?>" id="skduo_page_<?php echo esc_attr( $key ); ?>" name="skduo_page_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( (string) $options[ $key ] ); ?>" class="regular-text" />
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( 'klubist' === $page ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Ajajoone pildid', 'skduo' ); ?></th>
							<td>
								<?php skduo_render_klubist_timeline_image_pickers( skduo_get_klubist_timeline_image_ids() ); ?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<?php submit_button( __( 'Save page options', 'skduo' ) ); ?>
		</form>
	</div>
	<?php
	if ( 'klubist' === $page ) {
		skduo_print_klubist_timeline_image_picker_script();
	}
}

function skduo_render_klubist_timeline_image_pickers( array $image_ids ): void {
	echo '<p class="description" style="margin-top:0;">' . esc_html__( 'Valikuline pilt iga sündmuse jaoks. Rida 1 vastab esimesele sündmusele ülalpool "Ajajoone sündmused" väljal, rida 2 teisele, jne.', 'skduo' ) . '</p>';
	echo '<div style="display:grid; gap:12px; max-width:640px;">';

	foreach ( $image_ids as $index => $image_id ) {
		$slot = $index + 1;
		$thumb = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';

		echo '<div class="skduo-klubist-timeline-image-row" style="display:grid; grid-template-columns:2rem 96px auto; gap:12px; align-items:center;">';
		echo '<strong>' . (int) $slot . '.</strong>';
		echo '<div class="skduo-klubist-timeline-image-preview" style="width:96px;height:72px;display:flex;align-items:center;justify-content:center;background:#f0f0f1;overflow:hidden;">';
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" style="width:100%;height:100%;object-fit:cover;" alt="" />';
		}
		echo '</div>';
		echo '<div>';
		printf(
			'<input type="hidden" class="skduo-klubist-timeline-image-id" name="skduo_klubist_timeline_images[]" value="%d" />',
			(int) $image_id
		);
		echo '<button type="button" class="button skduo-select-klubist-timeline-image">' . esc_html__( 'Vali pilt', 'skduo' ) . '</button> ';
		echo '<button type="button" class="button skduo-clear-klubist-timeline-image">' . esc_html__( 'Eemalda', 'skduo' ) . '</button>';
		echo '</div>';
		echo '</div>';
	}

	echo '</div>';
}

function skduo_print_klubist_timeline_image_picker_script(): void {
	?>
	<script>
	(function ($) {
		document.querySelectorAll('.skduo-klubist-timeline-image-row').forEach(function (row) {
			var input = row.querySelector('.skduo-klubist-timeline-image-id');
			var preview = row.querySelector('.skduo-klubist-timeline-image-preview');
			var selectButton = row.querySelector('.skduo-select-klubist-timeline-image');
			var clearButton = row.querySelector('.skduo-clear-klubist-timeline-image');

			selectButton.addEventListener('click', function (e) {
				e.preventDefault();

				var frame = wp.media({
					title: '<?php echo esc_js( __( 'Vali pilt', 'skduo' ) ); ?>',
					button: { text: '<?php echo esc_js( __( 'Kasuta pilti', 'skduo' ) ); ?>' },
					multiple: false,
					library: { type: 'image' }
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					input.value = attachment.id;
					var src = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
					preview.innerHTML = '<img src="' + src + '" style="width:100%;height:100%;object-fit:cover;" alt="">';
				});

				frame.open();
			});

			clearButton.addEventListener('click', function (e) {
				e.preventDefault();
				input.value = '0';
				preview.innerHTML = '';
			});
		});
	})(jQuery);
	</script>
	<?php
}
