<?php
/**
 * Inline SVG icons (24×24, stroke based).
 */

defined( 'ABSPATH' ) || exit;

function medcentrum_icon( $name, $class = '' ) {
	static $paths = array(
		'calendar'    => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4M7.5 14h2M11 14h2M14.5 14h2M7.5 17.5h2M11 17.5h2"/>',
		'hospital'    => '<path d="M4 21V8l8-5 8 5v13"/><path d="M2 21h20M10 21v-5h4v5M12 7.5v5M9.5 10h5"/>',
		'arrow-right' => '<path d="M4 12h16M14 6l6 6-6 6"/>',
		'arrow-up'    => '<path d="M12 20V4M6 10l6-6 6 6"/>',
		'prev'        => '<path d="M15 5l-7 7 7 7"/>',
		'next'        => '<path d="M9 5l7 7-7 7"/>',
		'phone'       => '<path d="M5 3h3.5l2 5-2.5 1.5a11 11 0 0 0 6.5 6.5L16 13.5l5 2V19a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/>',
		'mail'        => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'pin'         => '<path d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
		'clock'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'doctors'     => '<circle cx="8.5" cy="7" r="3.5"/><circle cx="16.5" cy="8" r="3"/><path d="M2 20c0-3.6 2.9-6.5 6.5-6.5S15 16.4 15 20"/><path d="M15.5 13.6A5.5 5.5 0 0 1 22 19"/><path d="M8.5 16v3M7 17.5h3"/>',
		'plus'        => '<path d="M9 3h6v6h6v6h-6v6H9v-6H3V9h6z"/>',
		'menu'        => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'       => '<path d="M5 5l14 14M19 5L5 19"/>',
		'grid'        => '<rect x="3" y="3" width="7.5" height="7.5" rx="1"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1"/>',
		'list'        => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
		'check'       => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'shield'      => '<path d="M12 3l8 3v6c0 4.5-3.4 7.8-8 9-4.6-1.2-8-4.5-8-9V6z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon %s" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">%s</svg>',
		esc_attr( trim( 'icon--' . $name . ' ' . $class ) ),
		$paths[ $name ]
	);
}

/** Round logo mark with a pulse line. */
function medcentrum_logo_mark( $class = 'brand__mark' ) {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><circle cx="24" cy="24" r="22" fill="currentColor"/><path d="M7 26h10l3.5-8 5 15 4.5-11 2.5 4H41" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}
