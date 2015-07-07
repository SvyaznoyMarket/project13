;(function($) {
	$(function(){
		$('body').on('click', '.jsFavoriteDeleteLink', function(e) {
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