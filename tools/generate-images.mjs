// Generates the placeholder illustrations used by the MedCentrum theme.
// Run: node tools/generate-images.mjs
// Output: wp-content/themes/medcentrum/assets/img/*.svg
//
// Replace them with real photos later – keep the same file names, or upload
// photos in WordPress and change the paths in inc/content.php.

import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const OUT = join(dirname(fileURLToPath(import.meta.url)), '../wp-content/themes/medcentrum/assets/img');
mkdirSync(OUT, { recursive: true });

const C = {
	ink: '#0F3A36',
	primary: '#2C5E9E',
	primaryDark: '#1B4473',
	sky: '#DCE8F5',
	sky2: '#B7CFEA',
	sand: '#F4EFE6',
	sand2: '#E9DDC9',
	mint: '#D5EBE3',
	mint2: '#9FCFBF',
	teal: '#2E7D74',
	coral: '#EBB59B',
	white: '#FFFFFF',
};

// Deterministic pseudo-random numbers so the output is stable between runs.
function rng(seed) {
	let s = seed;
	return () => ((s = (s * 16807) % 2147483647) - 1) / 2147483646;
}

/* ----------------------------------------------------------------- icons */
// Line icons drawn in a 200×200 box.
const ICONS = {
	stethoscope: `
		<path d="M62 34v46a38 38 0 0 0 76 0V34"/>
		<path d="M52 34h20M128 34h20"/>
		<path d="M100 118v18a30 30 0 0 0 60 0v-22"/>
		<circle cx="160" cy="100" r="14"/>`,
	brain: `
		<path d="M98 44C86 32 62 34 56 52C40 54 32 70 38 84C26 94 28 114 42 122C40 140 56 154 74 150C82 164 98 162 98 150Z"/>
		<path d="M102 44C114 32 138 34 144 52C160 54 168 70 162 84C174 94 172 114 158 122C160 140 144 154 126 150C118 164 102 162 102 150Z"/>
		<path d="M56 52C62 62 74 64 80 58M38 84C50 84 58 92 58 102M42 122C54 118 64 124 68 132M80 80C88 86 90 98 84 108"/>
		<path d="M144 52C138 62 126 64 120 58M162 84C150 84 142 92 142 102M158 122C146 118 136 124 132 132M120 80C112 86 110 98 116 108"/>`,
	heart: `
		<path d="M100 164C46 124 30 96 38 72c8-24 40-34 62-6 22-28 54-18 62 6 8 24-8 52-62 92z" fill="currentColor" fill-opacity=".14"/>
		<path d="M22 108h42l10-22 14 44 12-34 8 12h70"/>`,
	bone: `
		<g transform="rotate(-35 100 100)">
			<path d="M62 90H138A14 14 0 1 1 158 100A14 14 0 1 1 138 110H62A14 14 0 1 1 42 100A14 14 0 1 1 62 90Z"/>
		</g>
		<path d="M150 44l8-8M160 58l12-4M136 36l2-12" stroke-opacity=".6"/>`,
	spine: [0, 1, 2, 3, 4]
		.map((i) => {
			const y = 32 + i * 30;
			const x = 70 + Math.round(Math.sin(i * 0.9) * 10);
			return `<rect x="${x}" y="${y}" width="60" height="22" rx="9"/><path d="M${x} ${y + 11}h-14M${x + 60} ${y + 11}h14"/>`;
		})
		.join(''),
	monitor: `
		<rect x="28" y="36" width="144" height="102" rx="12"/>
		<path d="M84 162h32M100 138v24"/>
		<path d="M100 52L68 116A72 72 0 0 0 132 116Z" fill="currentColor" fill-opacity=".14"/>
		<path d="M92 92c6 5 12 5 18 0"/>`,
	ecg: `
		<rect x="28" y="36" width="144" height="102" rx="12"/>
		<path d="M84 162h32M100 138v24"/>
		<path d="M42 92h34l8-18 12 36 10-30 8 12h44"/>`,
	shield: `
		<path d="M100 30L160 52V100C160 140 132 162 100 175C68 162 40 140 40 100V52Z" fill="currentColor" fill-opacity=".14"/>
		<path d="M72 102l20 20 38-42"/>`,
	flask: `
		<path d="M82 36h36M90 36v44L56 146a10 10 0 0 0 9 14h70a10 10 0 0 0 9-14L110 80V36"/>
		<path d="M67 124h66"/>
		<circle cx="88" cy="142" r="5"/><circle cx="114" cy="136" r="4"/>`,
	calendar: `
		<rect x="36" y="46" width="128" height="116" rx="16"/>
		<path d="M36 84h128M72 32v28M128 32v28"/>
		<path d="M64 110h10M95 110h10M126 110h10M64 136h10M95 136h10"/>
		<circle cx="131" cy="136" r="15" fill="currentColor" fill-opacity=".14"/>
		<path d="M124 136l5 5 9-10"/>`,
	cross: `<path d="M80 40h40v40h40v40h-40v40H80v-40H40V80h40Z" fill="currentColor" fill-opacity=".14"/>`,
	pulse: `<path d="M20 100h46l12-30 18 62 16-46 10 14h58"/>`,
};

