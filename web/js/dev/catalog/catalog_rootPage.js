;$(function() {
	var
		brandLinkActiveClass = 'active',
		brandTitleOpenedClass = 'opened',
		$selectedBrandsWrapper = $('.js-productCategory-rootPage-brands-selectedBrandsWrapper'),
		$brandLinks = $('.js-productCategory-rootPage-brands-link');

	function renderSelectedBrandsTemplate() {
		var $template = $('#root_page_selected_brands_tmpl');
		$selectedBrandsWrapper.html(Mustache.render($template.html(), {brandsCount: $brandLinks.length, selectedBrandsCount: $brandLinks.filter('.' + brandLinkActiveClass).length}, $template.data('partial')));
	}

	$brandLinks.click(function(e) {
		e.preventDefault();

		var
			$brandLink = $(e.currentTarget),
			url = document.location.href,
			brandLinkUrlParam = e.currentTarget.href.replace(/^.*?\?([^#]+)(\#.*)?$/, '$1'),
			brandLinkUrlParamName = decodeURIComponent(brandLinkUrlParam.replace(/^(.*?)\=.*$/, '$1')),
			brandLinkUrlParamValue = decodeURIComponent(brandLinkUrlParam.replace(/^.*?\=(.*)$/, '$1'));

		if ($brandLink.hasClass(brandLinkActiveClass)) {
			$brandLink.removeClass(brandLinkActiveClass);
			url = ENTER.utils.setURLParam(brandLinkUrlParamName, null, url);
		} else {
			$brandLink.addClass(brandLinkActiveClass);
			url = ENTER.utils.setURLParam(brandLinkUrlParamName, brandLinkUrlParamValue, url);
		}

		history.pushState({}, document.title, url);
		renderSelectedBrandsTemplate();

		$.ajax({
			url: url,
			type: 'GET',
			success: function(result){
				var
					$template = $('#root_page_links_tmpl'),
					$linksWrapper = $('.js-productCategory-rootPage-linksWrapper');

				$linksWrapper.html(Mustache.render($template.html(), {links: result.links, category: result.category}, $template.data('partial')));
			}
		});
	});


	$('.js-productCategory-rootPage-brands-else, .js-productCategory-rootPage-brands-title').click(function(e) {
		e.preventDefault();

		var
			$other = $('.js-productCategory-rootPage-brands-other'),
			$else = $('.js-productCategory-rootPage-brands-else'),
			$title = $('.js-productCategory-rootPage-brands-title');

		$other.toggle();

		if ($other.css('display') == 'none') {
			$else.show();
			$title.removeClass(brandTitleOpenedClass);
		} else {
			$else.hide();
			$title.addClass(brandTitleOpenedClass);
		}
	});

	$selectedBrandsWrapper.on('click', '.js-productCategory-rootPage-selectedBrands-clear', function(e) {
		e.preventDefault();

		$brandLinks.removeClass(brandLinkActiveClass);
		history.pushState({}, document.title, '?');
		renderSelectedBrandsTemplate();
	});
});