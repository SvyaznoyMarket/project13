$(function() {
	$('.js-category-filter-jewel-element-link').click(function(e) {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_pandora',
			action: $(e.currentTarget).closest('.js-category-filter-jewel-element').data('name'),
			label: ''
		});
	});

	$('.js-category-sorting-jewel-element-link').click(function(e) {
		ENTER.utils.sendSortEvent($(e.currentTarget).data('sort'), ENTER.config.pageConfig.category);
	});
});