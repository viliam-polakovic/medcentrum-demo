/**
 * MedCentrum theme interactions: sticky header, menu drawer, horizontal
 * scrollers, services view toggle, scroll reveal and counters.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Header shadow once the page scrolls -------------------------------- */
	var header = document.querySelector('[data-header]');
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* Menu drawer -------------------------------------------------------- */
	var drawer = document.querySelector('[data-drawer]');
	var opener = document.querySelector('[data-drawer-open]');
	if (drawer && opener) {
		var panel = drawer.querySelector('.drawer__panel');
		var open = function () {
			drawer.hidden = false;
			document.body.classList.add('drawer-open');
			opener.setAttribute('aria-expanded', 'true');
			panel.querySelector('[data-drawer-close]').focus();
		};
		var close = function (restoreFocus) {
			drawer.hidden = true;
			document.body.classList.remove('drawer-open');
			opener.setAttribute('aria-expanded', 'false');
			if (restoreFocus) opener.focus();
		};
		opener.addEventListener('click', open);
		drawer.querySelectorAll('[data-drawer-close]').forEach(function (el) {
			el.addEventListener('click', function () { close(true); });
		});
		panel.addEventListener('click', function (e) {
			if (e.target.closest('a')) close(false);
		});
		document.addEventListener('keydown', function (e) {
			if (drawer.hidden) return;
			if (e.key === 'Escape') close(true);
			if (e.key === 'Tab') {
				// Keep focus inside the open drawer.
				var items = panel.querySelectorAll('a, button');
				var first = items[0];
				var last = items[items.length - 1];
				if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
				else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
			}
		});
	}

	/* Horizontal scrollers ----------------------------------------------- */
	document.querySelectorAll('[data-scroller]').forEach(function (scroller) {
		var track = scroller.querySelector('[data-scroller-track]');
		var bar = scroller.querySelector('[data-scroller-bar]');
		var prev = scroller.querySelector('[data-scroller-prev]');
		var next = scroller.querySelector('[data-scroller-next]');

		var update = function () {
			var total = track.scrollWidth;
			if (bar) {
				bar.style.width = Math.min(100, (track.clientWidth / total) * 100) + '%';
				bar.style.left = (track.scrollLeft / total) * 100 + '%';
			}
			if (prev) prev.disabled = track.scrollLeft <= 2;
			if (next) next.disabled = track.scrollLeft + track.clientWidth >= total - 2;
		};
		var step = function (dir) {
			var item = track.children[0];
			var size = item ? item.getBoundingClientRect().width + 18 : track.clientWidth * 0.8;
			var count = Math.max(1, Math.floor(track.clientWidth / size) - 1);
			track.scrollBy({ left: dir * size * count, behavior: reduceMotion ? 'auto' : 'smooth' });
		};

		track.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		if (prev) prev.addEventListener('click', function () { step(-1); });
		if (next) next.addEventListener('click', function () { step(1); });
		update();
	});

	/* Services: cards / list --------------------------------------------- */
	document.querySelectorAll('[data-view-set]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var section = btn.closest('[data-view]');
			section.setAttribute('data-view', btn.getAttribute('data-view-set'));
			section.querySelectorAll('[data-view-set]').forEach(function (b) {
				b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
			});
		});
	});

	/* Counters ----------------------------------------------------------- */
	var countUp = function (el) {
		var target = parseInt(el.getAttribute('data-count'), 10);
		var start = null;
		var duration = 1400;
		var frame = function (t) {
			if (start === null) start = t;
			var p = Math.min(1, (t - start) / duration);
			el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
			if (p < 1) window.requestAnimationFrame(frame);
		};
		el.textContent = '0';
		window.requestAnimationFrame(frame);
	};

	/* Reveal on scroll --------------------------------------------------- */
	var revealEls = document.querySelectorAll('.reveal');
	if (!('IntersectionObserver' in window) || reduceMotion) {
		revealEls.forEach(function (el) { el.classList.add('is-visible'); });
		return;
	}

	// Stagger siblings in the same grid.
	revealEls.forEach(function (el) {
		var siblings = Array.prototype.filter.call(el.parentNode.children, function (c) { return c.classList.contains('reveal'); });
		var index = siblings.indexOf(el);
		if (index > 0) el.style.transitionDelay = Math.min(index, 4) * 90 + 'ms';
	});

	var io = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) return;
			entry.target.classList.add('is-visible');
			entry.target.querySelectorAll('[data-count]').forEach(countUp);
			io.unobserve(entry.target);
		});
	}, { rootMargin: '0px 0px -8% 0px' });

	revealEls.forEach(function (el) { io.observe(el); });
})();
