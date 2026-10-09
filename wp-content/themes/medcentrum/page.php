<?php
/**
 * Single page (e.g. the booking page, privacy policy).
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<header class="page-hero">
		<div class="wrap">
			<p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
		</div>
	</header>
	<div class="wrap entry">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
