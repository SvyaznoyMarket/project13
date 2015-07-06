$(function() {
	var $body = $('body');

	$body.on('click', '.js-listing-item-img, .js-listing-item-viewButton', function(e) {
		var
			$target = $(e.currentTarget),
			$item = $target.closest('.js-listing-item');

		var action = 'listing';
		if ($item.data('is-slot')) {
			action = 'listing-marketplace-slot';
		} else if ($item.data('is-only-from-partner')) {
			action = 'listing-marketplace';
		}

		$body.trigger('trackGoogleEvent', ['View', action, $target.is('.js-listing-item-img') ? 'image' : 'button']);
	});

	$('.js-slider').goodsSlider({
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);
		}
	});
});