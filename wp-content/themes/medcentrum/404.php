<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<header class="page-hero">
	<div class="wrap">
		<p class="eyebrow"><?php esc_html_e( 'Error 404', 'medcentrum' ); ?></p>
		<h1 class="page-hero__title"><?php esc_html_e( 'Page not found', 'medcentrum' ); ?></h1>
	</div>
</header>
<div class="wrap entry">
	<p><?php esc_html_e( 'The link is probably broken or the page has moved.', 'medcentrum' ); ?></p>
	<p><a class="btn btn--primary" href="<?php echo esc_url( medcentrum_home_url() ); ?>"><?php esc_html_e( 'Back to home page', 'medcentrum' ); ?></a></p>
</div>
<?php
get_footer();
