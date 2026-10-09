<?php
/**
 * Languages (DE / EN / SK).
 *
 * Polylang manages the languages, URLs (/, /en/, /sk/) and translated
 * pages and posts; the theme's own texts are translated in languages/*.po.
 */

defined( 'ABSPATH' ) || exit;

/** Home URL in the current language. */
function medcentrum_home_url() {
	return function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
}

/** Languages for the switcher, in Polylang's order. */
function medcentrum_languages() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}
	$raw = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
		)
	);
	$languages = array();
	foreach ( (array) $raw as $lang ) {
		$languages[] = array(
			'code'    => strtoupper( $lang['slug'] ),
			'name'    => $lang['name'],
			'url'     => $lang['url'],
			'locale'  => str_replace( '_', '-', $lang['locale'] ),
			'current' => ! empty( $lang['current_lang'] ),
		);
	}
	return $languages;
}

function medcentrum_language_switcher( $class = '' ) {
	$languages = medcentrum_languages();
	if ( count( $languages ) < 2 ) {
		return;
	}
	?>
	<nav class="lang-switch <?php echo esc_attr( $class ); ?>" aria-label="<?php esc_attr_e( 'Language', 'medcentrum' ); ?>">
		<?php foreach ( $languages as $lang ) : ?>
			<a href="<?php echo esc_url( $lang['url'] ); ?>" lang="<?php echo esc_attr( $lang['locale'] ); ?>" hreflang="<?php echo esc_attr( $lang['locale'] ); ?>" title="<?php echo esc_attr( $lang['name'] ); ?>"<?php echo $lang['current'] ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $lang['code'] ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php
}

add_action(
	'admin_notices',
	function () {
		if ( function_exists( 'pll_the_languages' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
			esc_html__( 'The language switcher (DE / EN / SK) needs the free Polylang plugin.', 'medcentrum' ),
			esc_url( admin_url( 'plugin-install.php?s=polylang&tab=search&type=term' ) ),
			esc_html__( 'Install Polylang', 'medcentrum' )
		);
	}
);
