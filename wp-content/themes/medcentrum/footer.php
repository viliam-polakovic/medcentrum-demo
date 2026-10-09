<?php
/**
 * Site footer.
 */

defined( 'ABSPATH' ) || exit;

$contact = medcentrum_contact();
$privacy = get_privacy_policy_url();
?>
</main>

<footer class="site-footer">
	<div class="wrap wrap--wide">
		<a class="to-top" href="#content"><?php esc_html_e( 'Back to top', 'medcentrum' ); ?> <?php echo medcentrum_icon( 'arrow-up' ); // phpcs:ignore ?></a>

		<div class="site-footer__grid">
			<div class="site-footer__col">
				<h2 class="site-footer__title"><?php esc_html_e( 'Contact', 'medcentrum' ); ?></h2>
				<address>
					<strong><?php echo esc_html( $contact['company'] ); ?></strong><br>
					<?php echo esc_html( $contact['street'] ); ?><br>
					<?php echo esc_html( $contact['city'] ); ?>
				</address>
				<p>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact['phone'] ) ); ?>"><?php echo esc_html( $contact['phone'] ); ?></a><br>
					<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a>
				</p>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__title"><?php esc_html_e( 'Important links', 'medcentrum' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => 'medcentrum_menu_fallback',
					)
				);
				?>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__title"><?php esc_html_e( 'Useful information', 'medcentrum' ); ?></h2>
				<ul class="menu">
					<li><a href="<?php echo esc_url( medcentrum_booking_url() ); ?>"><?php esc_html_e( 'Online booking', 'medcentrum' ); ?></a></li>
					<li><a href="<?php echo esc_url( medcentrum_section_url( 'contact' ) ); ?>"><?php esc_html_e( 'Opening hours', 'medcentrum' ); ?></a></li>
					<li><a href="<?php echo esc_url( medcentrum_section_url( 'referrers' ) ); ?>"><?php esc_html_e( 'For doctors', 'medcentrum' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Price list', 'medcentrum' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Partner insurers', 'medcentrum' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'FAQ', 'medcentrum' ); ?></a></li>
				</ul>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__title"><?php esc_html_e( 'Services', 'medcentrum' ); ?></h2>
				<ul class="menu">
					<?php foreach ( medcentrum_services() as $service ) : ?>
						<li><a href="<?php echo esc_url( medcentrum_booking_url( $service['booking'] ) ); ?>"><?php echo esc_html( $service['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="site-footer__bottom">
			<span>
				<?php
				/* translators: 1: year, 2: site name */
				printf( esc_html__( '© %1$s %2$s – All rights reserved.', 'medcentrum' ), esc_html( wp_date( 'Y' ) ), esc_html( get_bloginfo( 'name' ) ) );
				?>
			</span>
			<span class="site-footer__legal">
				<a href="#"><?php esc_html_e( 'Legal notice', 'medcentrum' ); ?></a>
				<?php if ( $privacy ) : ?>
					<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Privacy policy', 'medcentrum' ); ?></a>
				<?php endif; ?>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
