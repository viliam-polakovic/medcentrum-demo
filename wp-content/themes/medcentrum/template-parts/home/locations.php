<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section locations" id="contact">
	<div class="wrap wrap--wide">
		<div class="section__head reveal">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Our locations', 'medcentrum' ); ?></p>
				<h2 class="section__title"><?php esc_html_e( 'Always close by.', 'medcentrum' ); ?></h2>
				<p class="section__intro"><?php esc_html_e( 'Find the clinic closest to you. You can book at every location online or by phone.', 'medcentrum' ); ?></p>
			</div>
		</div>

		<div class="locations__grid">
			<ol class="locations__list">
				<?php foreach ( medcentrum_locations() as $i => $location ) : ?>
					<li class="location reveal">
						<span class="location__num"><?php echo esc_html( $i + 1 ); ?></span>
						<div>
							<h3 class="location__name"><?php echo esc_html( $location['name'] ); ?></h3>
							<p class="location__row"><?php echo medcentrum_icon( 'pin' ); // phpcs:ignore ?><?php echo esc_html( $location['address'] ); ?></p>
							<p class="location__row"><?php echo medcentrum_icon( 'clock' ); // phpcs:ignore ?><?php echo esc_html( $location['hours'] ); ?></p>
							<p class="location__row"><?php echo medcentrum_icon( 'phone' ); // phpcs:ignore ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $location['phone'] ) ); ?>"><?php echo esc_html( $location['phone'] ); ?></a></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
			<div class="locations__map reveal">
				<?php /* translators: %s: site name */ ?>
				<img src="<?php echo esc_url( medcentrum_img( 'map.svg' ) ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Map of %s locations', 'medcentrum' ), get_bloginfo( 'name' ) ) ); ?>" width="1200" height="800" loading="lazy">
			</div>
		</div>
	</div>
</section>
