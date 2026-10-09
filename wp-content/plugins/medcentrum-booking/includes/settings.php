<?php
/**
 * Plugin settings: services, opening hours, slot length, closed days.
 */

defined( 'ABSPATH' ) || exit;

/** Weekday names (ISO-8601 numbering, Monday = 1) in the current language. */
function mcb_weekdays() {
	global $wp_locale;
	$days = array();
	for ( $n = 1; $n <= 7; $n++ ) {
		$days[ $n ] = $wp_locale->get_weekday( $n % 7 );
	}
	return $days;
}

function mcb_default_settings() {
	$hours = array();
	foreach ( range( 1, 7 ) as $day ) {
		$hours[ $day ] = array(
			'on'   => $day <= 5 ? 1 : 0,
			'from' => '08:00',
			'to'   => 5 === $day ? '14:00' : '16:00',
		);
	}

	return array(
		'clinic_name'  => get_bloginfo( 'name' ),
		'notify_email' => get_option( 'admin_email' ),
		'phone'        => '',
		'services'     => implode(
			"\n",
			array(
				'de: Allgemeine Untersuchung | en: General examination | sk: Všeobecné vyšetrenie',
				'de: Vorsorgeuntersuchung | en: Preventive check-up | sk: Preventívna prehliadka',
				'de: Neurologische Untersuchung | en: Neurological examination | sk: Neurologické vyšetrenie',
				'de: Kardiologische Untersuchung | en: Cardiology examination | sk: Kardiologické vyšetrenie',
				'de: Orthopädische Untersuchung | en: Orthopaedic examination | sk: Ortopedické vyšetrenie',
				'de: Physiotherapie | en: Physiotherapy | sk: Fyzioterapia',
				'de: Ultraschall und Diagnostik | en: Ultrasound and diagnostics | sk: USG a diagnostika',
				'de: Blutabnahme und Labor | en: Blood test and laboratory | sk: Odber krvi a laboratórium',
			)
		),
		'insurers'     => "VšZP\nDôvera\nUnion\nde: Selbstzahler | en: Self-payer | sk: Samoplatca",
		'hours'        => $hours,
		'break_from'   => '12:00',
		'break_to'     => '12:30',
		'slot'         => 30,
		'capacity'     => 1,
		'horizon'      => 60,
		'lead_hours'   => 2,
		'closed_dates' => '',
		'auto_confirm' => 1,
	);
}

function mcb_settings() {
	$saved = get_option( 'mcb_settings', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), mcb_default_settings() );
}

/** Splits a textarea value into trimmed, non-empty lines. */
function mcb_lines( $text ) {
	$lines = array_map( 'trim', preg_split( '/\R/', (string) $text ) );
	return array_values(
		array_filter(
			$lines,
			function ( $line ) {
				return '' !== $line;
			}
		)
	);
}

/** Services as items with a stored value and per-language labels, see mcb_parse_item(). */
function mcb_services() {
	return array_map( 'mcb_parse_item', mcb_lines( mcb_settings()['services'] ) );
}

function mcb_insurers() {
	return array_map( 'mcb_parse_item', mcb_lines( mcb_settings()['insurers'] ) );
}

/**
 * Closed days are entered one per line, either a single date (2026-12-24)
 * or a range (2026-12-24 - 2026-12-31). Anything after the date is a comment.
 */
function mcb_is_closed_date( $date ) {
	foreach ( mcb_lines( mcb_settings()['closed_dates'] ) as $line ) {
		if ( ! preg_match( '/^(\d{4}-\d{2}-\d{2})(?:\s*[-–]\s*(\d{4}-\d{2}-\d{2}))?/u', $line, $m ) ) {
			continue;
		}
		$from = $m[1];
		$to   = isset( $m[2] ) ? $m[2] : $from;
		if ( $date >= $from && $date <= $to ) {
			return true;
		}
	}
	return false;
}

add_action(
	'admin_init',
	function () {
		register_setting(
			'mcb_settings_group',
			'mcb_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'mcb_sanitize_settings',
			)
		);
	}
);

add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'edit.php?post_type=' . MCB_CPT,
			__( 'Booking settings', 'medcentrum-booking' ),
			__( 'Settings', 'medcentrum-booking' ),
			'manage_options',
			'mcb-settings',
			'mcb_render_settings_page'
		);
	}
);

function mcb_sanitize_time( $value, $fallback ) {
	$value = is_string( $value ) ? trim( $value ) : '';
	return preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $value ) ? $value : $fallback;
}

