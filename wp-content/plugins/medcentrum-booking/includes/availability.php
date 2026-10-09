<?php
/**
 * Which days and times are still free.
 *
 * Dates are 'Y-m-d' strings and times 'H:i' strings in the site's timezone
 * (Settings → General → Timezone).
 */

defined( 'ABSPATH' ) || exit;

function mcb_valid_date( $date ) {
	if ( ! is_string( $date ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
		return false;
	}
	$parsed = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() );
	return $parsed && $parsed->format( 'Y-m-d' ) === $date;
}

function mcb_minutes( $hm ) {
	list( $h, $m ) = array_map( 'intval', explode( ':', $hm ) );
	return $h * 60 + $m;
}

/**
 * Slot start times the opening hours allow on a date, before existing
 * bookings are taken into account. Past slots and slots inside the
 * minimum lead time are left out.
 */
function mcb_schedule_slots( $date ) {
	if ( ! mcb_valid_date( $date ) ) {
		return array();
	}

	$s        = mcb_settings();
	$now      = current_datetime();
	$last_day = $now->modify( '+' . (int) $s['horizon'] . ' days' )->format( 'Y-m-d' );
	if ( $date < $now->format( 'Y-m-d' ) || $date > $last_day || mcb_is_closed_date( $date ) ) {
		return array();
	}

	$day   = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() );
	$hours = $s['hours'][ (int) $day->format( 'N' ) ] ?? null;
	if ( empty( $hours['on'] ) ) {
		return array();
	}

	$step        = max( 5, (int) $s['slot'] );
	$start       = mcb_minutes( $hours['from'] );
	$end         = mcb_minutes( $hours['to'] );
	$break_from  = $s['break_from'] && $s['break_to'] ? mcb_minutes( $s['break_from'] ) : null;
	$break_to    = null !== $break_from ? mcb_minutes( $s['break_to'] ) : null;
	$earliest_ts = $now->getTimestamp() + (int) $s['lead_hours'] * HOUR_IN_SECONDS;

	$slots = array();
	for ( $m = $start; $m + $step <= $end; $m += $step ) {
		if ( null !== $break_from && $m < $break_to && $m + $step > $break_from ) {
			continue;
		}
		$h   = intdiv( $m, 60 );
		$min = $m % 60;
		if ( $day->setTime( $h, $min )->getTimestamp() < $earliest_ts ) {
			continue;
		}
		$slots[] = sprintf( '%02d:%02d', $h, $min );
	}
	return $slots;
}

/**
 * Number of active (pending or confirmed) bookings per "Y-m-d H:i" between two dates.
 */
function mcb_booked_counts( $from, $to ) {
	$ids = get_posts(
		array(
			'post_type'        => MCB_CPT,
			'post_status'      => 'any',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'meta_query'       => array(
				array(
					'key'     => '_mcb_date',
					// ISO dates compare correctly as plain strings.
					'value'   => array( $from, $to ),
					'compare' => 'BETWEEN',
				),
				array(
					'key'     => '_mcb_status',
					'value'   => array( 'pending', 'confirmed' ),
					'compare' => 'IN',
				),
			),
		)
	);
	update_meta_cache( 'post', $ids );

	$counts = array();
	foreach ( $ids as $id ) {
		$key            = get_post_meta( $id, '_mcb_date', true ) . ' ' . get_post_meta( $id, '_mcb_time', true );
		$counts[ $key ] = ( $counts[ $key ] ?? 0 ) + 1;
	}
	return $counts;
}

function mcb_free_slots( $date, $counts = null ) {
	$slots = mcb_schedule_slots( $date );
	if ( ! $slots ) {
		return array();
	}
	$counts   = null === $counts ? mcb_booked_counts( $date, $date ) : $counts;
	$capacity = max( 1, (int) mcb_settings()['capacity'] );

	return array_values(
		array_filter(
			$slots,
			function ( $time ) use ( $date, $counts, $capacity ) {
				return ( $counts[ $date . ' ' . $time ] ?? 0 ) < $capacity;
			}
		)
	);
}

/**
 * Free slot count for every day of a month ('Y-m').
 */
function mcb_month_availability( $month ) {
	$first  = DateTimeImmutable::createFromFormat( '!Y-m-d', $month . '-01', wp_timezone() );
	$last   = $first->modify( 'last day of this month' );
	$counts = mcb_booked_counts( $first->format( 'Y-m-d' ), $last->format( 'Y-m-d' ) );

	$days = array();
	for ( $d = $first; $d <= $last; $d = $d->modify( '+1 day' ) ) {
		$key          = $d->format( 'Y-m-d' );
		$days[ $key ] = count( mcb_free_slots( $key, $counts ) );
	}
	return $days;
}

/**
 * Short-lived lock so two patients submitting the same slot at the same
 * moment cannot both get it. INSERT IGNORE on the unique option_name is
 * atomic, unlike add_option().
 */
function mcb_acquire_lock( $name ) {
	global $wpdb;
	$now   = time();
	$query = "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')";

	if ( 1 === (int) $wpdb->query( $wpdb->prepare( $query, $name, $now ) ) ) {
		return true;
	}
	// A lock older than 30 s was left behind by a crashed request.
	$held_since = (int) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name ) );
	if ( $held_since && $held_since < $now - 30 ) {
		mcb_release_lock( $name );
		return 1 === (int) $wpdb->query( $wpdb->prepare( $query, $name, $now ) );
	}
	return false;
}

function mcb_release_lock( $name ) {
	global $wpdb;
	$wpdb->delete( $wpdb->options, array( 'option_name' => $name ) );
}
