<?php
/**
 * MedCentrum theme setup.
 */

defined( 'ABSPATH' ) || exit;

define( 'MEDCENTRUM_VERSION', '1.1.0' );

require_once get_template_directory() . '/inc/content.php';
require_once get_template_directory() . '/inc/icons.php';
require_once get_template_directory() . '/inc/languages.php';

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'medcentrum', get_template_directory() . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 64,
				'width'       => 240,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		register_nav_menus(
			array(
				'primary' => __( 'Main menu', 'medcentrum' ),
				'footer'  => __( 'Footer – important links', 'medcentrum' ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$uri = get_template_directory_uri();
		wp_enqueue_style( 'medcentrum-fonts', 'https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( 'medcentrum', $uri . '/assets/css/main.css', array(), MEDCENTRUM_VERSION );
		wp_enqueue_script(
			'medcentrum',
			$uri . '/assets/js/main.js',
			array(),
			MEDCENTRUM_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
);

add_filter(
	'wp_resource_hints',
	function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array(
				'href' => 'https://fonts.gstatic.com',
				'crossorigin',
			);
		}
		return $urls;
	},
	10,
	2
);

// Lets CSS hide elements that animate in on scroll before main.js runs.
add_action(
	'wp_head',
	function () {
		echo "<script>document.documentElement.classList.add('js')</script>\n";
	},
	1
);

// Fallback favicon until a Site Icon is set in Appearance → Customize.
add_action(
	'wp_head',
	function () {
		if ( ! has_site_icon() ) {
			printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( medcentrum_img( 'favicon.svg' ) ) );
		}
	}
);

/** URL of an image in assets/img, or the value itself when it is already a URL. */
function medcentrum_img( $file ) {
	return preg_match( '#^https?://#', $file ) ? $file : get_template_directory_uri() . '/assets/img/' . $file;
}

/** Where "Book" buttons lead: the booking page in the current language, else the home page form. */
function medcentrum_booking_url( $service = '' ) {
	$url = function_exists( 'mcb_booking_page_url' ) ? mcb_booking_page_url() : '';
	if ( ! $url ) {
		return medcentrum_section_url( 'booking' );
	}
	return $service ? add_query_arg( 'service', rawurlencode( $service ), $url ) : $url;
}

/** Link to a section of the home page that works from any page. */
function medcentrum_section_url( $id ) {
	return is_front_page() ? '#' . $id : medcentrum_home_url() . '#' . $id;
}

function medcentrum_menu_fallback() {
	$items = array(
		'services' => __( 'Services', 'medcentrum' ),
		'doctors'  => __( 'Doctors', 'medcentrum' ),
		'about'    => __( 'About us', 'medcentrum' ),
		'news'     => __( 'News', 'medcentrum' ),
		'contact'  => __( 'Contact', 'medcentrum' ),
	);
	echo '<ul class="menu">';
	foreach ( $items as $id => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( medcentrum_section_url( $id ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

function medcentrum_brand() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	$name    = get_bloginfo( 'name' );
	$tagline = get_bloginfo( 'description' );
	?>
	<a class="brand" href="<?php echo esc_url( medcentrum_home_url() ); ?>" rel="home">
		<?php echo medcentrum_logo_mark(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<span class="brand__text">
			<span class="brand__name"><?php echo esc_html( $name ); ?></span>
			<?php if ( $tagline ) : ?>
				<span class="brand__tagline"><?php echo esc_html( $tagline ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<?php
}

/** Date format for news, e.g. "9. Oktober 2026" / "October 9, 2026". */
function medcentrum_date_format() {
	/* translators: PHP date format for news dates, see https://www.php.net/manual/datetime.format.php */
	return __( 'F j, Y', 'medcentrum' );
}

/** Thumbnail for a post, falling back to one of the bundled illustrations. */
function medcentrum_post_image( $post = null, $index = 0 ) {
	if ( has_post_thumbnail( $post ) ) {
		return get_the_post_thumbnail( $post, 'medium_large', array( 'loading' => 'lazy' ) );
	}
	return sprintf( '<img src="%s" alt="" loading="lazy" width="800" height="600">', esc_url( medcentrum_img( 'news-' . ( $index % 3 + 1 ) . '.svg' ) ) );
}

/** Strings with a line break (<br>) chosen per language. */
function medcentrum_kses_br( $text ) {
	return wp_kses( $text, array( 'br' => array() ) );
}

add_filter(
	'excerpt_length',
	function () {
		return 22;
	}
);

add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);