function mcb_sanitize_settings( $in ) {
	$in       = is_array( $in ) ? $in : array();
	$defaults = mcb_default_settings();
	$clamp    = function ( $key, $min, $max ) use ( $in, $defaults ) {
		$value = isset( $in[ $key ] ) ? (int) $in[ $key ] : $defaults[ $key ];
		return max( $min, min( $max, $value ) );
	};

	$hours = array();
	foreach ( range( 1, 7 ) as $day ) {
		$row           = isset( $in['hours'][ $day ] ) && is_array( $in['hours'][ $day ] ) ? $in['hours'][ $day ] : array();
		$hours[ $day ] = array(
			'on'   => empty( $row['on'] ) ? 0 : 1,
			'from' => mcb_sanitize_time( $row['from'] ?? '', $defaults['hours'][ $day ]['from'] ),
			'to'   => mcb_sanitize_time( $row['to'] ?? '', $defaults['hours'][ $day ]['to'] ),
		);
	}

	return array(
		'clinic_name'  => sanitize_text_field( $in['clinic_name'] ?? '' ),
		'notify_email' => sanitize_email( $in['notify_email'] ?? '' ),
		'phone'        => sanitize_text_field( $in['phone'] ?? '' ),
		'services'     => sanitize_textarea_field( $in['services'] ?? '' ),
		'insurers'     => sanitize_textarea_field( $in['insurers'] ?? '' ),
		'hours'        => $hours,
		'break_from'   => mcb_sanitize_time( $in['break_from'] ?? '', '' ),
		'break_to'     => mcb_sanitize_time( $in['break_to'] ?? '', '' ),
		'slot'         => $clamp( 'slot', 5, 240 ),
		'capacity'     => $clamp( 'capacity', 1, 20 ),
		'horizon'      => $clamp( 'horizon', 1, 365 ),
		'lead_hours'   => $clamp( 'lead_hours', 0, 168 ),
		'closed_dates' => sanitize_textarea_field( $in['closed_dates'] ?? '' ),
		'auto_confirm' => empty( $in['auto_confirm'] ) ? 0 : 1,
	);
}

