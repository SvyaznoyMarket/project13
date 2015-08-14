/**
 * @module      enter.search
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.search',
        [
            'jQuery', 'ko', 'docCookies'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, ko, docCookies ) {
        'use strict';


        var $body = $(document.body),
            searchUrl = '/search/autocomplete',
            switchCookie = JSON.parse(docCookies.getItem('switch')),
            advSearchEnabled = switchCookie && switchCookie.adv_search == 'on';

        function SearchModel(){
            var self = this;
            self.searchInput = ko.observable('');
            self.searchFocus = ko.observable(false);
            self.searchResults = ko.observableArray();

            self.advancedSearch = ko.observable(advSearchEnabled);
            self.searchCategoryVisible = ko.observable(false);
            self.currentCategory = ko.observable(null);
            self.previousCategory = ko.observable(null);

            self.searchResultCategories = ko.observableArray();
            self.searchResultProducts = ko.observableArray();

            self.isNoSearchResult = ko.computed(function(){
                return self.searchResultCategories().length == 0 && self.searchResultProducts().length == 0
            });

            self.toggleCategoryVisibility = function(){
                self.searchCategoryVisible(!self.searchCategoryVisible());
            };

            self.searchResultNavigation = function(data, e) {
                var keycode = e.which,
                    $links = $('.jsSearchbarResults a'),
                    activeClass = 'active',
                    index = $links.index($links.filter('.'+activeClass));

                console.log(e);

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

            self.categoryClick = function(data, event){
                var category = $(event.target).data('value');
                self.currentCategory(category);
                self.toggleCategoryVisibility();
            };
            self.categoryReset = function(){
                self.currentCategory(null);
                self.toggleCategoryVisibility();
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
                        self.searchResultCategories(
                            // Вытаскиваем отдельно количество товаров
                            $.map(data.result.categories, function(elem){
                                var regex = /(.*?)\s\((\d+)\)/,
                                    result = elem.name.match(regex);
                                if (result.length == 3) elem.name = result[1] + '<span class="search-suggest-list__count"> &#8230;&#8230; ' + result[2] + '</span>' || elem.name;
                                return elem;
                            })
                        );
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


        provide({});
    }
);
