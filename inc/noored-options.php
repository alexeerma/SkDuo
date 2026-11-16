<?php
/**
 * Youth page options.
 */

defined( 'ABSPATH' ) || exit;

function skduo_noored_default_options(): array {
	return [
		'hero_eyebrow'       => 'Noored',
		'hero_title'         => "Tule trenni.\nLeia oma võrkpall.",
		'hero_subtitle'      => 'Spordiklubi DUO kutsub poisse ja tüdrukuid võrkpalli mängima. Treeningutega saab liituda aastaringselt ning uutel liitujatel on kolm treeningut tutvumiseks tasuta.',
		'primary_label'      => 'Tule trenni',
		'secondary_label'    => 'Lisainfo',
		'nav_labels'         => "Treeninggrupid|treeninggrupid\nTreenerid|treenerid\nHinnakiri|hinnakiri",
		'intro_title'        => 'Spordiklubi DUO kutsub poisse ja tüdrukuid võrkpalli mängima.',
		'intro_text'         => "Kui armastad sportida ja tunned liikumisest rõõmu, oled oodatud meiega liituma. Treeningud toimuvad septembrist maini Tartu Ülikooli spordihoones ning juunist augustini rannavolle väljakutel.\n\nVõta meiega ühendust, et leiaksime just sulle sobiva treeninggrupi. Avalduse kättesaamisel võtame ühendust ja lepime kokku tutvumistreeningu.\n\nUutel liitujatel on kolm treeningut tutvumiseks tasuta.\n\nIgale trennis käijale antakse klubi särk, mis tagab tasuta sissepääsu Bigbank Tartu esindusmeeskonna kodumängudele Tartu Ülikooli spordihallis.",
		'contact_name'       => 'Jaanika Jakobson',
		'contact_phone'      => '58055888',
		'contact_email'      => 'info@skduo.ee',
		'form_recipient_email' => 'info@skduo.ee',
		'form_popup_title'   => 'Liitumisavaldus',
		'form_iframe_embed'  => '',
		'form_title'         => 'Saada avaldus ja võtame ühendust kahe tööpäeva jooksul.',
		'form_note'          => 'Vormi esitamise järel võtame teiega ühendust kahe tööpäeva jooksul.',
		'form_success_text'  => 'Aitäh! Avaldus on saadetud ja võtame teiega ühendust.',
		'form_error_text'    => 'Vormi saatmine ebaõnnestus. Palun kontrollige välju või kirjutage otse e-postile.',
		'free_badge_title'   => '3 trenni',
		'free_badge_label'   => 'tasuta',
		'free_badge_text'    => 'Tule proovima, kohtume treeneriga ja leiame sobiva grupi.',
		'groups_eyebrow'     => 'Treeninggrupid 2025/2026',
		'groups_title'       => 'Treenerite kaupa.',
		'coaches_eyebrow'    => 'Treenerid',
		'coaches_title'      => 'Inimesed, kes aitavad mängu armastama hakata.',
		'pricing_eyebrow'    => 'Hinnakiri',
		'pricing_title'      => 'Hinnakiri 2025/26 (10 kuud)',
		'pricing_headings'   => 'Võrkpall|3-5x nädalas|Suvi (juuli-august)|2016-2020 grupp',
		'pricing_rows'       => "Kuutasu|75|65|55\nAastamaks korraga tasudes|675|65|495\nTeise lapse kuutasu|37,5|32,5|27,5\nTeise lapse aastamaks korraga tasudes|375|32,5|275",
		'billing_title'      => 'Arveldus',
		'billing_text'       => "Alates 1. septembrist 2025 on aastamaks 750 eurot (10 kuud), mida saab tasuda osadena 75 eurot kuus. Aastamaksu korraga tasudes on summa 675 eurot.\n\nPeriood: september 2025 - juuni 2026. Suvekuude treeningutest osavõtjatel on tasu 65 EUR kuus.",
		'payment_title'      => 'Makseinfo',
		'payment_text'       => "Treeningtasu maksmine toimub arve alusel. Maksekorraldusel tuleb ära märkida õpilase nimi ja kuu, mille eest tasutakse.\n\nMajanduslikult vähekindlustatud peredel, kolme või enama lapse treenimisel või heade sportlike saavutustega lastel on võimalik taotleda õppemaksu vähendamist kuni 100%.",
		'membership_eyebrow' => 'Liikmelisus',
		'membership_title'   => 'Klubi liikmelisuse kinnitamine (Sportlyzer) ja lahkumine klubist',
		'membership_sections' => "Klubi liikmelisuse kinnitamine (Sportlyzer) ja lahkumine klubist\n- Liitudes SK DUO noortetreeningutega ja täites liitumisavalduse (Tule trenni) saad klubi liikmeks; liikmelisusega kaasneb liikmemaksu tasumine ja kodukorra kinnitamine.\n- Kui laps jätkab pärast proovitreeninguid (3 tasuta treeningut), registreerib lapsevanem end ja lapse Sportlyzeris ning märgib ära treeninggrupi.\n- Sportlyzeri kaudu tulevad liikmemaksu arved ja olulised teated; korrektsed kontaktandmed võimaldavad treeneril vajadusel ühendust võtta.\n- Tulumaksusoodustuse saamiseks tuleb Sportlyzeris märkida nimi ja isikukood ning tasuda arveid saadud viitenumbriga.\n- Lahkumisel veendu, et arved on tasutud, teavita treenerit ja saada põhjendusega e-kiri info@skduo.ee.\n- Sportlyzeri mobiilirakendusega saab vanem alati vaadata treeninggraafikut, märkida trenni tulemist ja jälgida kohalkäimist.\n\nTreeningud\n- Treeningud toimuvad vastavalt vanusele ja tasemele 2-5 korda nädalas.\n- Pikemast puudumisest või haigestumisest tuleb teavitada treenerit; soodustust saab taotleda haiguse/trauma korral, mis kestab üle kaheksa nädala.\n- Puhkusegraafikud (pühad, laagrid) edastatakse e-postile, treener teavitab ka kohapeal.\n\nVõistlused\n- SK DUO liikmelisus eeldab tublit treeningutest osavõttu ja klubi esindamist EVF noortevõistlustel vastavalt vanusele.\n- Võistkonnad mängivad Bigbank Tartu nime all klubi võistlusvormis, mida saab klubist soetada.\n- Klubi katab EVF noortevõistluste esindusvõistkonna võistluskulud, va majutus ja toitlustus; teise võistkonna saatmine kokkuleppel klubiga.\n- Majutuskulude eest esitab klubi lapsevanemale eraldi arve; ööbimine sugulaste/sõbra juures toimub kokkuleppel treeneriga.\n- EVF võistlustele sõidu igale esindusvõistkonnale organiseerib ja tasub klubi.\n- Tavaturniiridel osalemine toimub treeneri ettepanekul ja lapsevanema panusega.\n\nNoormängijate kohustused\n- Treeningutel ja võistlustel ei kasutata ebaviisakat kõnepruuki ning kuulatakse treeneri juhendamist.\n- Mängija hoiab klubi mainet ega satu konflikti klubikaaslaste ega vastasmängijatega.\n- Väljakutel hoitakse puhtust ja korda ning järgitakse sisekorra eeskirju.\n- Noormängijatel on võimalus olla pallipoisiks/-tüdrukuks Bigbank Tartu ja Tartu Ülikool/Bigbank naiskonna mängudel.\n\nLapsevanema kohustused\n- Lapsevanem tagab lapsele vajaliku varustuse: puhtad saalijalanõud ja lühike dress.\n- Võistlustel osalemiseks on vajalik SK Duo esindusvorm, mida saab tellida veebilehelt skduo.ee/noored/spordivarustus.\n- Majutuse ja toitlustuse eest võistlustel tasub lapsevanem eraldi arve alusel; majutust ei pea tasuma, kui sportlane võistleb vanema vanuseklassiga.\n- Lapsevanem usaldab treeningute ja võistluste otsused treenerile, ei sekku treeneri töösse ega kritiseeri mängijaid ega kohtunikke; kiitused ja ergutused on lubatud.\n- Probleemide korral tuleb koheselt teavitada treenerit ja/või klubi.\n\nKlubi kohustused\n- Klubi tagab sobilikud treeningtingimused ja kvalifitseeritud treenerid, kes arvestavad laste vanuse ja tasemega ning tagavad turvalisuse.\n- Klubi tagab treeningvarustuse vahendite olemasolu ning organiseerib treeninggruppide osalemise turniiridel ja laagrites.\n- Klubi katab EVF turniiride transpordikulud: minivõrkpalli festival, noorte MV, noorte karikavõistlused, EVF karikavõistlused, U18 superkarikavõistlused (4 parimat).\n- Igale trennis käijale antakse klubi särk, mis tagab tasuta sissepääsu Bigbank Tartu kodumängudele TÜ spordihallis.\n- Klubi hoiab kodulehel ajakohast kontaktinfot ja edastab olulised muudatused lapsevanemale e-maili või SMS-iga.",
		'membership_footer' => "Küsimuste korral treenerite, treeningaegade, kohtade või klubimaksude kohta helista või kirjuta otse SK DUO klubile.\nMTÜ Spordiklubi DUO | Ringtee 25, 50105 Tartu | info@skduo.ee | +372 53 400 379 (Hendrik)",
	];
}

