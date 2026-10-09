<?php
/**
 * [medcentrum_rezervacia] – the step-by-step booking form.
 *
 * The markup is rendered here; assets/booking.js adds the calendar and talks
 * to the REST API.
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'medcentrum_rezervacia', 'mcb_render_shortcode' );

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_register_style( 'mcb-booking', MCB_URL . 'assets/booking.css', array(), MCB_VERSION );
		wp_register_script(
			'mcb-booking',
			MCB_URL . 'assets/booking.js',
			array(),
			MCB_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		// Load the stylesheet in <head> where the form is expected, to avoid a flash of unstyled content.
		$post = get_post();
		$load = is_front_page() || ( is_singular() && $post && has_shortcode( $post->post_content, 'medcentrum_rezervacia' ) );
		if ( apply_filters( 'mcb_load_assets', $load ) ) {
			wp_enqueue_style( 'mcb-booking' );
		}
	}
);

function mcb_icon( $name ) {
	$paths = array(
		'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
		'check'    => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'prev'     => '<path d="M15 5l-7 7 7 7"/>',
		'next'     => '<path d="M9 5l7 7-7 7"/>',
		'lock'     => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
	);
	return '<svg class="mcb-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $paths[ $name ] . '</svg>';
}

/** Texts used by booking.js, in the current language. */
function mcb_script_strings() {
	$slots = function ( $n ) {
		/* translators: %d: number of free appointment slots */
		return _n( '%d free slot', '%d free slots', $n, 'medcentrum-booking' );
	};
	$phone = mcb_settings()['phone'];

	return array(
		'pickDay'           => __( 'Select a day in the calendar.', 'medcentrum-booking' ),
		'noSlotsMonth'      => __( 'There are no free slots left this month.', 'medcentrum-booking' ),
		'loadingTimes'      => __( 'Loading available times…', 'medcentrum-booking' ),
		'timesError'        => __( 'The times could not be loaded. Please try again.', 'medcentrum-booking' ),
		'calendarError'     => $phone
			/* translators: %s: phone number */
			? sprintf( __( 'The calendar could not be loaded. Please refresh the page or call us at %s.', 'medcentrum-booking' ), $phone )
			: __( 'The calendar could not be loaded. Please refresh the page.', 'medcentrum-booking' ),
		/* translators: %s: day, e.g. "Wednesday, 14 October" */
		'dayFull'           => __( '%s – fully booked.', 'medcentrum-booking' ),
		'noSlots'           => __( 'no free slots', 'medcentrum-booking' ),
		'morning'           => __( 'Morning', 'medcentrum-booking' ),
		'afternoon'         => __( 'Afternoon', 'medcentrum-booking' ),
		// Plural forms keyed by Intl.PluralRules categories.
		'slots'             => array(
			'one'   => $slots( 1 ),
			'few'   => $slots( 2 ),
			'many'  => $slots( 5 ),
			'other' => $slots( 5 ),
		),
		/* translators: 1: date, 2: time */
		'at'                => __( '%1$s at %2$s', 'medcentrum-booking' ),
		'service'           => __( 'Service', 'medcentrum-booking' ),
		'appointment'       => __( 'Appointment', 'medcentrum-booking' ),
		'patient'           => __( 'Patient', 'medcentrum-booking' ),
		'contact'           => __( 'Contact', 'medcentrum-booking' ),
		'insurer'           => __( 'Health insurer', 'medcentrum-booking' ),
		'networkError'      => __( 'Connection failed. Check your internet connection and try again.', 'medcentrum-booking' ),
		'genericError'      => __( 'The booking could not be completed.', 'medcentrum-booking' ),
		'doneConfirmed'     => __( 'Thank you, your appointment is booked', 'medcentrum-booking' ),
		'donePending'       => __( 'Thank you, we have received your request', 'medcentrum-booking' ),
		/* translators: 1: service, 2: date and time, 3: e-mail address */
		'doneConfirmedText' => __( '%1$s – %2$s. We have sent a confirmation to %3$s.', 'medcentrum-booking' ),
		/* translators: 1: service, 2: date and time, 3: e-mail address */
		'donePendingText'   => __( '%1$s – %2$s. We will confirm the appointment by e-mail at %3$s.', 'medcentrum-booking' ),
		'fields'            => mcb_messages(),
	);
}

