/* Product video picker: select/remove a library video for the Theme meta box. */
(function ($) {
	'use strict';
	$(function () {
		var frame = null;
		var $url = $('#torantejarat-video');
		var $id = $('#torantejarat-video-id');
		var $preview = $('#torantejarat-video-preview');
		var $remove = $('#torantejarat-video-remove');
		if (!$url.length) return;

		function showPreview(src) {
			src = src.trim();
			if (!src) {
				$id.val('');
				$preview.hide().empty();
				$remove.hide();
				return;
			}
			if (/\.(mp4|webm|ogg)(\?|#|$)/i.test(src)) {
				$preview.html($('<video>', { controls: true, preload: 'none', src: src, css: { width: '100%' } }));
			} else if (/aparat\.com\/(?:v\/|video\/video\/embed\/videohash\/)([a-zA-Z0-9]+)/i.test(src)) {
				var m = src.match(/aparat\.com\/(?:v\/|video\/video\/embed\/videohash\/)([a-zA-Z0-9]+)/i);
				$preview.html($('<iframe>', { src: 'https://www.aparat.com/video/video/embed/videohash/' + m[1] + '/vt/frame', css: { width: '100%', height: '200', frameborder: '0', allowfullscreen: true } }));
			} else if (/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{6,})/.test(src)) {
				var m = src.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{6,})/);
				$preview.html($('<iframe>', { src: 'https://www.youtube.com/embed/' + m[1], css: { width: '100%', height: '200', frameborder: '0', allowfullscreen: true } }));
			} else if (/vimeo\.com\/(\d+)/.test(src)) {
				var m = src.match(/vimeo\.com\/(\d+)/);
				$preview.html($('<iframe>', { src: 'https://player.vimeo.com/video/' + m[1], css: { width: '100%', height: '200', frameborder: '0', allowfullscreen: true } }));
			} else {
				$preview.html($('<p>', { class: 'description', css: { overflowWrap: 'anywhere' }, text: src }));
			}
			$preview.show();
			$remove.show();
		}

		$('#torantejarat-video-select').on('click', function (e) {
			e.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: 'Select product video',
				library: { type: 'video' },
				button: { text: 'Use this video' },
				multiple: false,
			});
			frame.on('select', function () {
				var file = frame.state().get('selection').first().toJSON();
				$id.val(file.id);
				$url.val(file.url);
				showPreview(file.url);
			});
			frame.open();
		});

		$remove.on('click', function (e) {
			e.preventDefault();
			$id.val('');
			$url.val('');
			$preview.hide().empty();
			$remove.hide();
		});

		$url.on('input', function () {
			showPreview($url.val());
		});
	});
})(jQuery);
