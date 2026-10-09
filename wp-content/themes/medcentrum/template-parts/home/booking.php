<?php
defined( 'ABSPATH' ) || exit;
$contact = medcentrum_contact();
?>
<section class="section section--tint booking" id="booking">
	<div class="wrap wrap--wide booking__grid">
		<div class="booking__intro reveal">
			<p class="eyebrow"><?php esc_html_e( 'Online booking', 'medcentrum' ); ?></p>
			<h2 class="section__title"><?php esc_html_e( 'Book your appointment for a specific day and time', 'medcentrum' ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Choose a service, a free slot in the calendar and fill in your contact details. You will receive a confirmation by e-mail.', 'medcentrum' ); ?></p>
			<ol class="booking__steps">
				<li><span>1</span><?php esc_html_e( 'Choose a service', 'medcentrum' ); ?></li>
				<li><span>2</span><?php esc_html_e( 'Pick a day and time', 'medcentrum' ); ?></li>
				<li><span>3</span><?php esc_html_e( 'Fill in your details', 'medcentrum' ); ?></li>
				<li><span>4</span><?php esc_html_e( 'Confirmation by e-mail', 'medcentrum' ); ?></li>
			</ol>
			<p class="booking__phone">
				<?php esc_html_e( 'Prefer to call?', 'medcentrum' ); ?><br>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact['phone'] ) ); ?>"><?php echo medcentrum_icon( 'phone' ); // phpcs:ignore ?><?php echo esc_html( $contact['phone'] ); ?></a>
			</p>
		</div>
		<div class="booking__widget">
			<?php if ( shortcode_exists( 'medcentrum_rezervacia' ) ) : ?>
				<?php echo do_shortcode( '[medcentrum_rezervacia]' ); ?>
			<?php else : ?>
				<p class="notice">
					<?php
					/* translators: %s: plugin name */
					printf( esc_html__( 'The booking calendar appears once the %s plugin is activated.', 'medcentrum' ), '<strong>MedCentrum – Online Booking</strong>' );
					?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
