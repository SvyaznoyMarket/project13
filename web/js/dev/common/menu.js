// Simple lazy loading
;$('nav').on('mouseenter', '.navsite2_i', function(){
	$(this).find('.menuImgLazy').each(function(){
		$(this).attr('src', $(this).data('src'))
	});
});

$('nav').on('mouseenter', '.navsite_i', function(){
	var
		$el = $(this),
		url = $el.data('recommendUrl'),
		xhr = $el.data('recommendXhr')
		;

	if (url && !xhr) {
		xhr = $.get(url);
		$el.data('recommendXhr', xhr);

		xhr.done(function(response) {
			if (!response.productBlocks) return;

			var $containers = $el.find('.jsMenuRecommendation');

			$.each(response.productBlocks, function(i, block) {
				try {
					if (!block.categoryId) return;

					var $container = $containers.filter('[data-parent-category-id="' + block.categoryId + '"]');
					$container.html(block.content);
				} catch (e) { console.error(e); }
			});
		});

		xhr.fail(function() {
			$el.data('recommendXhr', false);
			//$el.data('recommendXhr', true);
		});
	}
});

// аналитика
$('body').on('click', '.jsRecommendedItemInMenu', function(event) {
	console.log('jsRecommendedItemInMenu');

	event.stopPropagation();

	try {
		var
			$el = $(this),
			link = $el.attr('href'),
			sender = $el.data('sender')
			;

		body.trigger('TLT_processDOMEvent', [event]);

		$('body').trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Перешел на карточку товара',
			label: sender ? sender.position : null,
			hitCallback: function(){
				console.log({link: link});

				if (link) {
					setTimeout(function() { window.location.href = link; }, 90);
				}
			}
		});

		$el.trigger('TL_recommendation_clicked');

	} catch (e) { console.error(e); }
});
