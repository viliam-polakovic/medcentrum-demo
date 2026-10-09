<?php
/**
 * Languages: translation loading, per-language labels for services and
 * insurers, and Polylang integration (booking page in every language).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'mcb_load_textdomain', 0 );

function mcb_load_textdomain() {
	load_plugin_textdomain( 'medcentrum-booking', false, dirname( plugin_basename( MCB_FILE ) ) . '/languages' );
}

/** Two-letter code of the language being shown (de, en, sk…). */
function mcb_lang() {
	return substr( determine_locale(), 0, 2 );
}

/** Language the clinic works in: Polylang's default language, else the site language. */
function mcb_site_locale() {
	if ( function_exists( 'pll_default_language' ) && pll_default_language( 'locale' ) ) {
		return pll_default_language( 'locale' );
	}
	$locale = get_option( 'WPLANG' );
	return $locale ? $locale : 'en_US';
}

/** Runs $callback with $locale active, e.g. to write an e-mail in the patient's language. */
function mcb_with_locale( $locale, callable $callback ) {
	$switched = $locale && switch_to_locale( $locale );
	try {
		return $callback();
	} finally {
		if ( $switched ) {
			restore_previous_locale();
		}
	}
}

/**
 * Parses one line of the services or insurers setting. A line can carry
 * translations:  de: Physiotherapie | en: Physiotherapy | sk: Fyzioterapia
 * The first text is the stored value; the label follows the visitor's language.
 */
function mcb_parse_item( $line ) {
	$item = array(
		'value'  => '',
		'labels' => array(),
	);
	foreach ( explode( '|', $line ) as $part ) {
		$part = trim( $part );
		if ( '' === $part ) {
			continue;
		}
		$lang = '*';
		if ( preg_match( '/^([a-z]{2}):\s*(.+)$/u', $part, $m ) ) {
			$lang = $m[1];
			$part = trim( $m[2] );
		}
		$item['labels'][ $lang ] = $part;
		if ( '' === $item['value'] ) {
			$item['value'] = $part;
		}
	}
	return $item;
}

function mcb_item_label( array $item, $lang = null ) {
	$lang = $lang ? $lang : mcb_lang();
	return $item['labels'][ $lang ] ?? $item['labels']['*'] ?? $item['value'];
}

/** Label of a stored value (e.g. the service of a booking) in the current language. */
function mcb_label_of( array $items, $value ) {
	foreach ( $items as $item ) {
		if ( $item['value'] === $value ) {
			return mcb_item_label( $item );
		}
	}
	return $value;
}

function mcb_values( array $items ) {
	return wp_list_pluck( $items, 'value' );
}

/**
 * Translates a plugin text into a given locale straight from the plugin's
 * translation files, without switching the whole site's language.
 */
function mcb_translate_to( $text, $locale ) {
	static $files = array();
	if ( ! array_key_exists( $locale, $files ) ) {
		$files[ $locale ] = false;
		$base             = MCB_DIR . 'languages/medcentrum-booking-' . $locale;
		foreach ( array( '.l10n.php', '.mo' ) as $ext ) {
			if ( is_readable( $base . $ext ) ) {
				$files[ $locale ] = WP_Translation_File::create( $base . $ext );
				break;
			}
		}
	}
	$translated = $files[ $locale ] ? $files[ $locale ]->translate( $text ) : false;
	return $translated ? $translated : $text;
}

/* --------------------------------------------------------------------------
 * Polylang: the booking page exists once per language.
 * ----------------------------------------------------------------------- */

add_action( 'admin_init', 'mcb_sync_booking_page_translations' );

function mcb_sync_booking_page_translations() {
	if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return;
	}
	$page_id = (int) get_option( 'mcb_booking_page' );
	$slugs   = pll_languages_list( array( 'fields' => 'slug' ) );
	if ( ! $page_id || ! get_post( $page_id ) || ! $slugs ) {
		return;
	}
	if ( ! pll_get_post_language( $page_id ) ) {
		pll_set_post_language( $page_id, pll_default_language() );
	}
	$translations = pll_get_post_translations( $page_id );
	if ( ! array_diff( $slugs, array_keys( $translations ) ) ) {
		return;
	}

	$locales = pll_languages_list( array( 'fields' => 'locale' ) );
	foreach ( $slugs as $i => $slug ) {
		if ( isset( $translations[ $slug ] ) ) {
			continue;
		}
		$title = mcb_translate_to( 'Book an appointment', $locales[ $i ] );
		$id    = wp_insert_post( mcb_booking_page_args( $title ) );
		if ( $id && ! is_wp_error( $id ) ) {
			pll_set_post_language( $id, $slug );
			$translations[ $slug ] = $id;
		}
	}
	pll_save_post_translations( $translations );
}
