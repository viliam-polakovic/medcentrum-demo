<?php
/**
 * Demo setup for the local WordPress Playground preview only:
 * Polylang languages (DE default, EN, SK), translated demo posts and a few
 * bookings. Not needed on the real website.
 */

// Run as an admin request so Polylang loads its admin model.
define( 'WP_ADMIN', true );
require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/admin.php';
wp_set_current_user( 1 );

/* Languages ---------------------------------------------------------------- */

$model = PLL()->model;
foreach ( array( 'de_DE', 'en_US', 'sk_SK' ) as $order => $locale ) {
	$slug = substr( $locale, 0, 2 );
	if ( ! $model->languages->get( $slug ) ) {
		$model->languages->add(
			array(
				'locale'     => $locale,
				'term_group' => $order,
			)
		);
	}
}
$options                 = PLL()->options;
$options['default_lang'] = 'de';
$options['browser']      = false; // Always start in German, ignore the browser language.
$options['hide_default'] = true;  // German at /, English at /en/, Slovak at /sk/.
$options->save();
$model->clean_languages_cache();
$model->set_language_in_mass();
delete_transient( 'pll_activation_redirect' );
if ( class_exists( 'PLL_Admin_Notices' ) ) {
	PLL_Admin_Notices::dismiss( 'wizard' );
}

// Site tagline per language (Polylang → Translations → Strings).
foreach ( array( 'en' => 'Health & prevention', 'sk' => 'Zdravie & prevencia' ) as $slug => $tagline ) {
	$language = $model->languages->get( $slug );
	$mo       = new PLL_MO();
	$mo->import_from_db( $language );
	// WordPress stores the tagline HTML-escaped ("&amp;"), so the source and translation must match that.
	$mo->add_entry( $mo->make_entry( get_option( 'blogdescription' ), esc_html( $tagline ) ) );
	$mo->export_to_db( $language );
}

/* Content ------------------------------------------------------------------ */

wp_delete_post( 1, true ); // "Hello world!"

$news = array(
	array(
		'de' => array( 'Grippesaison: Impfung ohne Termin', 'Ab Oktober impfen wir an jedem Werktag von 7:30 bis 10:00 Uhr gegen Grippe. Bringen Sie einfach Ihre Versichertenkarte mit.' ),
		'en' => array( 'Flu season: vaccination without an appointment', 'From October we vaccinate against flu every weekday from 7:30 to 10:00. Just bring your insurance card.' ),
		'sk' => array( 'Chrípková sezóna: očkovanie bez objednania', 'Od októbra očkujeme proti chrípke každý pracovný deň od 7:30 do 10:00. Stačí prísť s preukazom poistenca.' ),
	),
	array(
		'de' => array( 'Neue Physiotherapie in Ružinov', 'Wir haben neue Räume mit modernen Trainingsgeräten und einem erfahrenen Team von Physiotherapeuten eröffnet.' ),
		'en' => array( 'New physiotherapy clinic in Ružinov', 'We have opened new premises with modern exercise equipment and an experienced team of physiotherapists.' ),
		'sk' => array( 'Nová ambulancia fyzioterapie v Ružinove', 'Otvorili sme nové priestory s modernými cvičebnými pomôckami a skúseným tímom fyzioterapeutov.' ),
	),
	array(
		'de' => array( 'So bereiten Sie sich auf die Vorsorgeuntersuchung vor', 'Kommen Sie zur Blutabnahme nüchtern und bringen Sie eine Liste Ihrer Medikamente und frühere Befunde mit.' ),
		'en' => array( 'How to prepare for a preventive check-up', 'Come to the blood test on an empty stomach and bring a list of your medication and previous results.' ),
		'sk' => array( 'Ako sa pripraviť na preventívnu prehliadku', 'Na odber krvi prichádzajte nalačno, vezmite si zoznam liekov a výsledky predchádzajúcich vyšetrení.' ),
	),
);
foreach ( $news as $i => $item ) {
	$translations = array();
	foreach ( $item as $lang => $text ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => $text[0],
				'post_content' => '<!-- wp:paragraph --><p>' . $text[1] . '</p><!-- /wp:paragraph -->',
				'post_date'    => wp_date( 'Y-m-d H:i:s', time() - $i * 6 * DAY_IN_SECONDS ),
			)
		);
		pll_set_post_language( $id, $lang );
		$translations[ $lang ] = $id;
	}
	pll_save_post_translations( $translations );
}

update_option(
	'mcb_settings',
	array_merge(
		mcb_settings(),
		array(
			'clinic_name' => 'MedCentrum',
			'phone'       => '+421 2 123 456 78',
		)
	)
);
mcb_sync_booking_page_translations();

// The admin for this preview speaks Slovak; visitors start in German.
update_user_meta( 1, 'locale', 'sk_SK' );

/* A few existing bookings so the calendar shows taken slots ---------------- */

$people = array( array( 'Ján', 'Novák' ), array( 'Eva', 'Kováčová' ), array( 'Peter', 'Horváth' ), array( 'Zuzana', 'Tóthová' ), array( 'Marek', 'Varga' ), array( 'Anna', 'Baláž' ) );
$values = mcb_values( mcb_services() );
$day    = current_datetime()->modify( '+1 day' );
$made   = 0;
for ( $i = 0; $i < 14 && $made < 12; $i++, $day = $day->modify( '+1 day' ) ) {
	foreach ( array_slice( mcb_free_slots( $day->format( 'Y-m-d' ) ), $i % 3, 2 ) as $time ) {
		$p = $people[ $made % count( $people ) ];
		mcb_create_booking(
			array(
				'service'    => $values[ $made % count( $values ) ],
				'date'       => $day->format( 'Y-m-d' ),
				'time'       => $time,
				'first_name' => $p[0],
				'last_name'  => $p[1],
				'email'      => strtolower( remove_accents( $p[1] ) ) . '@example.com',
				'phone'      => '+421 900 000 00' . $made,
				'birthdate'  => '',
				'insurance'  => 'VšZP',
				'note'       => '',
				'status'     => 0 === $made % 4 ? 'pending' : 'confirmed',
				'source'     => 'online',
				'locale'     => array( 'de_DE', 'sk_SK', 'en_US' )[ $made % 3 ],
			)
		);
		++$made;
	}
}

// Polylang adds its /en/ and /sk/ rules only once it boots with languages,
// so let the next request rebuild the rules instead of flushing here.
delete_option( 'rewrite_rules' );
