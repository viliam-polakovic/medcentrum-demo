/**
 * MedCentrum booking form: service → date & time → patient details → confirm.
 */
(function () {
	'use strict';

	var STEPS = ['service', 'slot', 'details', 'confirm'];

	function pad(n) { return (n < 10 ? '0' : '') + n; }
	function ymd(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
	function ym(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1); }
	function parse(s) { var p = s.split('-'); return new Date(+p[0], +p[1] - 1, +(p[2] || 1)); }
	function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }
	/** sprintf-lite for translated strings: %s, %d and numbered %1$s placeholders. */
	function format(str) {
		var args = Array.prototype.slice.call(arguments, 1);
		var i = 0;
		return str.replace(/%(?:(\d+)\$)?[sd]/g, function (m, n) { return n ? args[n - 1] : args[i++]; });
	}

	function Booking(root) {
		this.root = root;
		this.cfg = JSON.parse(root.getAttribute('data-mcb'));
		this.t = this.cfg.i18n;

		var lang = (this.cfg.locale || 'en_US').replace('_', '-');
		this.fmt = {
			month: new Intl.DateTimeFormat(lang, { month: 'long', year: 'numeric' }),
			day: new Intl.DateTimeFormat(lang, { weekday: 'long', day: 'numeric', month: 'long' }),
			short: new Intl.DateTimeFormat(lang, { weekday: 'short', day: 'numeric', month: 'numeric', year: 'numeric' }),
			dow: new Intl.DateTimeFormat(lang, { weekday: 'short' })
		};
		this.plural = new Intl.PluralRules(lang);
		this.form = root.querySelector('.mcb__form');
		this.grid = root.querySelector('.mcb-cal__grid');
		this.monthLabel = root.querySelector('.mcb-cal__month');
		this.timesLabel = root.querySelector('.mcb-times__label');
		this.timesList = root.querySelector('.mcb-times__list');
		this.alert = root.querySelector('.mcb-alert--confirm');
		this.slotAlert = root.querySelector('.mcb-alert--slot');
		this.done = root.querySelector('.mcb-done');

		var today = parse(this.cfg.today);
		this.minMonth = new Date(today.getFullYear(), today.getMonth(), 1);
		var last = new Date(today);
		last.setDate(last.getDate() + this.cfg.horizon);
		this.maxMonth = new Date(last.getFullYear(), last.getMonth(), 1);

		this.state = { service: '', date: '', time: '' };
		this.month = new Date(this.minMonth);
		this.days = {};
		this.monthCache = {};
		this.autoAdvance = 2;

		this.renderWeekdays();
		this.bind();
		this.preselect();
	}

	Booking.prototype.slots = function (n) {
		var forms = this.t.slots;
		return format(forms[this.plural.select(n)] || forms.other, n);
	};

	/** Weekday initials, Monday first, in the page language. */
	Booking.prototype.renderWeekdays = function () {
		var row = this.root.querySelector('.mcb-cal__dow');
		for (var i = 0; i < 7; i++) {
			var span = document.createElement('span');
			span.textContent = cap(this.fmt.dow.format(new Date(2024, 0, 1 + i)).replace('.', ''));
			row.appendChild(span);
		}
	};

	Booking.prototype.step = function (name) {
		return this.root.querySelector('[data-step="' + name + '"]');
	};

	Booking.prototype.complete = function (name) {
		var s = this.state;
		if (name === 'service') return !!s.service;
		if (name === 'slot') return !!(s.date && s.time);
		if (name === 'details') return !!s.detailsOk;
		return false;
	};

	/** Opens one step; the others collapse into "done" (with summary) or "locked". */
	Booking.prototype.open = function (name) {
		var self = this;
		STEPS.forEach(function (stepName) {
			var el = self.step(stepName);
			var done = stepName !== name && self.complete(stepName);
			el.classList.toggle('is-active', stepName === name);
			el.classList.toggle('is-done', done);
			el.classList.toggle('is-locked', stepName !== name && !done);
			var edit = el.querySelector('[data-edit]');
			if (edit) edit.hidden = !done;
		});
		if (name === 'slot' && !this.loadedOnce) {
			this.loadedOnce = true;
			this.loadMonth();
		}
		if (name === 'confirm') this.renderReview();

		var el = this.step(name);
		var rect = el.getBoundingClientRect();
		if (rect.top < 0 || rect.top > window.innerHeight * 0.6) {
			el.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
		var focusable = el.querySelector('.mcb-step__body input:not([type=hidden]), .mcb-step__body button');
		if (focusable && name !== 'slot') focusable.focus({ preventScroll: true });
	};

	Booking.prototype.next = function () {
		for (var i = 0; i < STEPS.length; i++) {
			if (!this.complete(STEPS[i])) return this.open(STEPS[i]);
		}
	};

	Booking.prototype.summary = function (name, text) {
		this.step(name).querySelector('[data-summary]').textContent = text;
	};

	Booking.prototype.bind = function () {
		var self = this;

		this.form.addEventListener('change', function (e) {
			if (e.target.name === 'service') {
				self.state.service = e.target.value;
				self.summary('service', e.target.parentNode.textContent.trim());
				self.next();
			}
		});

		this.root.querySelectorAll('[data-edit]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				self.open(btn.closest('.mcb-step').getAttribute('data-step'));
			});
		});

		this.root.querySelectorAll('.mcb-step__head').forEach(function (head) {
			head.addEventListener('click', function (e) {
				var step = head.closest('.mcb-step');
				if (e.target.closest('[data-edit]') || !step.classList.contains('is-done')) return;
				self.open(step.getAttribute('data-step'));
			});
		});

		this.root.querySelectorAll('[data-month]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				self.autoAdvance = 0;
				self.month.setMonth(self.month.getMonth() + +btn.getAttribute('data-month'));
				self.loadMonth();
			});
		});

		this.root.querySelector('[data-next]').addEventListener('click', function () {
			if (self.validateDetails()) {
				self.state.detailsOk = true;
				var f = self.form.elements;
				self.summary('details', f.first_name.value.trim() + ' ' + f.last_name.value.trim());
				self.next();
			}
		});

		this.form.addEventListener('input', function (e) {
			var field = e.target.closest('.mcb-field');
			if (field && field.classList.contains('has-error')) self.clearError(field);
			if (self.state.detailsOk && e.target.closest('[data-step="details"]')) {
				self.state.detailsOk = false;
			}
		});

		this.form.addEventListener('submit', function (e) {
			e.preventDefault();
			self.submit();
		});

		this.root.querySelector('[data-restart]').addEventListener('click', function () {
			self.restart();
		});

		// Service cards elsewhere on the page can preselect a service: <a data-mcb-service="…">.
		document.addEventListener('click', function (e) {
			var link = e.target.closest('[data-mcb-service]');
			if (link) self.selectService(link.getAttribute('data-mcb-service'));
		});
	};

	Booking.prototype.preselect = function () {
		var param = new URLSearchParams(window.location.search).get('service');
		if (param) this.selectService(param);
	};

	Booking.prototype.selectService = function (name) {
		// Match the stored value or the visible label, so links can use either.
		var wanted = String(name).toLowerCase();
		var input = Array.prototype.find.call(this.form.querySelectorAll('[name=service]'), function (el) {
			return el.value.toLowerCase() === wanted || el.parentNode.textContent.trim().toLowerCase() === wanted;
		});
		if (!input) return;
		input.checked = true;
		this.state.service = input.value;
		this.summary('service', input.parentNode.textContent.trim());
		this.next();
	};

	/* ---------------------------------------------------------------- calendar */

	/** REST URL; works with pretty permalinks (/wp-json/…) and plain ones (?rest_route=…). */
	Booking.prototype.url = function (path, params) {
		var base = this.cfg.api + path;
		var qs = new URLSearchParams(params || {}).toString();
		return qs ? base + (base.indexOf('?') === -1 ? '?' : '&') + qs : base;
	};

	Booking.prototype.fetch = function (path, params) {
		return fetch(this.url(path, params), { headers: { Accept: 'application/json' } }).then(function (r) {
			if (!r.ok) throw new Error(r.status);
			return r.json();
		});
	};

	Booking.prototype.loadMonth = function () {
		var self = this;
		var key = ym(this.month);
		this.renderMonthNav();
		this.grid.setAttribute('aria-busy', 'true');
		this.grid.classList.add('is-loading');
		this.renderGrid(this.monthCache[key] || {});

		this.fetch('month', { month: key })
			.then(function (res) {
				self.monthCache[key] = res.days;
				if (ym(self.month) !== key) return;
				var free = Object.keys(res.days).some(function (d) { return res.days[d] > 0; });
				// When the current month is fully booked, jump ahead instead of showing an empty calendar.
				if (!free && self.autoAdvance > 0 && self.month < self.maxMonth) {
					self.autoAdvance--;
					self.month.setMonth(self.month.getMonth() + 1);
					return self.loadMonth();
				}
				self.autoAdvance = 0;
				self.days = res.days;
				self.renderGrid(res.days);
			})
			.catch(function () {
				self.renderGrid({});
				self.timesLabel.textContent = self.t.calendarError;
			})
			.finally(function () {
				self.grid.removeAttribute('aria-busy');
				self.grid.classList.remove('is-loading');
			});
	};

	Booking.prototype.renderMonthNav = function () {
		this.monthLabel.textContent = cap(this.fmt.month.format(this.month));
		this.root.querySelector('[data-month="-1"]').disabled = this.month <= this.minMonth;
		this.root.querySelector('[data-month="1"]').disabled = this.month >= this.maxMonth;
	};

	Booking.prototype.renderGrid = function (days) {
		var self = this;
		var first = new Date(this.month);
		var offset = (first.getDay() + 6) % 7; // Monday first
		var count = new Date(first.getFullYear(), first.getMonth() + 1, 0).getDate();
		var frag = document.createDocumentFragment();

		for (var i = 0; i < offset; i++) {
			frag.appendChild(document.createElement('span'));
		}
		for (var d = 1; d <= count; d++) {
			var date = new Date(first.getFullYear(), first.getMonth(), d);
			var key = ymd(date);
			var free = days[key] || 0;
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'mcb-day' + (free ? ' has-slots' : '') + (key === this.cfg.today ? ' is-today' : '') + (key === this.state.date ? ' is-selected' : '');
			btn.textContent = d;
			btn.disabled = !free;
			btn.setAttribute('data-date', key);
			btn.setAttribute('aria-label', this.fmt.day.format(date) + ', ' + (free ? this.slots(free) : this.t.noSlots));
			btn.setAttribute('aria-pressed', key === this.state.date ? 'true' : 'false');
			frag.appendChild(btn);
		}
		this.grid.innerHTML = '';
		this.grid.appendChild(frag);

		this.grid.querySelectorAll('.mcb-day:not([disabled])').forEach(function (btn) {
			btn.addEventListener('click', function () { self.pickDate(btn.getAttribute('data-date')); });
		});

		if (!Object.keys(days).length) return;
		var anyFree = Object.keys(days).some(function (k) { return days[k] > 0; });
		if (!this.state.date) {
			this.timesLabel.textContent = anyFree ? this.t.pickDay : this.t.noSlotsMonth;
			this.timesList.innerHTML = '';
		}
	};

	Booking.prototype.pickDate = function (date) {
		var self = this;
		this.state.date = date;
		this.state.time = '';
		this.grid.querySelectorAll('.mcb-day').forEach(function (b) {
			var on = b.getAttribute('data-date') === date;
			b.classList.toggle('is-selected', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		this.timesLabel.textContent = this.t.loadingTimes;
		this.timesList.innerHTML = '';
		this.timesList.classList.add('is-loading');

		this.fetch('slots', { date: date })
			.then(function (res) {
				if (self.state.date !== date) return;
				self.renderTimes(res.slots);
			})
			.catch(function () {
				self.timesLabel.textContent = self.t.timesError;
			})
			.finally(function () {
				self.timesList.classList.remove('is-loading');
			});
	};

	Booking.prototype.renderTimes = function (times) {
		var self = this;
		var label = cap(this.fmt.day.format(parse(this.state.date)));
		this.timesLabel.textContent = times.length ? label : format(this.t.dayFull, label);
		this.timesList.innerHTML = '';

		var groups = {};
		groups[this.t.morning] = [];
		groups[this.t.afternoon] = [];
		times.forEach(function (t) { groups[+t.split(':')[0] < 12 ? self.t.morning : self.t.afternoon].push(t); });

		Object.keys(groups).forEach(function (name) {
			if (!groups[name].length) return;
			var wrap = document.createElement('div');
			wrap.className = 'mcb-times__group';
			var h = document.createElement('span');
			h.className = 'mcb-times__group-label';
			h.textContent = name;
			wrap.appendChild(h);
			var list = document.createElement('div');
			list.className = 'mcb-times__grid';
			groups[name].forEach(function (t) {
				var b = document.createElement('button');
				b.type = 'button';
				b.className = 'mcb-time' + (t === self.state.time ? ' is-selected' : '');
				b.textContent = t;
				b.addEventListener('click', function () { self.pickTime(t); });
				list.appendChild(b);
			});
			wrap.appendChild(list);
			self.timesList.appendChild(wrap);
		});
	};

	Booking.prototype.pickTime = function (time) {
		this.state.time = time;
		this.slotAlert.hidden = true;
		this.timesList.querySelectorAll('.mcb-time').forEach(function (b) {
			b.classList.toggle('is-selected', b.textContent === time);
		});
		this.summary('slot', this.fmt.short.format(parse(this.state.date)) + ' · ' + time);
		this.next();
	};

	/* ----------------------------------------------------------------- details */

	Booking.prototype.setError = function (field, message) {
		field.classList.add('has-error');
		var msg = field.querySelector('.mcb-field__error');
		if (!msg) {
			msg = document.createElement('span');
			msg.className = 'mcb-field__error';
			field.appendChild(msg);
		}
		msg.textContent = message;
		var input = field.querySelector('input, select, textarea');
		if (input) input.setAttribute('aria-invalid', 'true');
	};

	Booking.prototype.clearError = function (field) {
		field.classList.remove('has-error');
		var msg = field.querySelector('.mcb-field__error');
		if (msg) msg.remove();
		var input = field.querySelector('[aria-invalid]');
		if (input) input.removeAttribute('aria-invalid');
	};

	Booking.prototype.validateDetails = function () {
		var self = this;
		var f = this.form.elements;
		var m = this.t.fields;
		var checks = {
			first_name: f.first_name.value.trim() ? '' : m.first_name,
			last_name: f.last_name.value.trim() ? '' : m.last_name,
			email: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(f.email.value.trim()) ? '' : m.email,
			phone: /^\+?[0-9 ()\/-]{6,20}$/.test(f.phone.value.trim()) ? '' : m.phone,
			consent: f.consent.checked ? '' : m.consent
		};
		if (f.insurance) checks.insurance = f.insurance.value ? '' : m.insurance;
		if (f.birthdate.value && f.birthdate.value > this.cfg.today) checks.birthdate = m.birthdate;

		var firstBad = null;
		Object.keys(checks).forEach(function (name) {
			var field = f[name].closest('.mcb-field');
			if (checks[name]) {
				self.setError(field, checks[name]);
				firstBad = firstBad || f[name];
			} else {
				self.clearError(field);
			}
		});
		if (firstBad) firstBad.focus();
		return !firstBad;
	};

	Booking.prototype.renderReview = function () {
		var f = this.form.elements;
		var t = this.t;
		var service = this.form.querySelector('[name=service]:checked');
		var rows = [
			[t.service, service ? service.parentNode.textContent.trim() : this.state.service],
			[t.appointment, format(t.at, cap(this.fmt.day.format(parse(this.state.date))), this.state.time)],
			[t.patient, f.first_name.value.trim() + ' ' + f.last_name.value.trim()],
			[t.contact, f.email.value.trim() + ' · ' + f.phone.value.trim()]
		];
		if (f.insurance && f.insurance.value) rows.push([t.insurer, f.insurance.options[f.insurance.selectedIndex].text]);
		var dl = this.root.querySelector('.mcb-review');
		dl.innerHTML = '';
		rows.forEach(function (row) {
			var wrap = document.createElement('div');
			var dt = document.createElement('dt');
			var dd = document.createElement('dd');
			dt.textContent = row[0];
			dd.textContent = row[1];
			wrap.appendChild(dt);
			wrap.appendChild(dd);
			dl.appendChild(wrap);
		});
		this.alert.hidden = true;
	};

	/* ------------------------------------------------------------------ submit */

	Booking.prototype.submit = function () {
		var self = this;
		var f = this.form.elements;
		var btn = this.form.querySelector('.mcb-btn--submit');
		if (btn.disabled) return;

		var payload = {
			service: this.state.service,
			date: this.state.date,
			time: this.state.time,
			first_name: f.first_name.value.trim(),
			last_name: f.last_name.value.trim(),
			email: f.email.value.trim(),
			phone: f.phone.value.trim(),
			birthdate: f.birthdate.value,
			insurance: f.insurance ? f.insurance.value : '',
			note: f.note.value.trim(),
			consent: f.consent.checked,
			website: f.website.value,
			locale: this.cfg.locale
		};

		btn.disabled = true;
		btn.classList.add('is-loading');
		this.alert.hidden = true;

		fetch(this.url('book'), {
			method: 'POST',
			headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
			body: JSON.stringify(payload)
		})
			.then(function (r) { return r.json().then(function (body) { return { ok: r.ok, body: body }; }); })
			.then(function (res) {
				if (res.ok) return self.success(res.body);
				self.fail(res.body || {});
			})
			.catch(function () {
				self.fail({ message: self.t.networkError });
			})
			.finally(function () {
				btn.disabled = false;
				btn.classList.remove('is-loading');
			});
	};

	Booking.prototype.fail = function (body) {
		var self = this;
		this.alert.textContent = body.message || this.t.genericError;
		this.alert.hidden = false;

		if (body.code === 'mcb_taken' || body.code === 'mcb_busy') {
			// Someone else got the slot: refresh availability and let the patient pick again.
			this.state.time = '';
			this.summary('slot', '');
			delete this.monthCache[ym(this.month)];
			this.open('slot');
			this.slotAlert.textContent = body.message;
			this.slotAlert.hidden = false;
			this.loadMonth();
			if (this.state.date) this.pickDate(this.state.date);
			return;
		}
		var fields = body.data && body.data.fields;
		if (fields) {
			var detailFields = Object.keys(fields).filter(function (k) { return self.form.elements[k] && k !== 'service'; });
			if (detailFields.length) {
				this.state.detailsOk = false;
				this.open('details');
				detailFields.forEach(function (k) {
					self.setError(self.form.elements[k].closest('.mcb-field'), fields[k]);
				});
			}
		}
	};

	Booking.prototype.success = function (body) {
		this.form.hidden = true;
		this.root.querySelector('.mcb__header').hidden = true;
		this.done.hidden = false;
		var confirmed = body.status === 'confirmed';
		this.done.querySelector('.mcb-done__title').textContent = confirmed ? this.t.doneConfirmed : this.t.donePending;
		this.done.querySelector('.mcb-done__text').textContent =
			format(confirmed ? this.t.doneConfirmedText : this.t.donePendingText, body.service, body.when, body.email);
		this.done.focus();
		this.done.scrollIntoView({ behavior: 'smooth', block: 'center' });
	};

	Booking.prototype.restart = function () {
		var keep = { first_name: 1, last_name: 1, email: 1, phone: 1, birthdate: 1, insurance: 1 };
		Array.prototype.forEach.call(this.form.elements, function (el) {
			if (el.name === 'service' || el.name === 'consent') el.checked = false;
			else if (el.name === 'note') el.value = '';
			else if (el.name && !keep[el.name] && el.type !== 'button' && el.type !== 'submit') el.value = '';
		});
		this.state = { service: '', date: '', time: '' };
		this.monthCache = {};
		this.loadedOnce = false;
		this.month = new Date(this.minMonth);
		this.autoAdvance = 2;
		this.timesList.innerHTML = '';
		['service', 'slot', 'details'].forEach(function (s) { this.summary(s, ''); }, this);
		this.form.hidden = false;
		this.root.querySelector('.mcb__header').hidden = false;
		this.done.hidden = true;
		this.open('service');
	};

	function init() {
		document.querySelectorAll('.mcb[data-mcb]').forEach(function (el) { new Booking(el); });
	}
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
	else init();
})();
