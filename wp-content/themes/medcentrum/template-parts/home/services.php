<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section section--sand services" id="services" data-view="cards">
	<div class="wrap wrap--wide">
		<div class="section__head">
			<div class="reveal">
				<h2 class="section__title"><?php esc_html_e( 'Our offer', 'medcentrum' ); ?></h2>
				<p class="section__intro"><?php esc_html_e( 'Our clinics rely on modern diagnostics, individual treatment and a personal approach. We work closely with specialists so you do not have to go from door to door.', 'medcentrum' ); ?></p>
			</div>
			<div class="section__tools">
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( medcentrum_booking_url() ); ?>"><?php esc_html_e( 'Book appointment', 'medcentrum' ); ?></a>
				<div class="view-toggle" role="group" aria-label="<?php esc_attr_e( 'View', 'medcentrum' ); ?>">
					<button type="button" data-view-set="cards" aria-pressed="true"><?php echo medcentrum_icon( 'grid' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Cards', 'medcentrum' ); ?></span></button>
					<button type="button" data-view-set="list" aria-pressed="false"><?php echo medcentrum_icon( 'list' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'List', 'medcentrum' ); ?></span></button>
				</div>
			</div>
		</div>
	</div>

	<div class="scroller services__cards" data-scroller>
		<div class="scroller__track" data-scroller-track>
			<?php foreach ( medcentrum_services() as $service ) : ?>
				<article class="service-card">
					<a class="service-card__link" href="#booking" data-mcb-service="<?php echo esc_attr( $service['booking'] ); ?>">
						<span class="service-card__media"><img src="<?php echo esc_url( medcentrum_img( $service['image'] ) ); ?>" alt="" width="800" height="600" loading="lazy"></span>
						<h3 class="service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
						<p class="service-card__text"><?php echo esc_html( $service['text'] ); ?></p>
						<span class="service-card__more"><?php esc_html_e( 'Book an appointment', 'medcentrum' ); ?> <?php echo medcentrum_icon( 'arrow-right' ); // phpcs:ignore ?></span>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
		<div class="scroller__controls wrap wrap--wide">
			<div class="scroller__progress"><span data-scroller-bar></span></div>
			<button class="round-btn" type="button" data-scroller-prev aria-label="<?php esc_attr_e( 'Previous', 'medcentrum' ); ?>"><?php echo medcentrum_icon( 'prev' ); // phpcs:ignore ?></button>
			<button class="round-btn" type="button" data-scroller-next aria-label="<?php esc_attr_e( 'Next', 'medcentrum' ); ?>"><?php echo medcentrum_icon( 'next' ); // phpcs:ignore ?></button>
		</div>
	</div>

	<div class="wrap wrap--wide services__list">
		<ul>
			<?php foreach ( medcentrum_services() as $service ) : ?>
				<li>
					<a href="#booking" data-mcb-service="<?php echo esc_attr( $service['booking'] ); ?>">
						<span><?php echo esc_html( $service['title'] ); ?></span>
						<?php echo medcentrum_icon( 'arrow-right' ); // phpcs:ignore ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