function mcb_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s    = mcb_settings();
	$name = function ( $key ) {
		return 'mcb_settings[' . $key . ']';
	};
	$translations_hint = __( 'Translations go on the same line: de: Physiotherapie | en: Physiotherapy | sk: Fyzioterapia. The first text is stored with each booking – do not change it once bookings exist.', 'medcentrum-booking' );
	?>
	<div class="wrap mcb-settings">
		<h1><?php esc_html_e( 'Booking settings', 'medcentrum-booking' ); ?></h1>
		<?php settings_errors(); ?>
		<p>
			<?php
			/* translators: %s: shortcode */
			printf( esc_html__( 'Add the booking form to any page with the %s shortcode. The MedCentrum theme also shows it on the home page.', 'medcentrum-booking' ), '<code>[medcentrum_rezervacia]</code>' );
			?>
		</p>

		<form method="post" action="options.php">
			<?php settings_fields( 'mcb_settings_group' ); ?>

			<h2 class="title"><?php esc_html_e( 'Clinic', 'medcentrum-booking' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="mcb-clinic"><?php esc_html_e( 'Name', 'medcentrum-booking' ); ?></label></th>
					<td><input id="mcb-clinic" class="regular-text" type="text" name="<?php echo esc_attr( $name( 'clinic_name' ) ); ?>" value="<?php echo esc_attr( $s['clinic_name'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-email"><?php esc_html_e( 'E-mail for new bookings', 'medcentrum-booking' ); ?></label></th>
					<td><input id="mcb-email" class="regular-text" type="email" name="<?php echo esc_attr( $name( 'notify_email' ) ); ?>" value="<?php echo esc_attr( $s['notify_email'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-phone"><?php esc_html_e( 'Phone', 'medcentrum-booking' ); ?></label></th>
					<td><input id="mcb-phone" class="regular-text" type="text" name="<?php echo esc_attr( $name( 'phone' ) ); ?>" value="<?php echo esc_attr( $s['phone'] ); ?>">
					<p class="description"><?php esc_html_e( 'Shown to patients who cannot find a suitable slot.', 'medcentrum-booking' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-services"><?php esc_html_e( 'Services', 'medcentrum-booking' ); ?></label></th>
					<td><textarea id="mcb-services" class="large-text" rows="9" name="<?php echo esc_attr( $name( 'services' ) ); ?>"><?php echo esc_textarea( $s['services'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One service per line. Patients choose it in the first step.', 'medcentrum-booking' ); ?> <?php echo esc_html( $translations_hint ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-insurers"><?php esc_html_e( 'Health insurers', 'medcentrum-booking' ); ?></label></th>
					<td><textarea id="mcb-insurers" class="large-text" rows="4" name="<?php echo esc_attr( $name( 'insurers' ) ); ?>"><?php echo esc_textarea( $s['insurers'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One per line. Leave empty to hide the field from the form.', 'medcentrum-booking' ); ?></p></td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'Opening hours', 'medcentrum-booking' ); ?></h2>
			<table class="widefat striped mcb-hours">
				<thead><tr><th><?php esc_html_e( 'Day', 'medcentrum-booking' ); ?></th><th><?php esc_html_e( 'Open', 'medcentrum-booking' ); ?></th><th><?php esc_html_e( 'From', 'medcentrum-booking' ); ?></th><th><?php esc_html_e( 'To', 'medcentrum-booking' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( mcb_weekdays() as $day => $label ) : ?>
					<?php $row = $s['hours'][ $day ] ?? array(); ?>
					<tr>
						<td><strong><?php echo esc_html( $label ); ?></strong></td>
						<td><input type="checkbox" value="1" name="<?php echo esc_attr( $name( 'hours' ) . "[$day][on]" ); ?>" <?php checked( ! empty( $row['on'] ) ); ?> aria-label="<?php echo esc_attr( $label ); ?>"></td>
						<td><input type="time" step="300" name="<?php echo esc_attr( $name( 'hours' ) . "[$day][from]" ); ?>" value="<?php echo esc_attr( $row['from'] ?? '' ); ?>"></td>
						<td><input type="time" step="300" name="<?php echo esc_attr( $name( 'hours' ) . "[$day][to]" ); ?>" value="<?php echo esc_attr( $row['to'] ?? '' ); ?>"></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Lunch break', 'medcentrum-booking' ); ?></th>
					<td>
						<input type="time" step="300" name="<?php echo esc_attr( $name( 'break_from' ) ); ?>" value="<?php echo esc_attr( $s['break_from'] ); ?>" aria-label="<?php esc_attr_e( 'From', 'medcentrum-booking' ); ?>">
						–
						<input type="time" step="300" name="<?php echo esc_attr( $name( 'break_to' ) ); ?>" value="<?php echo esc_attr( $s['break_to'] ); ?>" aria-label="<?php esc_attr_e( 'To', 'medcentrum-booking' ); ?>">
						<p class="description"><?php esc_html_e( 'Leave empty if no break should be left out of the calendar.', 'medcentrum-booking' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-slot"><?php esc_html_e( 'Appointment length', 'medcentrum-booking' ); ?></label></th>
					<td><input id="mcb-slot" class="small-text" type="number" min="5" max="240" step="5" name="<?php echo esc_attr( $name( 'slot' ) ); ?>" value="<?php echo esc_attr( $s['slot'] ); ?>"> <?php esc_html_e( 'minutes', 'medcentrum-booking' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-capacity"><?php esc_html_e( 'Patients per slot', 'medcentrum-booking' ); ?></label></th>
					<td><input id="mcb-capacity" class="small-text" type="number" min="1" max="20" name="<?php echo esc_attr( $name( 'capacity' ) ); ?>" value="<?php echo esc_attr( $s['capacity'] ); ?>">
					<p class="description"><?php esc_html_e( 'Increase if several doctors see patients at the same time.', 'medcentrum-booking' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-horizon"><?php esc_html_e( 'Book ahead', 'medcentrum-booking' ); ?></label></th>
					<td><input id="mcb-horizon" class="small-text" type="number" min="1" max="365" name="<?php echo esc_attr( $name( 'horizon' ) ); ?>" value="<?php echo esc_attr( $s['horizon'] ); ?>"> <?php esc_html_e( 'days', 'medcentrum-booking' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-lead"><?php esc_html_e( 'Earliest', 'medcentrum-booking' ); ?></label></th>
					<td><input id="mcb-lead" class="small-text" type="number" min="0" max="168" name="<?php echo esc_attr( $name( 'lead_hours' ) ); ?>" value="<?php echo esc_attr( $s['lead_hours'] ); ?>"> <?php esc_html_e( 'hours from now', 'medcentrum-booking' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="mcb-closed"><?php esc_html_e( 'Closed days', 'medcentrum-booking' ); ?></label></th>
					<td><textarea id="mcb-closed" class="large-text code" rows="5" placeholder="2026-12-24 - 2027-01-01" name="<?php echo esc_attr( $name( 'closed_dates' ) ); ?>"><?php echo esc_textarea( $s['closed_dates'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One date (YYYY-MM-DD) or range per line – public holidays, vacations. Text after the date is a note.', 'medcentrum-booking' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Confirmation', 'medcentrum-booking' ); ?></th>
					<td><label><input type="checkbox" value="1" name="<?php echo esc_attr( $name( 'auto_confirm' ) ); ?>" <?php checked( ! empty( $s['auto_confirm'] ) ); ?>> <?php esc_html_e( 'Confirm online bookings automatically', 'medcentrum-booking' ); ?></label>
					<p class="description"><?php esc_html_e( 'When off, bookings wait for your confirmation. The patient first gets an e-mail that the request was received, then another one once you confirm.', 'medcentrum-booking' ); ?></p></td>
				</tr>
			</table>

			<?php submit_button( __( 'Save settings', 'medcentrum-booking' ) ); ?>
		</form>
	</div>
	<?php
}
