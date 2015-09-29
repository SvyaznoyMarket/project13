;(function($) {
	$(function(){
		var
			$body = $('body');

		$body.on('click', '.js-toggle-list', function( e ) {
			console.log('tick');
			var
				collapsed = 'collapsed',
				expanded  = 'expanded',
				$el       = $(e.currentTarget),
				parent    = $el.parents('.js-favorite-container');

			if ( parent.hasClass(expanded) ) {
				parent.removeClass(expanded).addClass(collapsed);
			} else {
				parent.removeClass(collapsed).addClass(expanded);
			}

			return;
		});

		$body.on('click', '.jsFavoriteDeleteLink', function(e) {
			var
				$el = $(e.currentTarget),
				xhr = $el.data('xhr')
				;

			console.info({'.jsFavoriteDeleteLink click': $el});

			if ($el.data('ajax')) {
				e.stopPropagation();

				try {
					if (xhr)  xhr.abort();
				} catch (error) { console.error(error); }

				xhr = $.post($el.attr('href'))
					.done(function(response) {
						if (response.success) {
							$($el.data('target')).remove();
						}
					})
					.always(function() {
						$el.data('xhr', null);
					})
				;
				$el.data('xhr', xhr);

				e.preventDefault();
			}
		});
	});
}(jQuery));