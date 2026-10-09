<?php defined( 'ABSPATH' ) || exit; ?>
<section class="gallery" aria-label="<?php esc_attr_e( 'Photo gallery', 'medcentrum' ); ?>">
	<div class="scroller" data-scroller>
		<div class="scroller__track gallery__track" data-scroller-track tabindex="0">
			<?php foreach ( medcentrum_gallery() as $image ) : ?>
				<figure class="gallery__item">
					<img src="<?php echo esc_url( medcentrum_img( $image['image'] ) ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="900" height="620" loading="lazy">
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="wrap wrap--wide"><div class="scroller__progress"><span data-scroller-bar></span></div></div>
	</div>
</section>
