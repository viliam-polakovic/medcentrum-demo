<?php
defined( 'ABSPATH' ) || exit;

// Polylang limits this to posts in the current language.
$news_posts = get_posts(
	array(
		'numberposts'      => 3,
		'post_status'      => 'publish',
		'suppress_filters' => false,
	)
);
if ( ! $news_posts ) {
	return;
}
$blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : '';
?>
<section class="section section--sand news" id="news">
	<div class="wrap wrap--wide">
		<div class="section__head reveal">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'News', 'medcentrum' ); ?></p>
				<h2 class="section__title"><?php esc_html_e( 'News and events', 'medcentrum' ); ?></h2>
			</div>
			<?php if ( $blog_url ) : ?>
				<div class="section__tools"><a class="btn btn--outline btn--sm" href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'All news', 'medcentrum' ); ?></a></div>
			<?php endif; ?>
		</div>
		<div class="news__grid">
			<?php foreach ( $news_posts as $i => $item ) : ?>
				<article class="news-card reveal">
					<a href="<?php echo esc_url( get_permalink( $item ) ); ?>">
						<span class="news-card__media"><?php echo medcentrum_post_image( $item, $i ); // phpcs:ignore ?></span>
						<time class="news-card__date" datetime="<?php echo esc_attr( get_the_date( 'c', $item ) ); ?>"><?php echo esc_html( get_the_date( medcentrum_date_format(), $item ) ); ?></time>
						<h3 class="news-card__title"><?php echo esc_html( get_the_title( $item ) ); ?></h3>
						<p class="news-card__text"><?php echo esc_html( get_the_excerpt( $item ) ); ?></p>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
