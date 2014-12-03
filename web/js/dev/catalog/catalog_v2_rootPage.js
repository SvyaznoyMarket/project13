;$(function() {
	var
		brandLinkActiveClass = 'act',
		brandTitleOpenClass = 'opn',
		$selectedBrandsWrapper = $('.js-category-v2-root-brands-selectedBrandsWrapper'),
		$brandLinks = $('.js-category-v2-root-brands-link'),
		$otherBrands = $('.js-category-v2-root-brands-other'),
		$otherBrandsOpener = $('.js-category-v2-root-brands-otherOpener'),
		$brandsTitle = $('.js-category-v2-root-brands-title'),
		$linksWrapper = $('.js-category-v2-root-linksWrapper');

	function renderSelectedBrandsTemplate() {
		var $template = $('#root_page_selected_brands_tmpl');
		$selectedBrandsWrapper.html(Mustache.render($template.html(), {brandsCount: $brandLinks.length, selectedBrandsCount: $brandLinks.filter('.' + brandLinkActiveClass).length}, $template.data('partial')));
	}

	function updateLinks(url) {
		if (!ENTER.catalog.enableHistoryAPI) {
			document.location.href = url;
			return;
		}

		history.pushState({}, document.title, url);

		$.ajax({
			url: url,
			type: 'GET',
			success: function(result){
				var $template = $('#root_page_links_tmpl');
				$linksWrapper.html(Mustache.render($template.html(), {links: result.links, category: result.category}, $template.data('partial')));
			}
		});
	}

	$brandLinks.click(function(e) {
		e.preventDefault();

		var
			$brandLink = $(e.currentTarget),
			url = document.location.href,
			brandLinkParamName = $brandLink.data('paramName'),
			brandLinkParamValue = $brandLink.data('paramValue');

		if ($brandLink.hasClass(brandLinkActiveClass)) {
			$brandLink.removeClass(brandLinkActiveClass);
			url = ENTER.utils.setURLParam(brandLinkParamName, null, url);
		} else {
			$brandLink.addClass(brandLinkActiveClass);
			url = ENTER.utils.setURLParam(brandLinkParamName, brandLinkParamValue, url);
		}

		renderSelectedBrandsTemplate();
		updateLinks(url);
	});


	$brandsTitle.add($otherBrandsOpener).click(function(e) {
		e.preventDefault();

		$otherBrands.toggle();

		if ($otherBrands.css('display') == 'none') {
			$otherBrandsOpener.show();
			$brandsTitle.removeClass(brandTitleOpenClass);
		} else {
			$otherBrandsOpener.hide();
			$brandsTitle.addClass(brandTitleOpenClass);
		}
	});

	$selectedBrandsWrapper.on('click', '.js-category-v2-root-selectedBrands-clear', function(e) {
		e.preventDefault();

		$brandLinks.removeClass(brandLinkActiveClass);
		renderSelectedBrandsTemplate();
		updateLinks('?');
	});
});