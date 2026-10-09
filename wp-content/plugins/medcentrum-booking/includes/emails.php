<?php
/**
 * Notification e-mails (plain text, sent with wp_mail).
 *
 * The clinic is written to in the site's main language, the patient in the
 * language they booked in. For reliable delivery configure SMTP on the site,
 * e.g. with WP Mail SMTP.
 */

defined( 'ABSPATH' ) || exit;

function mcb_clinic_name() {
	$name = mcb_settings()['clinic_name'];
	return $name ? $name : get_bloginfo( 'name' );
}

/** New online booking: notify the clinic and the patient. */
function mcb_send_new_booking_emails( $id ) {
	$b = mcb_booking_data( $id );

	mcb_with_locale(
		mcb_site_locale(),
		function () use ( $b, $id ) {
			$when = mcb_human_datetime( $b['date'], $b['time'] );
			$name = trim( $b['first_name'] . ' ' . $b['last_name'] );
			$rows = array(
				__( 'Appointment', 'medcentrum-booking' ) => $when,
				__( 'Service', 'medcentrum-booking' )     => mcb_label_of( mcb_services(), $b['service'] ),
				__( 'Patient', 'medcentrum-booking' )     => $name,
				__( 'Phone', 'medcentrum-booking' )       => $b['phone'],
				__( 'E-mail', 'medcentrum-booking' )      => $b['email'],
			);
			if ( $b['birthdate'] ) {
				$rows[ __( 'Date of birth', 'medcentrum-booking' ) ] = mcb_human_datetime( $b['birthdate'] );
			}
			if ( $b['insurance'] ) {
				$rows[ __( 'Health insurer', 'medcentrum-booking' ) ] = mcb_label_of( mcb_insurers(), $b['insurance'] );
			}
			$rows[ __( 'Language', 'medcentrum-booking' ) ] = strtoupper( substr( $b['locale'], 0, 2 ) );

			$lines = array( __( 'New online booking', 'medcentrum-booking' ), '' );
			foreach ( $rows as $label => $value ) {
				$lines[] = $label . ': ' . $value;
			}
			if ( $b['note'] ) {
				$lines[] = '';
				$lines[] = __( 'Note from the patient', 'medcentrum-booking' ) . ':';
				$lines[] = $b['note'];
			}
			$lines[] = '';
			$lines[] = 'pending' === $b['status'] ? __( 'The booking is waiting for your confirmation:', 'medcentrum-booking' ) : __( 'Booking details:', 'medcentrum-booking' );
			$lines[] = admin_url( 'post.php?post=' . $id . '&action=edit' );

			$s  = mcb_settings();
			$to = $s['notify_email'] ? $s['notify_email'] : get_option( 'admin_email' );
			wp_mail(
				$to,
				/* translators: 1: date and time, 2: patient name */
				sprintf( __( 'New booking: %1$s – %2$s', 'medcentrum-booking' ), $when, $name ),
				implode( "\n", $lines ),
				array( sprintf( 'Reply-To: %s <%s>', $name, $b['email'] ) )
			);
		}
	);

	mcb_send_patient_email( $id );
}

/** Tells the patient the current state of their booking, in their language. */
function mcb_send_patient_email( $id ) {
	$b = mcb_booking_data( $id );

	mcb_with_locale(
		$b['locale'] ? $b['locale'] : mcb_site_locale(),
		function () use ( $b ) {
			$clinic = mcb_clinic_name();
			$phone  = mcb_settings()['phone'];

			switch ( $b['status'] ) {
				case 'confirmed':
					/* translators: %s: clinic name */
					$subject = sprintf( __( 'Your appointment is confirmed – %s', 'medcentrum-booking' ), $clinic );
					$intro   = __( 'We confirm your appointment:', 'medcentrum-booking' );
					break;
				case 'cancelled':
					/* translators: %s: clinic name */
					$subject = sprintf( __( 'Your appointment has been cancelled – %s', 'medcentrum-booking' ), $clinic );
					$intro   = __( 'Your appointment has been cancelled:', 'medcentrum-booking' );
					break;
				default:
					/* translators: %s: clinic name */
					$subject = sprintf( __( 'We have received your request – %s', 'medcentrum-booking' ), $clinic );
					$intro   = __( 'We have received your appointment request. We will send you another e-mail once it is confirmed.', 'medcentrum-booking' );
			}

			$lines = array(
				__( 'Hello,', 'medcentrum-booking' ),
				'',
				$intro,
				'',
				__( 'Appointment', 'medcentrum-booking' ) . ': ' . mcb_human_datetime( $b['date'], $b['time'] ),
				__( 'Service', 'medcentrum-booking' ) . ': ' . mcb_label_of( mcb_services(), $b['service'] ),
				'',
			);
			if ( 'cancelled' !== $b['status'] ) {
				$lines[] = __( 'Please arrive 10 minutes early and bring your health insurance card.', 'medcentrum-booking' );
			}
			$lines[] = $phone
				/* translators: %s: phone number */
				? sprintf( __( 'If you need to change or cancel the appointment, call us at %s.', 'medcentrum-booking' ), $phone )
				: __( 'If you need to change or cancel the appointment, reply to this e-mail.', 'medcentrum-booking' );
			$lines[] = '';
			$lines[] = __( 'Kind regards', 'medcentrum-booking' );
			$lines[] = $clinic;

			wp_mail( $b['email'], $subject, implode( "\n", $lines ) );
		}
	);
}
