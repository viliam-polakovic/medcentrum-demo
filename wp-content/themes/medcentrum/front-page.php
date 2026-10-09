<?php
/**
 * Home page. Each section is a template part in template-parts/home/.
 */

defined( 'ABSPATH' ) || exit;

get_header();

foreach ( array( 'hero', 'intro', 'gallery', 'services', 'cta', 'team', 'booking', 'locations', 'news' ) as $section ) {
	get_template_part( 'template-parts/home/' . $section );
}

get_footer();
