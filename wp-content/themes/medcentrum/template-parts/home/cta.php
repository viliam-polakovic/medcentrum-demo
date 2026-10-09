<?php
defined( 'ABSPATH' ) || exit;
$contact = medcentrum_contact();
$subject = rawurlencode( __( 'Patient referral', 'medcentrum' ) );
?>
<section class="section cta" id="referrers">
	<div class="wrap wrap--wide cta__grid">
		<a class="cta-card reveal" href="mailto:<?php echo esc_attr( $contact['email'] ); ?>?subject=<?php echo esc_attr( $subject ); ?>">
			<span class="cta-card__top">
				<span class="eyebrow eyebrow--light"><?php esc_html_e( 'For doctors', 'medcentrum' ); ?></span>
				<?php echo medcentrum_icon( 'hospital', 'cta-card__icon' ); // phpcs:ignore ?>
			</span>
			<span class="cta-card__title"><?php echo medcentrum_kses_br( __( 'I am a doctor and want<br>to refer a patient.', 'medcentrum' ) ); ?></span>
			<span class="btn btn--ghost-light btn--sm"><?php esc_html_e( 'Refer a patient', 'medcentrum' ); ?></span>
		</a>
		<a class="cta-card cta-card--alt reveal" href="#booking">
			<span class="cta-card__top">
				<span class="eyebrow eyebrow--light"><?php esc_html_e( 'For patients', 'medcentrum' ); ?></span>
				<?php echo medcentrum_icon( 'calendar', 'cta-card__icon' ); // phpcs:ignore ?>
			</span>
			<span class="cta-card__title"><?php echo medcentrum_kses_br( __( 'I am a patient and want<br>to book an appointment.', 'medcentrum' ) ); ?></span>
			<span class="btn btn--ghost-light btn--sm"><?php esc_html_e( 'Book appointment', 'medcentrum' ); ?></span>
		</a>
	</div>
</section>