/* -------------------------------------------------------------- building blocks */

function svg(w, h, body, defs = '') {
	return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${w} ${h}" width="${w}" height="${h}" preserveAspectRatio="xMidYMid slice"><defs>${defs}</defs>${body}</svg>\n`;
}

function blobs(w, h, colors, seed, blur) {
	const r = rng(seed);
	const circles = colors
		.map((c, i) => {
			const cx = Math.round(w * (0.1 + r() * 0.8));
			const cy = Math.round(h * (0.1 + r() * 0.8));
			const rad = Math.round(Math.min(w, h) * (0.28 + r() * 0.2));
			return `<circle cx="${cx}" cy="${cy}" r="${rad}" fill="${c}" opacity="${(0.55 + (i % 2) * 0.25).toFixed(2)}"/>`;
		})
		.join('');
	return `<g filter="url(#blur)">${circles}</g>`;
}

function commonDefs(from, to, blur, dot) {
	return `
		<linearGradient id="bg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="${from}"/><stop offset="1" stop-color="${to}"/></linearGradient>
		<filter id="blur" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="${blur}"/></filter>
		<pattern id="dots" width="22" height="22" patternUnits="userSpaceOnUse"><circle cx="3" cy="3" r="1.8" fill="${dot}"/></pattern>
		<linearGradient id="glass" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".75"/><stop offset="1" stop-color="#fff" stop-opacity=".35"/></linearGradient>
		<linearGradient id="glassDark" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".22"/><stop offset="1" stop-color="#fff" stop-opacity=".06"/></linearGradient>`;
}

function plus(x, y, s, color, opacity = 0.5) {
	return `<path d="M${x - s} ${y}h${s * 2}M${x} ${y - s}v${s * 2}" stroke="${color}" stroke-width="3" stroke-linecap="round" opacity="${opacity}"/>`;
}

function icon(name, x, y, scale, color, width = 6) {
	return `<g transform="translate(${x} ${y}) scale(${scale})" fill="none" stroke="${color}" color="${color}" stroke-width="${width / scale * 1.6}" stroke-linecap="round" stroke-linejoin="round">${ICONS[name]}</g>`;
}

/**
 * A card illustration: soft gradient, blurred colour blobs, a dot grid and
 * a large line icon on a frosted panel.
 */
function card({ w = 800, h = 600, from, to, blobColors, icon: name, stroke, seed = 1, dark = false, layout = 'center' }) {
	const dot = dark ? 'rgba(255,255,255,.28)' : 'rgba(15,58,54,.16)';
	const r = rng(seed * 7);
	const panel = Math.round(Math.min(w, h) * 0.52);
	const px = layout === 'right' ? Math.round(w * 0.58 - panel / 2) : Math.round(w / 2 - panel / 2);
	const py = Math.round(h / 2 - panel / 2);
	const ringX = Math.round(w * (0.2 + r() * 0.6));
	const ringY = Math.round(h * (0.2 + r() * 0.6));
	const ringColor = dark ? '#fff' : C.primary;

	const body = `
		<rect width="${w}" height="${h}" fill="url(#bg)"/>
		${blobs(w, h, blobColors, seed, Math.round(Math.min(w, h) * 0.12))}
		<rect x="${Math.round(w * 0.55)}" y="0" width="${Math.round(w * 0.45)}" height="${Math.round(h * 0.45)}" fill="url(#dots)"/>
		<rect x="0" y="${Math.round(h * 0.62)}" width="${Math.round(w * 0.32)}" height="${Math.round(h * 0.38)}" fill="url(#dots)"/>
		<circle cx="${ringX}" cy="${ringY}" r="${Math.round(panel * 0.95)}" fill="none" stroke="${ringColor}" stroke-opacity=".12" stroke-width="2"/>
		<circle cx="${ringX}" cy="${ringY}" r="${Math.round(panel * 0.7)}" fill="none" stroke="${ringColor}" stroke-opacity=".08" stroke-width="2"/>
		<rect x="${px + 18}" y="${py + 26}" width="${panel}" height="${panel}" rx="${Math.round(panel * 0.16)}" fill="${dark ? '#000' : C.ink}" opacity="${dark ? 0.18 : 0.08}" filter="url(#blur)"/>
		<rect x="${px}" y="${py}" width="${panel}" height="${panel}" rx="${Math.round(panel * 0.16)}" fill="url(#${dark ? 'glassDark' : 'glass'})" stroke="#fff" stroke-opacity="${dark ? 0.35 : 0.9}" stroke-width="2"/>
		${icon(name, px + panel * 0.12, py + panel * 0.12, (panel * 0.76) / 200, stroke)}
		${plus(Math.round(w * 0.12), Math.round(h * 0.18), 10, dark ? '#fff' : C.primary)}
		${plus(Math.round(w * 0.88), Math.round(h * 0.78), 8, dark ? '#fff' : C.teal)}
		${plus(Math.round(w * 0.8), Math.round(h * 0.16), 6, dark ? '#fff' : C.primary, 0.35)}`;
	return svg(w, h, body, commonDefs(from, to, Math.round(Math.min(w, h) * 0.12), dot));
}

