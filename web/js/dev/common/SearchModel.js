;$(function($){
	var $body = $(document.body),
		searchUrl = '/search/autocomplete',
		switchCookie = JSON.parse(docCookies.getItem('switch'));

	function SearchModel(){
		var self = this;
		self.searchInput = ko.observable('');
		self.searchFocus = ko.observable(false);
		self.searchResults = ko.observableArray();

		self.currentCategory = ko.observable(null);

		self.searchResultCategories = ko.observableArray();
		self.searchResultProducts = ko.observableArray();

		self.isNoSearchResult = ko.computed(function(){
			return self.searchResultCategories().length == 0 && self.searchResultProducts().length == 0
		});

		self.searchResultNavigation = function(data, e) {
			var keycode = e.which,
				$links = $('.jsSearchbarResults a'),
				activeClass = 'searchdd_lk_iact',
				index = $links.index($links.filter('.'+activeClass));

			if (!self.isNoSearchResult()) {
				$links.removeClass(activeClass);
				switch (keycode) {
					case 13: // Enter key
						if (index > -1) {
							window.location.href = $links.eq(index).attr('href');
							return false;
						}
						break;
					case 38: // up key
                        if (index == -1) index = self.searchResults.length;
						$links.eq(index - 1).addClass(activeClass);
						break;
					case 40: // down key
						$links.eq(index + 1).addClass(activeClass);
						break
				}
			}

			return true;
		};

		// задержка для скрытия результатов поиска
		self.searchResultsVisibility = ko.computed(function(){
			return self.searchFocus()
		}).extend({throttle: 200});

		// Throttled ajax query
		ko.computed(function(){
			var val = self.searchInput();
			var params = {q: val};

			if (self.currentCategory() != null) params.catId = self.currentCategory().id;

			if (val.length < 3) return;

			// assuming jQuery
			$.get(searchUrl, params)
				.done(function (data) {
					self.searchResultCategories(data.result.categories);
					self.searchResultProducts(data.result.products);
				})
				.fail(function () {
					console.error("could not retrieve value from server");
				});
		}).extend({ throttle: 200 });

		return self;
	}

	// Биндинги на нужные элементы
	$body.find('.jsKnockoutSearch').each(function(){
		ko.applyBindings(new SearchModel(), this);
	});

	// Клик по категории в подсказке
	$body.on('click', '.jsSearchSuggestCategory', function(e){
		$body.trigger('trackGoogleEvent', [{
			category: 'search_string',
			action: 'suggest',
			label: 'category'
		}])
	});

	// Клик по продукте в подсказке
	$body.on('click', '.jsSearchSuggestProduct', function(e){
		$body.trigger('trackGoogleEvent', [{
			category: 'search_string',
			action: 'suggest',
			label: 'item'
		}])
	});
}(jQuery));
