<?php defined( 'ABSPATH' ) || exit; ?>
<section class="hero">
	<div class="hero__frame">
		<img class="hero__image" src="<?php echo esc_url( medcentrum_img( 'hero.svg' ) ); ?>" alt="" width="1600" height="900" fetchpriority="high">
		<div class="hero__content">
			<p class="eyebrow eyebrow--light reveal"><?php esc_html_e( 'What we offer', 'medcentrum' ); ?></p>
			<h1 class="hero__title reveal"><?php echo medcentrum_kses_br( __( 'Modern medicine<br>with a human touch', 'medcentrum' ) ); ?></h1>
			<div class="hero__actions reveal">
				<a class="btn btn--light" href="#booking"><span><?php esc_html_e( 'Book online', 'medcentrum' ); ?></span><?php echo medcentrum_icon( 'calendar' ); // phpcs:ignore ?></a>
				<a class="btn btn--ghost-light" href="#services"><?php esc_html_e( 'Our services', 'medcentrum' ); ?></a>
			</div>
		</div>
	</div>
</section>