/* ------------------------------------------------------------------- hero */

function hero() {
	const w = 1600;
	const h = 900;
	const ecg = 'M0 560H860l26-34 22 70 34-230 40 330 30-200 24 64h80l18-30 16 30H1600';
	const glassCard = (x, y, cw, ch, inner) => `
		<g transform="translate(${x} ${y})">
			<rect x="10" y="22" width="${cw}" height="${ch}" rx="26" fill="#000" opacity=".22" filter="url(#soft)"/>
			<rect width="${cw}" height="${ch}" rx="26" fill="url(#glassDark)" stroke="#fff" stroke-opacity=".35" stroke-width="1.5"/>
			${inner}
		</g>`;
	const line = (x, y, lw, o = 0.55, hgt = 12) => `<rect x="${x}" y="${y}" width="${lw}" height="${hgt}" rx="${hgt / 2}" fill="#fff" opacity="${o}"/>`;

	const body = `
		<rect width="${w}" height="${h}" fill="url(#bg)"/>
		<g filter="url(#blur)">
			<circle cx="250" cy="760" r="420" fill="${C.ink}" opacity=".9"/>
			<circle cx="1350" cy="120" r="380" fill="#6F9FD6" opacity=".75"/>
			<circle cx="980" cy="520" r="300" fill="${C.teal}" opacity=".55"/>
			<circle cx="1480" cy="780" r="260" fill="${C.primary}" opacity=".9"/>
		</g>
		<rect x="860" y="0" width="740" height="420" fill="url(#dots)"/>
		<g fill="none" stroke="#fff">
			<circle cx="1180" cy="420" r="210" stroke-opacity=".14" stroke-width="2"/>
			<circle cx="1180" cy="420" r="320" stroke-opacity=".09" stroke-width="2"/>
			<circle cx="1180" cy="420" r="440" stroke-opacity=".05" stroke-width="2"/>
		</g>
		<path d="${ecg}" fill="none" stroke="url(#ecgGlow)" stroke-width="16" stroke-linejoin="round" stroke-linecap="round" opacity=".45" filter="url(#glow)"/>
		<path d="${ecg}" fill="none" stroke="url(#ecgLine)" stroke-width="4" stroke-linejoin="round" stroke-linecap="round"/>
		<circle cx="1250" cy="560" r="9" fill="#fff"/>
		<circle cx="1250" cy="560" r="22" fill="#fff" opacity=".25"/>

		${glassCard(1090, 150, 380, 196, `
			<circle cx="58" cy="62" r="30" fill="#fff" opacity=".9"/>
			${icon('heart', 36, 40, 0.22, C.primary, 3)}
			${line(108, 46, 150, 0.85, 12)}
			${line(108, 70, 96, 0.5, 10)}
			${line(30, 122, 320, 0.22, 8)}
			<path d="M30 160h70l12-18 14 30 12-22 8 10h204" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" opacity=".9"/>`)}

		${glassCard(1180, 640, 300, 128, `
			<rect x="24" y="28" width="72" height="72" rx="18" fill="#fff" opacity=".9"/>
			${icon('calendar', 34, 38, 0.26, C.primary, 3)}
			${line(116, 40, 120, 0.85, 12)}
			${line(116, 64, 80, 0.5, 10)}
			<circle cx="258" cy="70" r="16" fill="#6FD3A8"/>
			<path d="M250 70l6 6 10-11" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>`)}

		${glassCard(900, 230, 120, 120, `
			<path d="M46 30h28v26h26v28H74v26H46V84H20V56h26Z" fill="#fff" opacity=".9"/>`)}

		${plus(820, 140, 12, '#fff', 0.5)}
		${plus(1530, 470, 9, '#fff', 0.45)}
		${plus(1000, 820, 8, '#fff', 0.35)}`;

	const defs = `
		<linearGradient id="bg" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="${C.ink}"/><stop offset=".55" stop-color="${C.primaryDark}"/><stop offset="1" stop-color="#5E8FCB"/></linearGradient>
		<filter id="blur" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="110"/></filter>
		<filter id="soft" x="-30%" y="-30%" width="160%" height="160%"><feGaussianBlur stdDeviation="18"/></filter>
		<filter id="glow" x="-10%" y="-50%" width="120%" height="200%"><feGaussianBlur stdDeviation="10"/></filter>
		<linearGradient id="ecgLine" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="1600" y2="0"><stop offset=".42" stop-color="#fff" stop-opacity="0"/><stop offset=".56" stop-color="#fff"/></linearGradient>
		<linearGradient id="ecgGlow" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="1600" y2="0"><stop offset=".42" stop-color="#9ED1FF" stop-opacity="0"/><stop offset=".56" stop-color="#9ED1FF"/></linearGradient>
		<pattern id="dots" width="26" height="26" patternUnits="userSpaceOnUse"><circle cx="3" cy="3" r="1.8" fill="#fff" fill-opacity=".22"/></pattern>
		<linearGradient id="glassDark" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".24"/><stop offset="1" stop-color="#fff" stop-opacity=".08"/></linearGradient>`;
	return svg(w, h, body, defs);
}

/* --------------------------------------------------------------- portraits */

function portrait({ bg, bg2, skin, hair, style, inner, seed }) {
	const w = 600;
	const h = 720;
	const shade = 'rgba(15,58,54,.10)';
	const hairBack = {
		long: `<path d="M206 330C196 228 252 214 300 214C364 214 410 236 396 336L410 520C372 548 228 548 192 520Z" fill="${hair}"/>`,
		bob: `<path d="M210 330C202 236 254 216 300 216C350 216 400 236 390 330L398 440C370 458 230 458 202 440Z" fill="${hair}"/>`,
		bun: `<circle cx="300" cy="214" r="46" fill="${hair}"/>`,
		short: '',
		grey: '',
	}[style];
	const hairFront = {
		long: `<path d="M222 326C226 258 270 236 318 240C362 244 384 280 380 326C356 288 316 272 280 286C252 296 234 310 222 326Z" fill="${hair}"/>`,
		bob: `<path d="M220 318C224 256 268 236 312 238C356 242 384 272 382 318C350 292 330 270 300 270C268 284 240 300 220 318Z" fill="${hair}"/>`,
		bun: `<path d="M222 322C222 262 262 240 300 240C342 240 380 262 378 322C356 292 330 280 300 280C268 280 240 296 222 322Z" fill="${hair}"/>`,
		short: `<path d="M220 330C210 256 254 232 302 232C356 232 394 256 382 330C372 296 352 282 300 280C252 282 230 300 220 330Z" fill="${hair}"/>`,
		grey: `<path d="M222 318C220 270 254 244 300 244C348 244 382 270 378 318C368 300 356 292 340 290C320 270 280 270 262 290C244 292 230 302 222 318Z" fill="${hair}"/>`,
	}[style];

	const body = `
		<rect width="${w}" height="${h}" fill="url(#bg)"/>
		${blobs(w, h, [bg2, '#fff'], seed, 70)}
		<rect x="380" y="0" width="220" height="220" fill="url(#dots)"/>
		<circle cx="300" cy="360" r="232" fill="#fff" opacity=".35"/>
		${hairBack}
		<path d="M84 720C90 574 170 506 300 496C430 506 510 574 516 720Z" fill="#fff"/>
		<path d="M84 720C88 640 110 590 150 556L196 720Z" fill="${shade}"/>
		<path d="M516 720C512 640 490 590 450 556L404 720Z" fill="${shade}"/>
		<path d="M246 500L300 610L354 500C336 494 318 492 300 492C282 492 264 494 246 500Z" fill="${inner}"/>
		<rect x="266" y="410" width="68" height="104" rx="30" fill="${skin}"/>
		<path d="M266 470C286 490 314 490 334 470V500H266Z" fill="${shade}"/>
		<path d="M246 500L300 610L262 720M354 500L300 610L338 720" fill="none" stroke="#D9E2EC" stroke-width="5" stroke-linejoin="round"/>
		<ellipse cx="222" cy="352" rx="15" ry="24" fill="${skin}"/>
		<ellipse cx="378" cy="352" rx="15" ry="24" fill="${skin}"/>
		<ellipse cx="300" cy="340" rx="80" ry="96" fill="${skin}"/>
		${hairFront}
		<path d="M262 500C252 574 270 618 300 624C330 618 348 574 338 500" fill="none" stroke="#33475B" stroke-width="7" stroke-linecap="round"/>
		<path d="M300 624v24" stroke="#33475B" stroke-width="7" stroke-linecap="round"/>
		<circle cx="300" cy="664" r="17" fill="#C9D3DD" stroke="#33475B" stroke-width="6"/>
		<rect x="392" y="590" width="62" height="20" rx="4" fill="${C.primary}"/>`;

	const defs = `
		<linearGradient id="bg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="${bg}"/><stop offset="1" stop-color="${bg2}"/></linearGradient>
		<filter id="blur" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="70"/></filter>
		<pattern id="dots" width="22" height="22" patternUnits="userSpaceOnUse"><circle cx="3" cy="3" r="1.8" fill="rgba(15,58,54,.14)"/></pattern>`;
	return svg(w, h, body, defs);
}

/* --------------------------------------------------------------------- map */

function map() {
	const w = 1200;
	const h = 800;
	const pin = (x, y, n) => `
		<g transform="translate(${x} ${y})">
			<ellipse cx="0" cy="6" rx="22" ry="8" fill="${C.ink}" opacity=".18"/>
			<path d="M0 0C-8-18-34-34-34-60a34 34 0 0 1 68 0C34-34 8-18 0 0Z" fill="${C.primary}"/>
			<circle cx="0" cy="-60" r="15" fill="#fff"/>
			<text x="0" y="-54" text-anchor="middle" font-family="Barlow, Helvetica, Arial, sans-serif" font-size="18" font-weight="600" fill="${C.primary}">${n}</text>
		</g>`;
	const body = `
		<rect width="${w}" height="${h}" fill="#EFE9DE"/>
		<path d="M-20 520C180 470 300 560 470 520S760 380 900 420S1120 520 1240 470V820H-20Z" fill="#E3DBCC" opacity=".6"/>
		<path d="M-40 640C160 600 260 690 430 650S700 520 880 560S1100 660 1260 610" fill="none" stroke="#B9D3EE" stroke-width="54" stroke-linecap="round"/>
		<path d="M140 120c60-40 160-30 190 30s-20 120-100 120-150-90-90-150Z" fill="${C.mint}"/>
		<path d="M880 120c80-30 190 10 200 80s-80 110-160 90-120-140-40-170Z" fill="${C.mint}"/>
		<g fill="none" stroke="#fff" stroke-linecap="round">
			<path d="M-20 300H1220M380 -20V820M760 -20V820M-20 90L1220 760" stroke-width="30"/>
			<path d="M-20 430H1220M560 -20V820M200 -20V820M960 -20V820M-20 180H1220M-20 740H1220" stroke-width="14"/>
		</g>
		<g fill="#E6DFD2">
			${[[230, 330, 120, 80], [420, 330, 110, 80], [600, 200, 130, 80], [800, 330, 130, 80], [990, 200, 150, 80], [420, 470, 110, 120], [600, 330, 130, 80], [230, 200, 120, 80], [800, 470, 130, 60], [990, 330, 150, 80]]
				.map(([x, y, bw, bh]) => `<rect x="${x}" y="${y}" width="${bw}" height="${bh}" rx="10"/>`)
				.join('')}
		</g>
		${pin(470, 300, 1)}
		${pin(860, 430, 2)}
		${pin(300, 560, 3)}`;
	return svg(w, h, body);
}

/* ------------------------------------------------------------------ logo watermark */

function mark() {
	return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48"><circle cx="24" cy="24" r="22" fill="${C.primary}"/><path d="M7 26h10l3.5-8 5 15 4.5-11 2.5 4H41" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>\n`;
}

