;$(function() {
	var
		brandLinkActiveClass = 'act',
		brandTitleOpenClass = 'opn',
		$body = $(document.body),
		$selectedBrandsWrapper = $('.js-category-v2-root-brands-selectedBrandsWrapper'),
		$brandLinks = $('.js-category-v2-root-brands-link'),
		$otherBrands = $('.js-category-v2-root-brands-other'),
		$otherBrandsOpener = $('.js-category-v2-root-brands-otherOpener'),
		$brandsTitle = $('.js-category-v2-root-brands-title'),
		$linksWrapper = $('.js-category-v2-root-linksWrapper'),
		pageBusinessUnitId = ENTER.utils.getPageBusinessUnitId();

	function renderSelectedBrandsTemplate() {
		var $template = $('#root_page_selected_brands_tmpl');
		$selectedBrandsWrapper.html(Mustache.render($template.html(), {brandsCount: $brandLinks.length, selectedBrandsCount: $brandLinks.filter('.' + brandLinkActiveClass).length}, $template.data('partial')));
	}

	// Обновление списка категорий
	function updateLinks(params) {
		var url = ENTER.utils.generateUrl('product.category', $.extend(params, ENTER.config.pageConfig.request.route.attributes)) + document.location.hash;
		if (!History.enabled) {
			document.location.href = url;
			return;
		}

		history.pushState({}, document.title, url);

		$.ajax({
			url: ENTER.utils.generateUrl('ajax.product.category', $.extend(params, ENTER.config.pageConfig.request.route.attributes)) + document.location.hash,
			type: 'GET',
			success: function(result){
				var $template = $('#root_page_links_tmpl');
				$linksWrapper.html(Mustache.render($template.html(), {links: result.links, category: result.category}, $template.data('partial')));
			}
		});
	}

	// Нажатие на ссылки брендов
	$brandLinks.click(function(e) {
		e.preventDefault();

		var
			$brandLink = $(e.currentTarget),
			params = $.deparam((document.location.search || '').replace(/^\?/, '')),
			brandLinkParamName = $brandLink.data('paramName'),
			brandLinkParamValue = $brandLink.data('paramValue');

		if ($brandLink.hasClass(brandLinkActiveClass)) {
			$brandLink.removeClass(brandLinkActiveClass);
			delete params[brandLinkParamName];
		} else {
			$brandLink.addClass(brandLinkActiveClass);
			params[brandLinkParamName] = brandLinkParamValue;
		}

		renderSelectedBrandsTemplate();
		updateLinks(params);

		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'brand',
			label: pageBusinessUnitId
		});
	});

	// Сворачивание/разворачивание брендов
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

		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'brand',
			label: pageBusinessUnitId
		});
	});

	// Очистка выбранных брендов
	$selectedBrandsWrapper.on('click', '.js-category-v2-root-selectedBrands-clear', function(e) {
		e.preventDefault();

		$brandLinks.removeClass(brandLinkActiveClass);
		renderSelectedBrandsTemplate();

		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'brand',
			label: pageBusinessUnitId
		});

		updateLinks({});
	});

	// Выделение брендов, присутствующих в URL адресе
	$brandLinks.each(function(index, link) {
		var $brandLink = $(link);
		if (ENTER.utils.getURLParam($brandLink.data('paramName'), document.location.href) == $brandLink.data('paramValue')) {
			$brandLink.addClass(brandLinkActiveClass);
		}
	});
});