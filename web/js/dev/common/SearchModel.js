;$(function($){
	var $body = $(document.body),
		searchUrl = '/search/autocomplete';

	function SearchModel(){
		var self = this;
		self.searchInput = ko.observable('');
		self.searchFocus = ko.observable(false);
		self.searchResults = ko.observableArray();

		self.advancedSearch = ko.observable(true);
		self.searchCategoryVisible = ko.observable(false);
		self.currentCategory = ko.observable(null);

		self.searchResultCategories = ko.observableArray();
		self.searchResultProducts = ko.observableArray();

		self.toggleCategoryVisibility = function(){
			self.searchCategoryVisible(!self.searchCategoryVisible());
		};

		self.categoryClick = function(data, event){
			var category = $(event.target).data('value');
			self.currentCategory(category);
			self.toggleCategoryVisibility();
		};
		self.categoryReset = function(){
			self.currentCategory(null);
			self.toggleCategoryVisibility();
		};

		self.searchInput.subscribe(function (val) {
			var params = {q: val, sender: 'knockout'};

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
		});

		return self;
	}

	// Биндинги на нужные элементы
	// Топбар, кнопка Купить на странице продукта, листинги, слайдер аксессуаров
	$body.find('.jsKnockoutSearch').each(function(){
		ko.applyBindings(new SearchModel(), this);
	});
}(jQuery));
