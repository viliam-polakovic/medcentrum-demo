<?php
/**
 * Site header.
 */

defined( 'ABSPATH' ) || exit;

$contact = medcentrum_contact();
$tel     = preg_replace( '/[^0-9+]/', '', $contact['phone'] );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'medcentrum' ); ?></a>

<div class="topbar">
	<div class="topbar__inner wrap wrap--wide">
		<div class="topbar__contact">
			<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo medcentrum_icon( 'phone' ); // phpcs:ignore ?><?php echo esc_html( $contact['phone'] ); ?></a>
			<a class="hide-mobile" href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo medcentrum_icon( 'mail' ); // phpcs:ignore ?><?php echo esc_html( $contact['email'] ); ?></a>
		</div>
		<?php medcentrum_language_switcher(); ?>
	</div>
</div>

<header class="site-header" data-header>
	<div class="site-header__inner wrap wrap--wide">
		<?php medcentrum_brand(); ?>

		<nav class="main-nav" aria-label="<?php esc_attr_e( 'Main menu', 'medcentrum' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'medcentrum_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<a class="btn btn--primary btn--sm" href="<?php echo esc_url( medcentrum_booking_url() ); ?>">
				<span><?php esc_html_e( 'Book appointment', 'medcentrum' ); ?></span><?php echo medcentrum_icon( 'calendar' ); // phpcs:ignore ?>
			</a>
			<a class="btn btn--outline btn--sm hide-mobile" href="<?php echo esc_url( medcentrum_section_url( 'referrers' ) ); ?>">
				<span><?php esc_html_e( 'For doctors', 'medcentrum' ); ?></span><?php echo medcentrum_icon( 'hospital' ); // phpcs:ignore ?>
			</a>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="drawer" data-drawer-open>
				<?php echo medcentrum_icon( 'menu' ); // phpcs:ignore ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'medcentrum' ); ?></span>
			</button>
		</div>
	</div>
</header>

<div class="drawer" id="drawer" hidden data-drawer>
	<div class="drawer__backdrop" data-drawer-close></div>
	<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'medcentrum' ); ?>">
		<div class="drawer__top">
			<?php medcentrum_language_switcher( 'lang-switch--drawer' ); ?>
			<button class="menu-toggle" type="button" data-drawer-close>
				<?php echo medcentrum_icon( 'close' ); // phpcs:ignore ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'medcentrum' ); ?></span>
			</button>
		</div>
		<nav class="drawer__nav" aria-label="<?php esc_attr_e( 'Menu', 'medcentrum' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'medcentrum_menu_fallback',
				)
			);
			?>
		</nav>
		<div class="drawer__group">
			<span class="eyebrow"><?php esc_html_e( 'Services', 'medcentrum' ); ?></span>
			<ul>
				<?php foreach ( medcentrum_services() as $service ) : ?>
					<li><a href="<?php echo esc_url( medcentrum_booking_url( $service['booking'] ) ); ?>"><?php echo esc_html( $service['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="drawer__contact">
			<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo medcentrum_icon( 'phone' ); // phpcs:ignore ?><?php echo esc_html( $contact['phone'] ); ?></a>
			<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo medcentrum_icon( 'mail' ); // phpcs:ignore ?><?php echo esc_html( $contact['email'] ); ?></a>
		</div>
		<a class="btn btn--primary btn--block" href="<?php echo esc_url( medcentrum_booking_url() ); ?>"><span><?php esc_html_e( 'Book online', 'medcentrum' ); ?></span><?php echo medcentrum_icon( 'calendar' ); // phpcs:ignore ?></a>
	</div>
</div>

<main id="content" class="site-main">