function skduo_noored_option_fields(): array {
	return [
		'hero_eyebrow'     => [ 'Hero väike pealkiri', 'text' ],
		'hero_title'       => [ 'Hero pealkiri', 'textarea' ],
		'hero_subtitle'    => [ 'Hero tekst', 'textarea' ],
		'primary_label'    => [ 'Peamine nupp', 'text' ],
		'secondary_label'  => [ 'Teine nupp', 'text' ],
		'nav_labels'       => [ 'Parempoolsed nupud', 'textarea' ],
		'intro_title'      => [ 'Sissejuhatuse pealkiri', 'text' ],
		'intro_text'       => [ 'Sissejuhatuse tekst', 'textarea' ],
		'contact_name'     => [ 'Kontaktisik', 'text' ],
		'contact_phone'    => [ 'Kontakttelefon', 'text' ],
		'contact_email'    => [ 'Kontakt e-post', 'email' ],
		'form_recipient_email' => [ 'Avalduse saaja e-post', 'email' ],
		'form_popup_title' => [ 'Popup vormi pealkiri', 'text' ],
		'form_iframe_embed' => [ 'Popup iframe / vormi embed kood', 'embed' ],
		'form_title'       => [ 'Avalduse pealkiri', 'text' ],
		'form_note'        => [ 'Avalduse märkus', 'textarea' ],
		'form_success_text' => [ 'Avalduse õnnestumise teade', 'textarea' ],
		'form_error_text'  => [ 'Avalduse veateade', 'textarea' ],
		'free_badge_title' => [ 'Tasuta treeningute number', 'text' ],
		'free_badge_label' => [ 'Tasuta treeningute silt', 'text' ],
		'free_badge_text'  => [ 'Tasuta treeningute tekst', 'textarea' ],
		'groups_eyebrow'   => [ 'Treeninggruppide väike pealkiri', 'text' ],
		'groups_title'     => [ 'Treeninggruppide pealkiri', 'text' ],
		'coaches_eyebrow'  => [ 'Treenerite väike pealkiri', 'text' ],
		'coaches_title'    => [ 'Treenerite pealkiri', 'text' ],
		'pricing_eyebrow'  => [ 'Hinnakirja väike pealkiri', 'text' ],
		'pricing_title'    => [ 'Hinnakirja pealkiri', 'text' ],
		'pricing_headings' => [ 'Hinnakirja tabeli pealkirjad', 'text' ],
		'pricing_rows'     => [ 'Hinnakirja read', 'textarea' ],
		'billing_title'    => [ 'Arvelduse pealkiri', 'text' ],
		'billing_text'     => [ 'Arvelduse tekst', 'textarea' ],
		'payment_title'    => [ 'Makseinfo pealkiri', 'text' ],
		'payment_text'     => [ 'Makseinfo tekst', 'textarea' ],
		'membership_eyebrow' => [ 'Liikmelisuse väike pealkiri', 'text' ],
		'membership_title'   => [ 'Liikmelisuse pealkiri', 'text' ],
		'membership_sections' => [ 'Liikmelisuse plokid', 'textarea' ],
		'membership_footer' => [ 'Liikmelisuse allkiri (kontaktid)', 'textarea' ],
	];
}

