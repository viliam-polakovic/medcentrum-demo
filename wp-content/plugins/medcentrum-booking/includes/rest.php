<?php
/**
 * Public REST endpoints used by the booking form:
 *
 *   GET  /wp-json/medcentrum/v1/month?month=2026-10   free slot count per day
 *   GET  /wp-json/medcentrum/v1/slots?date=2026-10-15 free times on a day
 *   POST /wp-json/medcentrum/v1/book                  create a booking
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	function () {
		$ns = 'medcentrum/v1';

		register_rest_route(
			$ns,
			'/month',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'args'                => array(
					'month' => array(
						'required'          => true,
						'validate_callback' => function ( $value ) {
							return is_string( $value ) && preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $value );
						},
					),
				),
				'callback'            => function ( WP_REST_Request $request ) {
					return mcb_no_store( array( 'days' => mcb_month_availability( $request['month'] ) ) );
				},
			)
		);

		register_rest_route(
			$ns,
			'/slots',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'args'                => array(
					'date' => array(
						'required'          => true,
						'validate_callback' => 'mcb_valid_date',
					),
				),
				'callback'            => function ( WP_REST_Request $request ) {
					return mcb_no_store(
						array(
							'date'  => $request['date'],
							'slots' => mcb_free_slots( $request['date'] ),
						)
					);
				},
			)
		);

		register_rest_route(
			$ns,
			'/book',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => '__return_true',
				'callback'            => 'mcb_rest_book',
			)
		);
	}
);

/** Availability changes with every booking, so it must never be cached. */
function mcb_no_store( $data ) {
	$response = rest_ensure_response( $data );
	$response->header( 'Cache-Control', 'no-store, max-age=0' );
	return $response;
}

function mcb_rest_book( WP_REST_Request $request ) {
	$p   = (array) $request->get_json_params() + (array) $request->get_body_params();
	$str = function ( $key, $max = 120 ) use ( $p ) {
		return mb_substr( sanitize_text_field( (string) ( $p[ $key ] ?? '' ) ), 0, $max );
	};

	// Answer (and later e-mail the patient) in the language the form was shown in.
	$locale = $str( 'locale', 20 );
	if ( preg_match( '/^[a-z]{2,3}(_[A-Z]{2})?(_[a-z]+)?$/', $locale ) ) {
		switch_to_locale( $locale );
	}

	// Honeypot: real visitors never see or fill this field.
	if ( '' !== $str( 'website' ) ) {
		return new WP_Error( 'mcb_spam', __( 'The request could not be processed.', 'medcentrum-booking' ), array( 'status' => 400 ) );
	}

	$rate_key = 'mcb_rate_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	if ( (int) get_transient( $rate_key ) >= 5 ) {
		return new WP_Error( 'mcb_rate', __( 'Too many bookings were made from this device. Please try again later or call us.', 'medcentrum-booking' ), array( 'status' => 429 ) );
	}

	$data = array(
		'service'    => $str( 'service' ),
		'date'       => $str( 'date', 10 ),
		'time'       => $str( 'time', 5 ),
		'first_name' => $str( 'first_name', 80 ),
		'last_name'  => $str( 'last_name', 80 ),
		'email'      => sanitize_email( (string) ( $p['email'] ?? '' ) ),
		'phone'      => $str( 'phone', 30 ),
		'birthdate'  => $str( 'birthdate', 10 ),
		'insurance'  => $str( 'insurance', 80 ),
		'note'       => mb_substr( sanitize_textarea_field( (string) ( $p['note'] ?? '' ) ), 0, 1000 ),
	);

	$errors = array();
	$m = mcb_messages();
	if ( ! in_array( $data['service'], mcb_values( mcb_services() ), true ) ) {
		$errors['service'] = $m['service'];
	}
	if ( ! mcb_valid_date( $data['date'] ) || '' === mcb_sanitize_time( $data['time'], '' ) ) {
		$errors['slot'] = $m['slot'];
	}
	if ( '' === $data['first_name'] ) {
		$errors['first_name'] = $m['first_name'];
	}
	if ( '' === $data['last_name'] ) {
		$errors['last_name'] = $m['last_name'];
	}
	if ( ! is_email( $data['email'] ) ) {
		$errors['email'] = $m['email'];
	}
	if ( ! preg_match( '/^\+?[0-9 ()\/-]{6,20}$/', $data['phone'] ) ) {
		$errors['phone'] = $m['phone'];
	}
	if ( '' !== $data['birthdate'] && ( ! mcb_valid_date( $data['birthdate'] ) || $data['birthdate'] > current_datetime()->format( 'Y-m-d' ) ) ) {
		$errors['birthdate'] = $m['birthdate'];
	}
	$insurers = mcb_insurers();
	if ( $insurers && ! in_array( $data['insurance'], mcb_values( $insurers ), true ) ) {
		$errors['insurance'] = $m['insurance'];
	}
	if ( empty( $p['consent'] ) ) {
		$errors['consent'] = $m['consent'];
	}
	if ( $errors ) {
		return new WP_Error(
			'mcb_invalid',
			__( 'Please check the highlighted fields.', 'medcentrum-booking' ),
			array(
				'status' => 422,
				'fields' => $errors,
			)
		);
	}

	$lock = 'mcb_lock_' . md5( $data['date'] . ' ' . $data['time'] );
	if ( ! mcb_acquire_lock( $lock ) ) {
		return new WP_Error( 'mcb_busy', __( 'Someone else is booking this slot right now. Please try again in a moment.', 'medcentrum-booking' ), array( 'status' => 409 ) );
	}
	try {
		if ( ! in_array( $data['time'], mcb_free_slots( $data['date'] ), true ) ) {
			return new WP_Error( 'mcb_taken', __( 'This slot is no longer available. Please choose another time.', 'medcentrum-booking' ), array( 'status' => 409 ) );
		}
		$data['status'] = mcb_settings()['auto_confirm'] ? 'confirmed' : 'pending';
		$data['source'] = 'online';
		$data['locale'] = determine_locale();
		$id             = mcb_create_booking( $data );
	} finally {
		mcb_release_lock( $lock );
	}

	if ( is_wp_error( $id ) ) {
		return new WP_Error( 'mcb_failed', __( 'The booking could not be saved. Please call us.', 'medcentrum-booking' ), array( 'status' => 500 ) );
	}

	set_transient( $rate_key, (int) get_transient( $rate_key ) + 1, HOUR_IN_SECONDS );
	mcb_send_new_booking_emails( $id );

	return array(
		'status'  => $data['status'],
		'when'    => mcb_human_datetime( $data['date'], $data['time'] ),
		'service' => mcb_label_of( mcb_services(), $data['service'] ),
		'email'   => $data['email'],
	);
}

/** Field messages, shared by server-side validation and the form script. */
function mcb_messages() {
	return array(
		'service'    => __( 'Please choose a service.', 'medcentrum-booking' ),
		'slot'       => __( 'Please choose a date and time.', 'medcentrum-booking' ),
		'first_name' => __( 'Please enter your first name.', 'medcentrum-booking' ),
		'last_name'  => __( 'Please enter your last name.', 'medcentrum-booking' ),
		'email'      => __( 'Please enter a valid e-mail address.', 'medcentrum-booking' ),
		'phone'      => __( 'Please enter a valid phone number.', 'medcentrum-booking' ),
		'birthdate'  => __( 'Please enter a valid date of birth.', 'medcentrum-booking' ),
		'insurance'  => __( 'Please choose your health insurer.', 'medcentrum-booking' ),
		'consent'    => __( 'We need your consent to process your data to book the appointment.', 'medcentrum-booking' ),
	);
}