function mcb_render_shortcode() {
	wp_enqueue_style( 'mcb-booking' );
	wp_enqueue_script( 'mcb-booking' );

	$s        = mcb_settings();
	$insurers = mcb_insurers();
	$privacy  = get_privacy_policy_url();
	$config   = array(
		// Relative, so the form keeps working when the site is opened on another host (www / non-www).
		'api'     => esc_url_raw( wp_make_link_relative( rest_url( 'medcentrum/v1/' ) ) ),
		'locale'  => determine_locale(),
		'today'   => current_datetime()->format( 'Y-m-d' ),
		'horizon' => (int) $s['horizon'],
		'i18n'    => mcb_script_strings(),
	);
	static $instance = 0;
	$uid = 'mcb-' . ( ++$instance );
	$req = ' <span aria-hidden="true">*</span>';

	ob_start();
	?>
	<div class="mcb" id="<?php echo esc_attr( $uid ); ?>" data-mcb="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
		<div class="mcb__header">
			<span class="mcb__badge"><?php echo mcb_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div>
				<span class="mcb__kicker"><?php esc_html_e( 'Book online', 'medcentrum-booking' ); ?></span>
				<strong class="mcb__clinic"><?php echo esc_html( mcb_clinic_name() ); ?></strong>
			</div>
		</div>

		<form class="mcb__form" novalidate>
			<section class="mcb-step is-active" data-step="service">
				<header class="mcb-step__head">
					<span class="mcb-step__num">1</span>
					<h3 class="mcb-step__title"><?php esc_html_e( 'Service', 'medcentrum-booking' ); ?></h3>
					<span class="mcb-step__summary" data-summary></span>
					<button type="button" class="mcb-step__edit" data-edit hidden><?php esc_html_e( 'Change', 'medcentrum-booking' ); ?></button>
				</header>
				<div class="mcb-step__body">
					<fieldset class="mcb-services">
						<legend class="mcb-sr"><?php esc_html_e( 'Please choose a service.', 'medcentrum-booking' ); ?></legend>
						<?php foreach ( mcb_services() as $service ) : ?>
							<label class="mcb-choice">
								<input type="radio" name="service" value="<?php echo esc_attr( $service['value'] ); ?>">
								<span><?php echo esc_html( mcb_item_label( $service ) ); ?></span>
							</label>
						<?php endforeach; ?>
					</fieldset>
				</div>
			</section>

			<section class="mcb-step is-locked" data-step="slot">
				<header class="mcb-step__head">
					<span class="mcb-step__num">2</span>
					<h3 class="mcb-step__title"><?php esc_html_e( 'Date and time', 'medcentrum-booking' ); ?></h3>
					<span class="mcb-step__summary" data-summary></span>
					<button type="button" class="mcb-step__edit" data-edit hidden><?php esc_html_e( 'Change', 'medcentrum-booking' ); ?></button>
				</header>
				<div class="mcb-step__body">
					<p class="mcb-alert mcb-alert--slot" role="alert" hidden></p>
					<div class="mcb-picker">
						<div class="mcb-cal">
							<div class="mcb-cal__bar">
								<button type="button" class="mcb-cal__nav" data-month="-1" aria-label="<?php esc_attr_e( 'Previous month', 'medcentrum-booking' ); ?>"><?php echo mcb_icon( 'prev' ); // phpcs:ignore ?></button>
								<strong class="mcb-cal__month" aria-live="polite"></strong>
								<button type="button" class="mcb-cal__nav" data-month="1" aria-label="<?php esc_attr_e( 'Next month', 'medcentrum-booking' ); ?>"><?php echo mcb_icon( 'next' ); // phpcs:ignore ?></button>
							</div>
							<div class="mcb-cal__dow" aria-hidden="true"></div>
							<div class="mcb-cal__grid"></div>
							<p class="mcb-cal__legend"><span class="mcb-dot"></span> <?php esc_html_e( 'free slots', 'medcentrum-booking' ); ?></p>
						</div>
						<div class="mcb-times">
							<p class="mcb-times__label" aria-live="polite"><?php esc_html_e( 'Select a day in the calendar.', 'medcentrum-booking' ); ?></p>
							<div class="mcb-times__list"></div>
						</div>
					</div>
				</div>
			</section>

			<section class="mcb-step is-locked" data-step="details">
				<header class="mcb-step__head">
					<span class="mcb-step__num">3</span>
					<h3 class="mcb-step__title"><?php esc_html_e( 'Your details', 'medcentrum-booking' ); ?></h3>
					<span class="mcb-step__summary" data-summary></span>
					<button type="button" class="mcb-step__edit" data-edit hidden><?php esc_html_e( 'Change', 'medcentrum-booking' ); ?></button>
				</header>
				<div class="mcb-step__body">
					<div class="mcb-fields">
						<div class="mcb-field">
							<label for="<?php echo esc_attr( $uid ); ?>-first"><?php esc_html_e( 'First name', 'medcentrum-booking' ); ?><?php echo $req; // phpcs:ignore ?></label>
							<input id="<?php echo esc_attr( $uid ); ?>-first" name="first_name" autocomplete="given-name" required>
						</div>
						<div class="mcb-field">
							<label for="<?php echo esc_attr( $uid ); ?>-last"><?php esc_html_e( 'Last name', 'medcentrum-booking' ); ?><?php echo $req; // phpcs:ignore ?></label>
							<input id="<?php echo esc_attr( $uid ); ?>-last" name="last_name" autocomplete="family-name" required>
						</div>
						<div class="mcb-field">
							<label for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'E-mail', 'medcentrum-booking' ); ?><?php echo $req; // phpcs:ignore ?></label>
							<input id="<?php echo esc_attr( $uid ); ?>-email" name="email" type="email" autocomplete="email" required>
						</div>
						<div class="mcb-field">
							<label for="<?php echo esc_attr( $uid ); ?>-phone"><?php esc_html_e( 'Phone', 'medcentrum-booking' ); ?><?php echo $req; // phpcs:ignore ?></label>
							<input id="<?php echo esc_attr( $uid ); ?>-phone" name="phone" type="tel" autocomplete="tel" required>
						</div>
						<div class="mcb-field">
							<label for="<?php echo esc_attr( $uid ); ?>-birth"><?php esc_html_e( 'Date of birth', 'medcentrum-booking' ); ?></label>
							<input id="<?php echo esc_attr( $uid ); ?>-birth" name="birthdate" type="date" autocomplete="bday">
						</div>
						<?php if ( $insurers ) : ?>
							<div class="mcb-field">
								<label for="<?php echo esc_attr( $uid ); ?>-ins"><?php esc_html_e( 'Health insurer', 'medcentrum-booking' ); ?><?php echo $req; // phpcs:ignore ?></label>
								<select id="<?php echo esc_attr( $uid ); ?>-ins" name="insurance" required>
									<option value=""><?php esc_html_e( 'Choose…', 'medcentrum-booking' ); ?></option>
									<?php foreach ( $insurers as $insurer ) : ?>
										<option value="<?php echo esc_attr( $insurer['value'] ); ?>"><?php echo esc_html( mcb_item_label( $insurer ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>
						<div class="mcb-field mcb-field--wide">
							<label for="<?php echo esc_attr( $uid ); ?>-note"><?php esc_html_e( 'Reason for the visit / note', 'medcentrum-booking' ); ?></label>
							<textarea id="<?php echo esc_attr( $uid ); ?>-note" name="note" rows="3" maxlength="1000"></textarea>
						</div>
						<div class="mcb-hp" aria-hidden="true">
							<label>Website <input name="website" tabindex="-1" autocomplete="off"></label>
						</div>
						<div class="mcb-field mcb-field--wide mcb-consent">
							<label>
								<input type="checkbox" name="consent" value="1" required>
								<span>
									<?php esc_html_e( 'I agree to the processing of my personal data for booking and providing medical care.', 'medcentrum-booking' ); ?>
									<?php if ( $privacy ) : ?>
										<a href="<?php echo esc_url( $privacy ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Privacy policy', 'medcentrum-booking' ); ?></a>
									<?php endif; ?>
								</span>
							</label>
						</div>
					</div>
					<button type="button" class="mcb-btn" data-next><?php esc_html_e( 'Continue', 'medcentrum-booking' ); ?></button>
				</div>
			</section>

			<section class="mcb-step is-locked" data-step="confirm">
				<header class="mcb-step__head">
					<span class="mcb-step__num">4</span>
					<h3 class="mcb-step__title"><?php esc_html_e( 'Confirmation', 'medcentrum-booking' ); ?></h3>
				</header>
				<div class="mcb-step__body">
					<dl class="mcb-review"></dl>
					<p class="mcb-alert mcb-alert--confirm" role="alert" hidden></p>
					<button type="submit" class="mcb-btn mcb-btn--submit"><?php esc_html_e( 'Book appointment', 'medcentrum-booking' ); ?></button>
				</div>
			</section>
		</form>

		<div class="mcb-done" tabindex="-1" hidden>
			<span class="mcb-done__icon"><?php echo mcb_icon( 'check' ); // phpcs:ignore ?></span>
			<h3 class="mcb-done__title"></h3>
			<p class="mcb-done__text"></p>
			<button type="button" class="mcb-btn mcb-btn--ghost" data-restart><?php esc_html_e( 'Book another appointment', 'medcentrum-booking' ); ?></button>
		</div>

		<p class="mcb__foot"><?php echo mcb_icon( 'lock' ); // phpcs:ignore ?> <?php esc_html_e( 'Your data is sent encrypted and only the clinic staff can see it.', 'medcentrum-booking' ); ?></p>
	</div>
	<?php
	return ob_get_clean();
}