function skduo_noored_sidebar_image_slots(): int {
	return 8;
}

function skduo_get_noored_sidebar_image_ids(): array {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) get_option( 'skduo_noored_sidebar_images', '' ) ) ) );
	return array_slice( array_values( $ids ), 0, skduo_noored_sidebar_image_slots() );
}

function skduo_get_noored_sidebar_images(): array {
	$images = [];

	foreach ( skduo_get_noored_sidebar_image_ids() as $image_id ) {
		$url = wp_get_attachment_image_url( $image_id, 'large' );
		if ( $url ) {
			$images[] = $url;
		}
	}

	return $images;
}

function skduo_get_noored_options(): array {
	$defaults = skduo_noored_default_options();
	$saved    = get_option( 'skduo_noored_options', [] );
	$saved    = is_array( $saved ) ? $saved : [];

	return array_merge( $defaults, array_intersect_key( $saved, $defaults ) );
}

function skduo_parse_noored_nav_items( string $value ): array {
	return array_values( array_filter( array_map(
		function ( string $line ): ?array {
			$parts = array_map( 'trim', explode( '|', $line ) );
			if ( count( $parts ) < 2 || '' === $parts[0] || '' === $parts[1] ) {
				return null;
			}
			return [ 'label' => $parts[0], 'target' => sanitize_title( $parts[1] ) ];
		},
		preg_split( '/\R+/', $value ) ?: []
	) ) );
}

