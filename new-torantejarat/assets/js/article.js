/**
 * Single article interactions: reading progress, TOC highlighting and share.
 * Loaded with defer on single posts only.
 */
(function () {
	'use strict';

	var doc = document;

	/* Reading progress bar. */
	var progress = doc.querySelector('[data-tta-progress] span');
	if (progress) {
		var ticking = false;

		var paintProgress = function () {
			var root = doc.documentElement;
			var max = root.scrollHeight - root.clientHeight;
			var percent = max > 0 ? (root.scrollTop / max) * 100 : 0;
			progress.style.width = Math.min(100, Math.max(0, percent)) + '%';
			ticking = false;
		};

		var queueProgress = function () {
			if (ticking) {
				return;
			}
			ticking = true;
			if (window.requestAnimationFrame) {
				window.requestAnimationFrame(paintProgress);
			} else {
				window.setTimeout(paintProgress, 16);
			}
		};

		window.addEventListener('scroll', queueProgress, { passive: true });
		window.addEventListener('resize', queueProgress);
		paintProgress();
	}

	/* Highlight the TOC entry for the section currently being read.
	   Both the sidebar list and the compact-viewport details element are
	   synced; only one of the two is visible at any width. */
	var tocs = doc.querySelectorAll('[data-tta-toc]');
	var body = doc.querySelector('.torantejarat-article-body');

	if (tocs.length && body) {
		var links = {};
		var sections = [];

		Array.prototype.forEach.call(tocs, function (container) {
			Array.prototype.forEach.call(container.querySelectorAll('a[href^="#"]'), function (link) {
				var id = decodeURIComponent(link.getAttribute('href').slice(1));
				var target = id ? doc.getElementById(id) : null;

				if (!id || !target) {
					return;
				}

				if (!links[id]) {
					links[id] = [];
					sections.push(target);
				}

				links[id].push(link);

				/* Collapse the inline list once a section is picked. */
				if ('DETAILS' === container.tagName) {
					link.addEventListener('click', function () {
						window.setTimeout(function () {
							container.open = false;
						}, 80);
					});
				}
			});
		});

		sections.sort(function (a, b) {
			return a.getBoundingClientRect().top - b.getBoundingClientRect().top;
		});

		var activeId = null;

		var setActive = function (id) {
			if (id === activeId) {
				return;
			}
			activeId = id;

			Array.prototype.forEach.call(tocs, function (container) {
				Array.prototype.forEach.call(container.querySelectorAll('a.is-active'), function (active) {
					active.classList.remove('is-active');
				});
			});

			if (links[id]) {
				links[id].forEach(function (link) {
					link.classList.add('is-active');
				});
			}
		};

		/* Last heading that has passed the reading line wins. */
		var syncActive = function () {
			var line = 170;
			var current = '';

			for (var i = 0; i < sections.length; i += 1) {
				if (sections[i].getBoundingClientRect().top <= line) {
					current = sections[i].id;
				} else {
					break;
				}
			}

			setActive(current);
		};

		var syncQueued = false;

		var queueSync = function () {
			if (syncQueued) {
				return;
			}
			syncQueued = true;
			if (window.requestAnimationFrame) {
				window.requestAnimationFrame(function () {
					syncQueued = false;
					syncActive();
				});
			} else {
				window.setTimeout(function () {
					syncQueued = false;
					syncActive();
				}, 16);
			}
		};

		window.addEventListener('scroll', queueSync, { passive: true });
		window.addEventListener('resize', queueSync);
		syncActive();
	}

	/* Copy link / native share. */
	var shareStatus = doc.querySelector('[data-tta-share-status]');
	var strings = window.torantejaratArticle || {};

	var announce = function (message) {
		if (!shareStatus) {
			return;
		}
		shareStatus.textContent = message;
		window.clearTimeout(announce.timer);
		announce.timer = window.setTimeout(function () {
			shareStatus.textContent = '';
		}, 4000);
	};

	var copyText = function (text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}

		return new Promise(function (resolve, reject) {
			var field = doc.createElement('textarea');
			field.value = text;
			field.setAttribute('readonly', '');
			field.style.position = 'fixed';
			field.style.top = '-1000px';
			field.style.opacity = '0';
			doc.body.appendChild(field);
			field.select();

			try {
				if (doc.execCommand('copy')) {
					resolve();
				} else {
					reject(new Error('copy-failed'));
				}
			} catch (error) {
				reject(error);
			} finally {
				doc.body.removeChild(field);
			}
		});
	};

	var copyButton = doc.querySelector('[data-tta-copy]');
	if (copyButton) {
		copyButton.addEventListener('click', function () {
			copyText(window.location.href).then(
				function () {
					announce(strings.copyDone || 'لینک کپی شد.');
				},
				function () {
					announce(strings.copyFail || 'کپی انجام نشد؛ لینک را دستی بردارید.');
				}
			);
		});
	}

	var nativeButton = doc.querySelector('[data-tta-native-share]');
	if (nativeButton && navigator.share) {
		nativeButton.hidden = false;
		nativeButton.addEventListener('click', function () {
			navigator
				.share({
					title: doc.title,
					url: window.location.href
				})
				.catch(function () {
					/* The user dismissed the sheet; nothing to report. */
				});
		});
	}
})();