/* ------------------------------------------------------------------ write */

const files = {
	'hero.svg': hero(),
	'favicon.svg': mark(),
	'map.svg': map(),

	// Gallery strip
	'gallery-1.svg': card({ w: 900, h: 620, from: C.sand, to: '#F8F4EC', blobColors: [C.sky2, C.coral, C.mint2], icon: 'calendar', stroke: C.primary, seed: 3 }),
	'gallery-2.svg': card({ w: 900, h: 620, from: C.sky, to: '#EEF4FB', blobColors: [C.sky2, C.mint2, '#fff'], icon: 'stethoscope', stroke: C.ink, seed: 5 }),
	'gallery-3.svg': card({ w: 900, h: 620, from: C.primaryDark, to: C.primary, blobColors: [C.teal, '#6F9FD6', C.ink], icon: 'ecg', stroke: '#fff', seed: 9, dark: true }),
	'gallery-4.svg': card({ w: 900, h: 620, from: C.mint, to: '#EEF7F3', blobColors: [C.mint2, C.sky2, '#fff'], icon: 'flask', stroke: C.teal, seed: 11 }),

	// Services
	'service-general.svg': card({ from: C.sky, to: '#F1F6FC', blobColors: [C.sky2, C.mint2], icon: 'stethoscope', stroke: C.primary, seed: 21 }),
	'service-neuro.svg': card({ from: C.ink, to: C.teal, blobColors: [C.teal, '#5E8FCB'], icon: 'brain', stroke: '#fff', seed: 22, dark: true }),
	'service-cardio.svg': card({ from: '#FBEDE6', to: C.sand, blobColors: [C.coral, '#fff', C.sky2], icon: 'heart', stroke: '#B4533A', seed: 23 }),
	'service-ortho.svg': card({ from: C.sand, to: C.sand2, blobColors: [C.sky2, '#fff'], icon: 'bone', stroke: C.ink, seed: 24 }),
	'service-physio.svg': card({ from: C.mint, to: '#F1F8F5', blobColors: [C.mint2, C.sky2], icon: 'spine', stroke: C.teal, seed: 25 }),
	'service-diagnostics.svg': card({ from: C.primaryDark, to: C.primary, blobColors: ['#6F9FD6', C.teal], icon: 'monitor', stroke: '#fff', seed: 26, dark: true }),
	'service-prevention.svg': card({ from: '#EEF4FB', to: C.sky, blobColors: [C.mint2, C.sky2], icon: 'shield', stroke: C.primary, seed: 27 }),
	'service-lab.svg': card({ from: C.mint, to: C.sky, blobColors: [C.mint2, '#fff'], icon: 'flask', stroke: C.ink, seed: 28 }),

	// News fallbacks
	'news-1.svg': card({ from: C.sand, to: C.sand2, blobColors: [C.coral, C.sky2], icon: 'shield', stroke: C.ink, seed: 31 }),
	'news-2.svg': card({ from: C.ink, to: C.primaryDark, blobColors: [C.teal, '#5E8FCB'], icon: 'pulse', stroke: '#fff', seed: 32, dark: true }),
	'news-3.svg': card({ from: C.sky, to: C.mint, blobColors: [C.sky2, C.mint2], icon: 'calendar', stroke: C.primary, seed: 33 }),

	// Team portraits
	'doctor-1.svg': portrait({ bg: C.sky, bg2: '#EEF4FB', skin: '#F2C9A8', hair: '#3B2A22', style: 'long', inner: '#4F86C6', seed: 41 }),
	'doctor-2.svg': portrait({ bg: C.mint, bg2: '#EEF7F3', skin: '#C98E66', hair: '#1F1A17', style: 'short', inner: C.teal, seed: 42 }),
	'doctor-3.svg': portrait({ bg: C.sand, bg2: '#FBF8F2', skin: '#F6D5BA', hair: '#B9874E', style: 'bun', inner: '#7FA7D6', seed: 43 }),
	'doctor-4.svg': portrait({ bg: '#E6ECF3', bg2: C.sky, skin: '#E9BE98', hair: '#A9B0B6', style: 'grey', inner: C.primaryDark, seed: 44 }),
};

for (const [name, content] of Object.entries(files)) {
	writeFileSync(join(OUT, name), content.replace(/\n\s+/g, '\n'));
}
console.log(`Wrote ${Object.keys(files).length} images to ${OUT}`);