function skduo_parse_noored_pricing_rows( string $value ): array {
	return array_values( array_filter( array_map(
		function ( string $line ): ?array {
			$parts = array_map( 'trim', explode( '|', $line ) );
			if ( count( $parts ) < 4 || '' === $parts[0] ) {
				return null;
			}
			return [ 'label' => $parts[0], 'main' => $parts[1], 'summer' => $parts[2], 'young' => $parts[3] ];
		},
		preg_split( '/\R+/', $value ) ?: []
	) ) );
}

function skduo_parse_noored_rules_sections( string $value ): array {
	$blocks = preg_split( "/\R{2,}/", trim( $value ) ) ?: [];
	$sections = [];

	foreach ( $blocks as $block ) {
		$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R+/', $block ) ?: [] ) ) );
		if ( empty( $lines ) ) {
			continue;
		}

		$title = array_shift( $lines );
		$items = array_map(
			function ( string $line ): string {
				return trim( preg_replace( '/^[-*]\s*/', '', $line ) ?? $line );
			},
			$lines
		);

		$sections[] = [ 'title' => $title, 'items' => array_values( array_filter( $items ) ) ];
	}

	return $sections;
}

add_action( 'admin_menu', function (): void {
	add_submenu_page(
		'skduo-page-options',
		__( 'Noored', 'skduo' ),
		__( 'Noored', 'skduo' ),
		'manage_options',
		'skduo-noored-options',
		'skduo_render_noored_options_page'
	);
} );

add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
	if ( str_contains( $hook, 'skduo-noored-options' ) ) {
		wp_enqueue_media();
	}
} );

