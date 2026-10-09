<?php
/**
 * Bookings post type and its admin screens.
 *
 * Each booking is a private post; its data lives in post meta prefixed _mcb_.
 * Staff can also add bookings by hand (e.g. taken over the phone).
 */

defined( 'ABSPATH' ) || exit;

function mcb_statuses() {
	return array(
		'pending'   => __( 'Awaiting confirmation', 'medcentrum-booking' ),
		'confirmed' => __( 'Confirmed', 'medcentrum-booking' ),
		'cancelled' => __( 'Cancelled', 'medcentrum-booking' ),
	);
}

add_action( 'init', 'mcb_register_post_type' );

function mcb_register_post_type() {
	register_post_type(
		MCB_CPT,
		array(
			'labels'              => array(
				'name'               => __( 'Bookings', 'medcentrum-booking' ),
				'singular_name'      => __( 'Booking', 'medcentrum-booking' ),
				'menu_name'          => __( 'Bookings', 'medcentrum-booking' ),
				'all_items'          => __( 'Bookings', 'medcentrum-booking' ),
				'add_new'            => __( 'Add booking', 'medcentrum-booking' ),
				'add_new_item'       => __( 'New booking', 'medcentrum-booking' ),
				'edit_item'          => __( 'Booking', 'medcentrum-booking' ),
				'search_items'       => __( 'Search', 'medcentrum-booking' ),
				'not_found'          => __( 'No bookings', 'medcentrum-booking' ),
				'not_found_in_trash' => __( 'The trash is empty', 'medcentrum-booking' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-calendar-alt',
			'supports'            => false,
			'capability_type'     => array( 'mcb_booking', 'mcb_bookings' ),
			'map_meta_cap'        => true,
			'rewrite'             => false,
			'query_var'           => false,
		)
	);
}

function mcb_get( $id, $key ) {
	return (string) get_post_meta( $id, '_mcb_' . $key, true );
}

function mcb_booking_data( $id ) {
	$keys = array( 'service', 'date', 'time', 'first_name', 'last_name', 'email', 'phone', 'birthdate', 'insurance', 'note', 'status', 'source', 'locale' );
	$data = array();
	foreach ( $keys as $key ) {
		$data[ $key ] = mcb_get( $id, $key );
	}
	return $data;
}

/** Inserts a booking. $data uses the keys from mcb_booking_data(). */
function mcb_create_booking( array $data ) {
	$id = wp_insert_post(
		array(
			'post_type'   => MCB_CPT,
			'post_status' => 'publish',
			'post_title'  => wp_slash( trim( $data['first_name'] . ' ' . $data['last_name'] ) ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	foreach ( $data as $key => $value ) {
		update_post_meta( $id, '_mcb_' . $key, wp_slash( $value ) );
	}
	update_post_meta( $id, '_mcb_start', $data['date'] . ' ' . $data['time'] );
	return $id;
}

/** Date (and time) in the current language, e.g. "Donnerstag, 15. Oktober 2026 um 09:30". */
function mcb_human_datetime( $date, $time = '' ) {
	if ( ! mcb_valid_date( $date ) ) {
		return trim( $date . ' ' . $time );
	}
	$timestamp = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() )->getTimestamp();
	/* translators: PHP date format for a booking date, see https://www.php.net/manual/datetime.format.php */
	$out = wp_date( __( 'l, F j, Y', 'medcentrum-booking' ), $timestamp );
	/* translators: 1: date, 2: time */
	return $time ? sprintf( __( '%1$s at %2$s', 'medcentrum-booking' ), $out, $time ) : $out;
}

/* --------------------------------------------------------------------------
 * List screen
 * ----------------------------------------------------------------------- */

add_filter(
	'manage_' . MCB_CPT . '_posts_columns',
	function ( $columns ) {
		return array(
			'cb'          => $columns['cb'],
			'mcb_when'    => __( 'Appointment', 'medcentrum-booking' ),
			'title'       => __( 'Patient', 'medcentrum-booking' ),
			'mcb_service' => __( 'Service', 'medcentrum-booking' ),
			'mcb_contact' => __( 'Contact', 'medcentrum-booking' ),
			'mcb_status'  => __( 'Status', 'medcentrum-booking' ),
		);
	}
);

add_action(
	'manage_' . MCB_CPT . '_posts_custom_column',
	function ( $column, $id ) {
		switch ( $column ) {
			case 'mcb_when':
				echo '<strong>' . esc_html( mcb_human_datetime( mcb_get( $id, 'date' ), mcb_get( $id, 'time' ) ) ) . '</strong>';
				break;
			case 'mcb_service':
				echo esc_html( mcb_label_of( mcb_services(), mcb_get( $id, 'service' ) ) );
				break;
			case 'mcb_contact':
				$phone = mcb_get( $id, 'phone' );
				$email = mcb_get( $id, 'email' );
				if ( $phone ) {
					printf( '<a href="tel:%s">%s</a><br>', esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ), esc_html( $phone ) );
				}
				if ( $email ) {
					printf( '<a href="mailto:%1$s">%1$s</a>', esc_html( $email ) );
				}
				break;
			case 'mcb_status':
				$status = mcb_get( $id, 'status' );
				printf( '<span class="mcb-badge mcb-badge--%s">%s</span>', esc_attr( $status ), esc_html( mcb_statuses()[ $status ] ?? $status ) );
				break;
		}
	},
	10,
	2
);

add_filter(
	'manage_edit-' . MCB_CPT . '_sortable_columns',
	function ( $columns ) {
		$columns['mcb_when'] = 'mcb_when';
		return $columns;
	}
);

add_filter(
	'disable_months_dropdown',
	function ( $disable, $post_type ) {
		return MCB_CPT === $post_type ? true : $disable;
	},
	10,
	2
);

add_filter(
	'post_row_actions',
	function ( $actions, $post ) {
		if ( MCB_CPT === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	},
	10,
	2
);

add_action(
	'restrict_manage_posts',
	function ( $post_type ) {
		if ( MCB_CPT !== $post_type ) {
			return;
		}
		$when   = isset( $_GET['mcb_when'] ) ? sanitize_key( $_GET['mcb_when'] ) : 'upcoming'; // phpcs:ignore WordPress.Security.NonceVerification
		$status = isset( $_GET['mcb_status'] ) ? sanitize_key( $_GET['mcb_status'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<select name="mcb_when" aria-label="<?php esc_attr_e( 'Period', 'medcentrum-booking' ); ?>">
			<option value="upcoming" <?php selected( $when, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming', 'medcentrum-booking' ); ?></option>
			<option value="today" <?php selected( $when, 'today' ); ?>><?php esc_html_e( 'Today', 'medcentrum-booking' ); ?></option>
			<option value="past" <?php selected( $when, 'past' ); ?>><?php esc_html_e( 'Past', 'medcentrum-booking' ); ?></option>
			<option value="all" <?php selected( $when, 'all' ); ?>><?php esc_html_e( 'All', 'medcentrum-booking' ); ?></option>
		</select>
		<select name="mcb_status" aria-label="<?php esc_attr_e( 'Status', 'medcentrum-booking' ); ?>">
			<option value=""><?php esc_html_e( 'All statuses', 'medcentrum-booking' ); ?></option>
			<?php foreach ( mcb_statuses() as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
);

add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || MCB_CPT !== $query->get( 'post_type' ) ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification
		$when   = isset( $_GET['mcb_when'] ) ? sanitize_key( $_GET['mcb_when'] ) : 'upcoming';
		$status = isset( $_GET['mcb_status'] ) ? sanitize_key( $_GET['mcb_status'] ) : '';
		$order  = isset( $_GET['order'] ) && 'desc' === strtolower( sanitize_key( $_GET['order'] ) ) ? 'DESC' : ( 'past' === $when ? 'DESC' : 'ASC' );
		$sorted = ! empty( $_GET['orderby'] ) && 'mcb_when' !== $_GET['orderby'];
		// phpcs:enable

		$today = current_datetime()->format( 'Y-m-d' );
		$meta  = array();
		if ( 'upcoming' === $when ) {
			$meta[] = array( 'key' => '_mcb_date', 'value' => $today, 'compare' => '>=' );
		} elseif ( 'today' === $when ) {
			$meta[] = array( 'key' => '_mcb_date', 'value' => $today );
		} elseif ( 'past' === $when ) {
			$meta[] = array( 'key' => '_mcb_date', 'value' => $today, 'compare' => '<' );
		}
		if ( isset( mcb_statuses()[ $status ] ) ) {
			$meta[] = array( 'key' => '_mcb_status', 'value' => $status );
		}
		if ( $meta ) {
			$query->set( 'meta_query', $meta );
		}
		if ( ! $sorted ) {
			$query->set( 'meta_key', '_mcb_start' );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'order', $order );
		}
	}
);

add_action(
	'admin_head',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || MCB_CPT !== $screen->post_type ) {
			return;
		}
		?>
		<style>
			.mcb-badge { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 12px; font-weight: 500; background: #f0f0f1; }
			.mcb-badge--confirmed { background: #e3f4ea; color: #1b6b3a; }
			.mcb-badge--pending { background: #fff4d6; color: #7a5800; }
			.mcb-badge--cancelled { background: #fbe7e7; color: #8a1f1f; text-decoration: line-through; }
			.column-mcb_when { width: 22%; }
			.column-mcb_status { width: 14%; }
			.mcb-fields { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px 20px; }
			.mcb-fields label { display: block; font-weight: 600; margin-bottom: 4px; }
			.mcb-fields input:not([type=checkbox]), .mcb-fields select, .mcb-fields textarea { width: 100%; }
			.mcb-fields .mcb-wide { grid-column: 1 / -1; }
			.mcb-meta { color: #646970; margin-top: 16px; }
		</style>
		<?php
	}
);

/* --------------------------------------------------------------------------
 * Edit screen
 * ----------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_' . MCB_CPT,
	function () {
		add_meta_box( 'mcb_details', __( 'Booking details', 'medcentrum-booking' ), 'mcb_render_meta_box', MCB_CPT, 'normal', 'high' );
	}
);

/** <option>s for service/insurer items; keeps a stored value that is no longer in the list. */
function mcb_select_options( array $items, $current ) {
	if ( '' !== $current && ! in_array( $current, mcb_values( $items ), true ) ) {
		$items[] = mcb_parse_item( $current );
	}
	foreach ( $items as $item ) {
		printf( '<option value="%s" %s>%s</option>', esc_attr( $item['value'] ), selected( $current, $item['value'], false ), esc_html( mcb_item_label( $item ) ) );
	}
}

function mcb_render_meta_box( $post ) {
	$b     = mcb_booking_data( $post->ID );
	$isnew = '' === $b['date'];
	if ( $isnew ) {
		$b['status'] = 'confirmed';
	}
	wp_nonce_field( 'mcb_save', 'mcb_nonce' );
	$field = function ( $key, $label, $type = 'text', $extra = '' ) use ( $b ) {
		printf(
			'<div><label for="mcb-%1$s">%2$s</label><input id="mcb-%1$s" type="%3$s" name="mcb[%1$s]" value="%4$s" %5$s></div>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( $type ),
			esc_attr( $b[ $key ] ),
			$extra // phpcs:ignore WordPress.Security.EscapeOutput -- static attributes only.
		);
	};
	?>
	<div class="mcb-fields">
		<div>
			<label for="mcb-service"><?php esc_html_e( 'Service', 'medcentrum-booking' ); ?></label>
			<select id="mcb-service" name="mcb[service]"><?php mcb_select_options( mcb_services(), $b['service'] ); ?></select>
		</div>
		<?php $field( 'date', __( 'Date', 'medcentrum-booking' ), 'date', 'required' ); ?>
		<?php $field( 'time', __( 'Time', 'medcentrum-booking' ), 'time', 'required step="300"' ); ?>
		<div>
			<label for="mcb-status"><?php esc_html_e( 'Status', 'medcentrum-booking' ); ?></label>
			<select id="mcb-status" name="mcb[status]">
				<?php foreach ( mcb_statuses() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $b['status'], $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php $field( 'first_name', __( 'First name', 'medcentrum-booking' ), 'text', 'required' ); ?>
		<?php $field( 'last_name', __( 'Last name', 'medcentrum-booking' ), 'text', 'required' ); ?>
		<?php $field( 'email', __( 'E-mail', 'medcentrum-booking' ), 'email' ); ?>
		<?php $field( 'phone', __( 'Phone', 'medcentrum-booking' ), 'tel' ); ?>
		<?php $field( 'birthdate', __( 'Date of birth', 'medcentrum-booking' ), 'date' ); ?>
		<div>
			<label for="mcb-insurance"><?php esc_html_e( 'Health insurer', 'medcentrum-booking' ); ?></label>
			<select id="mcb-insurance" name="mcb[insurance]"><option value="">—</option><?php mcb_select_options( mcb_insurers(), $b['insurance'] ); ?></select>
		</div>
		<div class="mcb-wide">
			<label for="mcb-note"><?php esc_html_e( 'Note from the patient', 'medcentrum-booking' ); ?></label>
			<textarea id="mcb-note" name="mcb[note]" rows="3"><?php echo esc_textarea( $b['note'] ); ?></textarea>
		</div>
		<div class="mcb-wide">
			<label><input type="checkbox" name="mcb_notify" value="1"> <?php esc_html_e( 'E-mail the patient about the new status or time', 'medcentrum-booking' ); ?></label>
		</div>
	</div>
	<?php if ( ! $isnew ) : ?>
		<p class="mcb-meta">
			<?php echo esc_html( 'online' === $b['source'] ? __( 'Booked online', 'medcentrum-booking' ) : __( 'Entered manually', 'medcentrum-booking' ) ); ?>
			· <?php echo esc_html( get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $post ) ); ?>
			<?php if ( $b['locale'] ) : ?>
				· <?php echo esc_html( strtoupper( substr( $b['locale'], 0, 2 ) ) ); ?>
			<?php endif; ?>
		</p>
	<?php endif; ?>
	<?php
}

function mcb_meta_box_input() {
	if ( ! isset( $_POST['mcb'], $_POST['mcb_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mcb_nonce'] ) ), 'mcb_save' ) ) {
		return null;
	}
	$in = (array) wp_unslash( $_POST['mcb'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized below.
	$s  = function ( $key ) use ( $in ) {
		return sanitize_text_field( (string) ( $in[ $key ] ?? '' ) );
	};
	return array(
		'service'    => $s( 'service' ),
		'date'       => mcb_valid_date( $s( 'date' ) ) ? $s( 'date' ) : '',
		'time'       => mcb_sanitize_time( $s( 'time' ), '' ),
		'status'     => isset( mcb_statuses()[ $s( 'status' ) ] ) ? $s( 'status' ) : 'confirmed',
		'first_name' => $s( 'first_name' ),
		'last_name'  => $s( 'last_name' ),
		'email'      => sanitize_email( $s( 'email' ) ),
		'phone'      => $s( 'phone' ),
		'birthdate'  => mcb_valid_date( $s( 'birthdate' ) ) ? $s( 'birthdate' ) : '',
		'insurance'  => $s( 'insurance' ),
		'note'       => sanitize_textarea_field( (string) ( $in['note'] ?? '' ) ),
	);
}

// The title is always the patient's name; set it before the post is written.
add_filter(
	'wp_insert_post_data',
	function ( $data ) {
		if ( MCB_CPT === $data['post_type'] ) {
			$input = mcb_meta_box_input();
			if ( $input ) {
				$data['post_title'] = wp_slash( trim( $input['first_name'] . ' ' . $input['last_name'] ) );
			}
		}
		return $data;
	}
);

add_action(
	'save_post_' . MCB_CPT,
	function ( $id ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $id ) ) {
			return;
		}
		$input = mcb_meta_box_input();
		if ( ! $input ) {
			return;
		}
		$before = mcb_booking_data( $id );
		foreach ( $input as $key => $value ) {
			update_post_meta( $id, '_mcb_' . $key, wp_slash( $value ) );
		}
		update_post_meta( $id, '_mcb_start', $input['date'] . ' ' . $input['time'] );
		if ( '' === $before['source'] ) {
			update_post_meta( $id, '_mcb_source', 'manual' );
		}

		$changed = $before['status'] !== $input['status'] || $before['date'] !== $input['date'] || $before['time'] !== $input['time'];
		if ( $changed && ! empty( $_POST['mcb_notify'] ) && is_email( $input['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- verified in mcb_meta_box_input().
			mcb_send_patient_email( $id );
		}
	}
);
