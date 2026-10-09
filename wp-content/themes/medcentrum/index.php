<?php
/**
 * Posts (news) – archive and single post.
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_singular() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="page-hero">
				<div class="wrap">
					<time class="eyebrow" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( medcentrum_date_format() ) ); ?></time>
					<h1 class="page-hero__title"><?php the_title(); ?></h1>
				</div>
			</header>
			<div class="wrap entry">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry__image"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
else :
	?>
	<header class="page-hero">
		<div class="wrap">
			<p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<h1 class="page-hero__title"><?php echo is_home() ? esc_html__( 'News', 'medcentrum' ) : wp_kses_post( get_the_archive_title() ); ?></h1>
		</div>
	</header>
	<div class="wrap wrap--wide section section--flush">
		<?php if ( have_posts() ) : ?>
			<div class="news__grid">
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					?>
					<article class="news-card">
						<a href="<?php the_permalink(); ?>">
							<span class="news-card__media"><?php echo medcentrum_post_image( null, $i++ ); // phpcs:ignore ?></span>
							<time class="news-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( medcentrum_date_format() ) ); ?></time>
							<h2 class="news-card__title"><?php the_title(); ?></h2>
							<p class="news-card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="pagination"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing here yet.', 'medcentrum' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
endif;

get_footer();