function skduo_render_noored_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$fields   = skduo_noored_option_fields();
	$defaults = skduo_noored_default_options();

	if (
		isset( $_POST['skduo_noored_options_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_noored_options_nonce'] ) ), 'skduo_save_noored_options' )
	) {
		$options = [];
		$posted  = isset( $_POST['skduo_noored_options'] ) && is_array( $_POST['skduo_noored_options'] )
			? wp_unslash( $_POST['skduo_noored_options'] )
			: [];

		foreach ( $fields as $key => [ $label, $type ] ) {
			$value = isset( $posted[ $key ] ) ? (string) $posted[ $key ] : '';
			if ( 'email' === $type ) {
				$options[ $key ] = sanitize_email( $value );
			} elseif ( 'embed' === $type ) {
				$options[ $key ] = skduo_sanitize_noored_form_embed( $value );
			} else {
				$options[ $key ] = sanitize_textarea_field( $value );
			}
		}

		update_option( 'skduo_noored_options', array_merge( $defaults, $options ), false );

		$sidebar_image_ids = isset( $_POST['skduo_noored_sidebar_images'] )
			? implode( ',', array_slice( array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['skduo_noored_sidebar_images'] ) ) ) ) ), 0, skduo_noored_sidebar_image_slots() ) )
			: '';
		update_option( 'skduo_noored_sidebar_images', $sidebar_image_ids, false );

		$default_coach_phone = isset( $_POST['skduo_noored_default_coach_phone'] )
			? skduo_sanitize_noored_phone( sanitize_text_field( wp_unslash( $_POST['skduo_noored_default_coach_phone'] ) ) )
			: '';
		$default_coach_email = isset( $_POST['skduo_noored_default_coach_email'] )
			? sanitize_email( wp_unslash( $_POST['skduo_noored_default_coach_email'] ) )
			: '';
		update_option( 'skduo_noored_default_coach_phone', $default_coach_phone, false );
		update_option( 'skduo_noored_default_coach_email', is_email( $default_coach_email ) ? $default_coach_email : '', false );

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Noored page options saved.', 'skduo' ) . '</p></div>';
	}

	$options = skduo_get_noored_options();
	$sidebar_image_ids = skduo_get_noored_sidebar_image_ids();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Noored', 'skduo' ); ?></h1>
		<p><?php echo esc_html__( 'Manage the /noored page copy, buttons, contact details, pricing table, and rules sections.', 'skduo' ); ?></p>
		<p><?php echo esc_html__( 'For button/pricing rows use the | character. Rules use a heading line followed by - bullet lines; separate sections with an empty line.', 'skduo' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'skduo_save_noored_options', 'skduo_noored_options_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php foreach ( $fields as $key => [ $label, $type ] ) : ?>
						<tr>
							<th scope="row"><label for="skduo_noored_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<?php if ( 'textarea' === $type || 'embed' === $type ) : ?>
									<textarea id="skduo_noored_<?php echo esc_attr( $key ); ?>" name="skduo_noored_options[<?php echo esc_attr( $key ); ?>]" rows="6" class="large-text code"><?php echo esc_textarea( (string) $options[ $key ] ); ?></textarea>
									<?php if ( 'embed' === $type ) : ?>
										<p class="description"><?php echo esc_html__( 'Paste an iframe embed code from your form provider. Only safe iframe attributes are saved.', 'skduo' ); ?></p>
									<?php endif; ?>
								<?php else : ?>
									<input type="<?php echo esc_attr( $type ); ?>" id="skduo_noored_<?php echo esc_attr( $key ); ?>" name="skduo_noored_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( (string) $options[ $key ] ); ?>" class="regular-text" />
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th scope="row"><label for="skduo_noored_default_coach_phone"><?php echo esc_html__( 'Vaikimisi treeneri telefon', 'skduo' ); ?></label></th>
						<td>
							<input type="text" id="skduo_noored_default_coach_phone" name="skduo_noored_default_coach_phone" value="<?php echo esc_attr( (string) get_option( 'skduo_noored_default_coach_phone', '' ) ); ?>" class="regular-text" inputmode="tel" />
							<p class="description"><?php echo esc_html__( 'Kasutatakse treeneri paneelis, kui treeneril pole telefoni.', 'skduo' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="skduo_noored_default_coach_email"><?php echo esc_html__( 'Vaikimisi treeneri e-post', 'skduo' ); ?></label></th>
						<td>
							<input type="email" id="skduo_noored_default_coach_email" name="skduo_noored_default_coach_email" value="<?php echo esc_attr( (string) get_option( 'skduo_noored_default_coach_email', '' ) ); ?>" class="regular-text" />
							<p class="description"><?php echo esc_html__( 'Kasutatakse treeneri paneelis, kui treeneril pole e-posti.', 'skduo' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Lisainfo külgriba pildid', 'skduo' ); ?></th>
						<td>
							<?php skduo_render_noored_sidebar_image_picker( $sidebar_image_ids ); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( __( 'Save Noored options', 'skduo' ) ); ?>
		</form>
	</div>
	<?php
	skduo_print_noored_sidebar_image_picker_script();
}

function skduo_render_noored_sidebar_image_picker( array $image_ids ): void {
	echo '<input type="hidden" id="skduo_noored_sidebar_images" name="skduo_noored_sidebar_images" value="' . esc_attr( implode( ',', $image_ids ) ) . '" />';
	echo '<div id="skduo-noored-sidebar-image-preview" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; max-width:720px;">';
	foreach ( $image_ids as $image_id ) {
		$thumb = wp_get_attachment_image_url( $image_id, 'thumbnail' );
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" style="width:72px;height:90px;object-fit:cover;border:1px solid #ccd0d4;" alt="" />';
		}
	}
	echo '</div>';
	echo '<button type="button" class="button" id="skduo-select-noored-sidebar-images">' . esc_html__( 'Vali pildid', 'skduo' ) . '</button> ';
	echo '<button type="button" class="button" id="skduo-clear-noored-sidebar-images">' . esc_html__( 'Tühjenda', 'skduo' ) . '</button>';
	echo '<p class="description">' . sprintf(
		/* translators: %d: max image count */
		esc_html__( 'Kuvatakse /noored lehe Lisainfo kasti all külgriba pildisliiderina. Kuni %d pilti.', 'skduo' ),
		(int) skduo_noored_sidebar_image_slots()
	) . '</p>';
}

function skduo_print_noored_sidebar_image_picker_script(): void {
	$max = skduo_noored_sidebar_image_slots();
	?>
	<script>
	(function($) {
		const input = $('#skduo_noored_sidebar_images');
		const preview = $('#skduo-noored-sidebar-image-preview');
		const max = <?php echo (int) $max; ?>;

		$('#skduo-select-noored-sidebar-images').on('click', function(e) {
			e.preventDefault();

			const frame = wp.media({
				title: '<?php echo esc_js( __( 'Vali külgriba pildid', 'skduo' ) ); ?>',
				button: { text: '<?php echo esc_js( __( 'Kasuta pilte', 'skduo' ) ); ?>' },
				multiple: true,
				library: { type: 'image' }
			});

			frame.on('select', function() {
				const attachments = frame.state().get('selection').toJSON();
				const ids = attachments.slice(0, max).map((attachment) => attachment.id);
				input.val(ids.join(','));
				preview.empty();

				attachments.slice(0, max).forEach((attachment) => {
					const src = attachment.sizes?.thumbnail?.url || attachment.url;
					preview.append($('<img>', {
						src,
						alt: '',
						css: {
							width: '72px',
							height: '90px',
							objectFit: 'cover',
							border: '1px solid #ccd0d4'
						}
					}));
				});
			});

			frame.open();
		});

		$('#skduo-clear-noored-sidebar-images').on('click', function(e) {
			e.preventDefault();
			input.val('');
			preview.empty();
		});
	})(jQuery);
	</script>
	<?php
}

/**
 * Phone input: keep digits, "+" and spaces only, collapse repeated spaces.
 */
function skduo_sanitize_noored_phone( string $value ): string {
	return trim( (string) preg_replace( '/ {2,}/', ' ', (string) preg_replace( '/[^\d+ ]/', '', $value ) ) );
}

/**
 * Coach panel contact: fill an empty phone/email (each separately) from the club-wide defaults
 * and build phone_href (tel:) and email_href (mailto:).
 */
function skduo_noored_coach_contact( array $contact ): array {
	$phone = trim( (string) ( $contact['phone'] ?? '' ) );
	$email = trim( (string) ( $contact['email'] ?? '' ) );

	if ( '' === $phone ) {
		$phone = skduo_sanitize_noored_phone( (string) get_option( 'skduo_noored_default_coach_phone', '' ) );
	}
	if ( '' === $email ) {
		$default_email = sanitize_email( (string) get_option( 'skduo_noored_default_coach_email', '' ) );
		$email         = is_email( $default_email ) ? $default_email : '';
	}

	$phone_digits = preg_replace( '/[^\d+]/', '', $phone );
	$phone_digits = ( '+' === substr( $phone_digits, 0, 1 ) ? '+' : '' ) . str_replace( '+', '', $phone_digits );

	$contact['phone']      = $phone;
	$contact['phone_href'] = '' !== str_replace( '+', '', $phone_digits ) ? 'tel:' . $phone_digits : '';
	$contact['email']      = $email;
	$contact['email_href'] = '' !== $email ? 'mailto:' . $email : '';
	$contact['extra']      = is_array( $contact['extra'] ?? null ) ? $contact['extra'] : [];

	return $contact;
}

function skduo_sanitize_noored_form_embed( string $value ): string {
	return wp_kses( $value, [
		'iframe' => [
			'src'             => true,
			'title'           => true,
			'width'           => true,
			'height'          => true,
			'style'           => true,
			'class'           => true,
			'id'              => true,
			'name'            => true,
			'loading'         => true,
			'allow'           => true,
			'allowfullscreen' => true,
			'referrerpolicy'  => true,
			'frameborder'     => true,
		],
	] );
}

add_action( 'admin_post_skduo_noored_application', 'skduo_handle_noored_application' );
add_action( 'admin_post_nopriv_skduo_noored_application', 'skduo_handle_noored_application' );

function skduo_handle_noored_application(): void {
	$redirect = wp_get_referer() ?: home_url( '/noored/' );
	$redirect = remove_query_arg( [ 'noored_form' ], $redirect );

	if (
		! isset( $_POST['skduo_noored_application_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['skduo_noored_application_nonce'] ) ), 'skduo_noored_application' )
	) {
		wp_safe_redirect( add_query_arg( 'noored_form', 'error', $redirect ) . '#liitumisavaldus' );
		exit;
	}

	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'noored_form', 'success', $redirect ) . '#liitumisavaldus' );
		exit;
	}

	$fields = [
		'child_name'  => __( 'Lapse nimi', 'skduo' ),
		'child_code'  => __( 'Lapse isikukood', 'skduo' ),
		'parent_name' => __( 'Lapsevanema nimi', 'skduo' ),
		'email'       => __( 'E-post', 'skduo' ),
		'phone'       => __( 'Kontakttelefon', 'skduo' ),
		'message'     => __( 'Info klubile', 'skduo' ),
	];

	$data = [];
	foreach ( $fields as $key => $label ) {
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$data[ $key ] = 'message' === $key ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	}
	$data['email'] = sanitize_email( $data['email'] );

	foreach ( [ 'child_name', 'child_code', 'parent_name', 'email', 'phone' ] as $required_key ) {
		if ( '' === $data[ $required_key ] ) {
			wp_safe_redirect( add_query_arg( 'noored_form', 'error', $redirect ) . '#liitumisavaldus' );
			exit;
		}
	}

	$options   = skduo_get_noored_options();
	$recipient = sanitize_email( $options['form_recipient_email'] ?? '' );
	if ( '' === $recipient ) {
		$recipient = sanitize_email( $options['contact_email'] ?? get_option( 'admin_email' ) );
	}

	$body_lines = [];
	foreach ( $fields as $key => $label ) {
		$body_lines[] = $label . ': ' . $data[ $key ];
	}

	$sent = wp_mail(
		$recipient,
		sprintf( __( 'Uus liitumisavaldus: %s', 'skduo' ), $data['child_name'] ),
		implode( "\n", $body_lines ),
		[
			'Content-Type: text/plain; charset=UTF-8',
			'Reply-To: ' . $data['parent_name'] . ' <' . $data['email'] . '>',
		]
	);

	wp_safe_redirect( add_query_arg( 'noored_form', $sent ? 'success' : 'error', $redirect ) . '#liitumisavaldus' );
	exit;
}
