<?php
/**
 * Plugin Name:       MedCentrum – Online Booking
 * Description:       Booking calendar where patients pick a specific day and time. Add the form to any page with the [medcentrum_rezervacia] shortcode. Multilingual (DE / EN / SK), works with Polylang.
 * Version:           1.1.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            MedCentrum
 * License:           GPL-2.0-or-later
 * Text Domain:       medcentrum-booking
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'MCB_VERSION', '1.1.0' );
define( 'MCB_FILE', __FILE__ );
define( 'MCB_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCB_URL', plugin_dir_url( __FILE__ ) );
define( 'MCB_CPT', 'mcb_booking' );

require_once MCB_DIR . 'includes/i18n.php';
require_once MCB_DIR . 'includes/settings.php';
require_once MCB_DIR . 'includes/availability.php';
require_once MCB_DIR . 'includes/bookings.php';
require_once MCB_DIR . 'includes/emails.php';
require_once MCB_DIR . 'includes/rest.php';
require_once MCB_DIR . 'includes/shortcode.php';

register_activation_hook( __FILE__, 'mcb_activate' );

function mcb_activate() {
	// Activation runs after `init`, so load translations for the page title here.
	mcb_load_textdomain();
	mcb_register_post_type();
	mcb_grant_caps();
	mcb_ensure_booking_page();
	flush_rewrite_rules();
}

/**
 * Capabilities for the bookings post type. Only administrators and editors
 * get them, so authors/contributors never see patient data.
 */
function mcb_caps() {
	return array(
		'edit_mcb_bookings',
		'edit_others_mcb_bookings',
		'edit_published_mcb_bookings',
		'edit_private_mcb_bookings',
		'publish_mcb_bookings',
		'read_private_mcb_bookings',
		'delete_mcb_bookings',
		'delete_others_mcb_bookings',
		'delete_published_mcb_bookings',
		'delete_private_mcb_bookings',
	);
}

function mcb_grant_caps() {
	foreach ( array( 'administrator', 'editor' ) as $role_name ) {
		$role = get_role( $role_name );
		if ( ! $role ) {
			continue;
		}
		foreach ( mcb_caps() as $cap ) {
			$role->add_cap( $cap );
		}
	}
	update_option( 'mcb_caps_version', MCB_VERSION, false );
}

// Re-grant capabilities when the plugin files are updated without re-activation.
add_action(
	'admin_init',
	function () {
		if ( get_option( 'mcb_caps_version' ) !== MCB_VERSION ) {
			mcb_grant_caps();
		}
	}
);

function mcb_booking_page_args( $title ) {
	return array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => sanitize_title( $title ),
		'post_content' => "<!-- wp:shortcode -->\n[medcentrum_rezervacia]\n<!-- /wp:shortcode -->",
	);
}

function mcb_ensure_booking_page() {
	$page_id = (int) get_option( 'mcb_booking_page' );
	if ( $page_id && get_post( $page_id ) ) {
		return;
	}
	$page_id = wp_insert_post( mcb_booking_page_args( __( 'Book an appointment', 'medcentrum-booking' ) ) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_option( 'mcb_booking_page', $page_id );
	}
}

/** URL of the booking page in the current language, used by the theme for "Book" buttons. */
function mcb_booking_page_url() {
	$page_id = (int) get_option( 'mcb_booking_page' );
	if ( $page_id && function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $page_id );
		$page_id    = $translated ? $translated : $page_id;
	}
	return $page_id && 'publish' === get_post_status( $page_id ) ? get_permalink( $page_id ) : '';
}
