;$(function() {
	$('.js-product-3d-html5-opener').bind('click', function(e) {
		e.preventDefault();

		$LAB.script('/maybe3dPlayer/player.min.js').wait(function() {
			$('.js-product-3d-html5-popup').lightbox_me({
				centered: true,
				closeSelector: '.close',
				onLoad: function() {
					var $popup = $('.js-product-3d-html5-popup');
					Maybe3D.Starter.setModelPathHTML5($popup.data('url'));
					Maybe3D.Starter.embed($popup.data('id'), 'js-product-3d-html5-popup-model');
				},
				onClose: function() {
					$('#js-product-3d-html5-popup-model').empty();
				}
			});
		});
	});

	$('.js-product-3d-swf-opener').bind('click', function(e) {
		e.preventDefault();

		$LAB.script('swfobject.min.js').wait(function() {
			try {
				if (!$('#js-product-3d-swf-popup-model').length) {
					$('.js-product-3d-swf-popup-container').append('<div id="js-product-3d-swf-popup-model"></div>');
				}

				var
					swfId = 'js-product-3d-swf-popup-object',
					$popup = $('.js-product-3d-swf-popup');

				swfobject.embedSWF(
					$popup.data('url'),
					'js-product-3d-swf-popup-model',
					'700px',
					'500px',
					'10.0.0',
					'js/vendor/expressInstall.swf',
					{
						language: 'auto'
					},
					{
						menu: 'false',
						scale: 'noScale',
						allowFullscreen: 'true',
						allowScriptAccess: 'always',
						wmode: 'direct'
					},
					{
						id: swfId
					}
				);

				$popup.lightbox_me({
					centered: true,
					closeSelector: '.close',
					onClose: function() {
						swfobject.removeSWF(swfId);
					}
				});
			}
			catch (err) {}
		});
	});

	// 3D для мебели
	$('.js-product-3d-img-opener').bind('click', function(e) {
		e.preventDefault();

		$LAB.script('DAnimFramePlayer.min.js').wait(function() {
			var
				$element = $('.js-product-3d-img-popup'),
				data = $element.data('value'),
				host = $element.data('host');

			try {
				if (!$('#js-product-3d-img-container').length) {
					(new DAnimFramePlayer($element[0], host)).DoLoadModel(data);
				}

				$element.lightbox_me({
					centered: true,
					closeSelector: '.close'
				});
			}
			catch (err) {}
		});
	});
});