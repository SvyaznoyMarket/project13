/**
 * ==========================
 * ========= FILTER =========
 * ==========================
 */
/**
 * @module      enter.catalog.filter
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    urlHelper
 * @requires    Mustache
 * @requires    jquery.ui
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.catalog.filter',
        [
            'jQuery',
            'Mustache',
            'enter.BaseViewClass',
            'urlHelper',
            'jquery.ui'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, mustache, BaseViewClass, urlHelper, jQueryUI ) {
        'use strict';

        var
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {
                DROPDOWN: 'js-category-v2-filter-dropBox',
                DROPDOWN_OPEN: 'opn',
                RANGE_SLIDER: 'js-category-filter-rangeSlider',
                SLIDER: 'js-category-filter-rangeSlider-slider',
                SLIDER_FROM: 'js-category-filter-rangeSlider-from',
                SLIDER_TO: 'js-category-filter-rangeSlider-to',
                SLIDER_TICK: 'js-slider-tick-wrapper',
                BRANDS: 'js-category-v2-filter-otherBrands',
                BRANDS_OPENER: 'js-category-v2-filter-otherBrandsOpener',
                BRANDS_OPEN: 'open'
            };

        provide(BaseViewClass.extend({

            /**
             * @classdesc   Представление каталога
             * @memberOf    module:enter.catalog.filter~
             * @augments    module:BaseViewClass
             * @constructs  CatalogFilterView
             */
            initialize: function( options ) {
                this.catalogView = options.catalogView;
                this.sliders     = this.$el.find('.' + CSS_CLASSES.RANGE_SLIDER);
                this.brands      = this.$el.find('.' + CSS_CLASSES.BRANDS);

                this.sliders.each(this.initSlider.bind(this));

                // Setup events
                this.events['click .' + CSS_CLASSES.DROPDOWN]      = 'toggleDropdown';
                this.events['click .' + CSS_CLASSES.BRANDS_OPENER] = 'toggleBrands';

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.catalog.filter~CatalogFilterView
             * @type        {Object}
             */
            events: {
                'change': 'filterChanged'
            },

            /**
             * Получение изменненых и неизменненых полей слайдеров
             *
             * @method      getSlidersInputState
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             *
             * @return      {Object}    res
             * @return      {Array}     res.changedSliders      Массив имен измененных полей
             * @return      {Array}     res.unchangedSliders    Массив имен неизмененных полей
             */
            getSlidersInputState: function() {
                var
                    res = {
                        changedSliders: [],
                        unchangedSliders: []
                    },

                    sortSliders = function() {
                        var
                            slider          = $(this),
                            sliderWrap      = slider.find('.' + CSS_CLASSES.SLIDER),
                            sliderConfig    = sliderWrap.data('config'),

                            sliderFromInput = slider.find('.' + CSS_CLASSES.SLIDER_FROM),
                            sliderToInput   = slider.find('.' + CSS_CLASSES.SLIDER_TO),

                            sliderFromVal   = parseFloat(sliderFromInput.val()),
                            sliderToVal     = parseFloat(sliderToInput.val()),

                            min             = sliderConfig.min,
                            max             = sliderConfig.max;


                        if ( sliderFromVal === min ) {
                            res.unchangedSliders.push(sliderFromInput.attr('name'));
                        } else {
                            res.changedSliders.push(sliderFromInput.attr('name'));
                        }

                        if ( sliderToVal === max ) {
                            res.unchangedSliders.push(sliderToInput.attr('name'));
                        } else {
                            res.changedSliders.push(sliderToInput.attr('name'));
                        }
                    };

                this.sliders.each(sortSliders);

                return res;
            },

            /**
             * Скрытие и открытие брендов
             *
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             * @method      toggleBrands
             */
            toggleBrands: function() {
                if ( this.brands.hasClass(CSS_CLASSES.BRANDS_OPEN) ) {
                    this.brands.removeClass(CSS_CLASSES.BRANDS_OPEN);
                } else {
                    this.brands.addClass(CSS_CLASSES.BRANDS_OPEN);
                }

                return false;
            },

            /**
             * Создание URL по текущим параметрам фильтрации
             *
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             * @method      createFilterUrl
             */
            createFilterUrl: function() {
                var
                    searchPhrase      = urlHelper.getURLParam('q'),
                    serialized        = this.$el.serializeArray(),
                    slidersInputState = this.getSlidersInputState(),
                    sliderFromRe      = /^from-(.*$)/,
                    sliderToRe        = /^to-(.*$)/,
                    tmpObj            = {},
                    outArray          = [],
                    url, key, i;


                // Remove default value sliders
                for ( i = serialized.length - 1; i >= 0; i-- ) {
                    if ( slidersInputState.unchangedSliders.indexOf(serialized[i].name) !== -1 ) {
                        serialized.splice(i,1);
                    }
                }

                for ( i = 0; i < serialized.length; i++ ) {

                    // Detect slider from
                    if ( serialized[i].name.match(sliderFromRe) ) {
                        serialized[i].name.replace(sliderFromRe, function( str, p1, offset, s) {
                            tmpObj[p1]      = tmpObj[p1] || {};
                            tmpObj[p1].from = serialized[i].value;
                        });

                        continue;
                    }

                    // Detect slider to
                    if ( serialized[i].name.match(sliderToRe) ) {
                        serialized[i].name.replace(sliderToRe, function( str, p1, offset, s) {
                            tmpObj[p1]    = tmpObj[p1] || {};
                            tmpObj[p1].to = serialized[i].value;
                        });

                        continue;
                    }

                    // If property not prepared
                    if ( !tmpObj.hasOwnProperty(serialized[i].name) ) {
                        tmpObj[serialized[i].name] = '';
                    }

                    tmpObj[serialized[i].name] = serialized[i].value;
                }

                url = urlHelper.addParams('', tmpObj);

                if ( searchPhrase ) {
                    url = urlHelper.addParams(url, {
                        q: searchPhrase
                    });
                }

                return url;
            },

            /**
             * Инициализация слайдера
             *
             * @method      initSlider
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             */
            initSlider: function( index, element ) {
                var
                    tickPercentage = [20, 40, 60, 80],

                    slider         = $(element),
                    sliderWrap     = slider.find('.' + CSS_CLASSES.SLIDER),
                    tickWrap       = slider.find('.' + CSS_CLASSES.SLIDER_TICK),
                    config         = sliderWrap.data('config'),

                    fromVal        = slider.find('.' + CSS_CLASSES.SLIDER_FROM),
                    toVal          = slider.find('.' + CSS_CLASSES.SLIDER_TO),

                    self           = this,

                    percent, res, html, i;

                sliderWrap.slider({
                    range: true,
                    step: config.step,
                    min: config.min,
                    max: config.max,
                    values: [
                        parseFloat(fromVal.val()),
                        parseFloat(toVal.val())
                    ],

                    slide: function( e, ui ) {
                        fromVal.val( ui.values[ 0 ] );
                        toVal.val( ui.values[ 1 ] );
                    },

                    change: function( e, ui ) {
                        self.filterChanged();
                    }
                });

                if ( !tickWrap.length ) {
                    return;
                }

                // Создание засечек на шкале
                percent = (config.max - config.min) / 100;

                for ( i = 0; i < tickPercentage.length; i++ ) {
                    res = config.min + tickPercentage[i] * percent;
                    res = ( config.step < 1 ) ? res : Math.round(res);

                    res = ( res.toFixed() != res ) ? res.toFixed(1) : res;

                    html = mustache.render(
                        '<span class="int" style="left: {{percentage}}%">{{value}}</span>',
                        {
                            percentage: tickPercentage[i],
                            value: res
                        }
                    );

                    tickWrap.append(html);
                }
            },

            /**
             * Пеервключе
             *
             * @method      toggleDropdown
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             *
             * @param       {jQuery.Event}      event
             */
            toggleDropdown: function( event ) {
                var
                    currentDropdown = $(event.currentTarget),
                    dropdowns       = this.$el.find('.' + CSS_CLASSES.DROPDOWN),
                    isOpen          = currentDropdown.hasClass(CSS_CLASSES.DROPDOWN_OPEN);

                dropdowns.removeClass(CSS_CLASSES.DROPDOWN_OPEN);

                if ( !isOpen ) {
                    currentDropdown.addClass(CSS_CLASSES.DROPDOWN_OPEN);
                }
            },

            /**
             * Хандлер изменения фильтра
             *
             * @method      filterChanged
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             */
            filterChanged: function() {
                this.catalogView.updateListing();

                return false;
            }
        }));
    }
);



/**
 * ===========================
 * ========= CATALOG =========
 * ===========================
 */

/**
 * @module      enter.catalog.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    Mustache
 * @requires    enter.BaseViewClass
 * @requires    enter.catalog.filter
 * @requires    urlHelper
 * @requires    history
 * @requires    jquery.replaceWithPush
 * @requires    jquery.update
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.catalog.view',
        [
            'jQuery',
            'Mustache',
            'enter.BaseViewClass',
            'enter.catalog.filter',
            'urlHelper',
            'history',
            'jquery.replaceWithPush',
            'jquery.update'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, mustache, BaseViewClass, FilterView, urlHelper, History, replaceWithPush, jUpdate ) {
        'use strict';

        var
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {
                CATALOG_WRAPPER: 'js-catalog-wrapper',
                SORTING: 'js-category-sorting-item',
                INF_SCROLL: 'js-category-pagination-infinity',
                INF_SCROLL_ACTIVE: 'act',
                SORTING_ACTIVE: 'act',
                CATALOG_FILTER: 'js-category-filter',
                PAGINATION_WRAPPER: 'js-category-pagination',
                PAGINATION: 'js-category-pagination-page',
                PAGINATION_ACTIVE: 'act',
                SELECTED_FILTERS_WRAPPER: 'js-category-filter-selected'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {
                LISTING_ITEM: $('#js-list-item-template').html(),
                PAGINATION: $('#js-pagination-template').html(),
                SELECTED_FILTERS: $('#js-list-selected-filter-template').html()
            };


        provide(BaseViewClass.extend({

            /**
             * @classdesc   Представление каталога
             * @memberOf    module:enter.catalog~
             * @augments    module:BaseViewClass
             * @constructs  CatalogView
             */
            initialize: function( options ) {
                this.subViews = {
                    filterView: new FilterView({
                        el: this.$el.find('.' + CSS_CLASSES.CATALOG_FILTER),
                        catalogView: this
                    }),

                    wrapper: this.$el.find('.' + CSS_CLASSES.CATALOG_WRAPPER),
                    sortings: this.$el.find('.' + CSS_CLASSES.SORTING),
                    pagination: this.$el.find('.' + CSS_CLASSES.PAGINATION),
                    infScroll: this.$el.find('.' + CSS_CLASSES.INF_SCROLL),
                    paginationWrapper: this.$el.find('.' + CSS_CLASSES.PAGINATION_WRAPPER),
                    selectedFilters: this.$el.find('.' + CSS_CLASSES.SELECTED_FILTERS_WRAPPER)
                };

                // Init History
                History.Adapter.bind(window, 'statechange', this.history.stateChange.bind(this));

                // Setup events
                this.events['click .' + CSS_CLASSES.SORTING]    = 'toggleSorting';
                this.events['click .' + CSS_CLASSES.INF_SCROLL] = 'toggleInfinityScroll';
                this.events['click .' + CSS_CLASSES.PAGINATION] = 'togglePage';

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.catalog~CatalogView
             * @type        {Object}
             */
            events: {},

            /**
             * Объект загрузчика. Передается в опциях в AJAX вызовы.
             *
             * @memberOf    module:enter.catalog~CatalogView
             * @type        {Object}
             */
            loader: {
                show: function() {
                    console.info('enter.catalog~CatalogView.loader#show');
                },

                hide: function() {
                    console.info('enter.catalog~CatalogView.loader#hide');
                }
            },

            /**
             * Комплекс методов по работе с историей браузера
             *
             * @memberOf    module:enter.catalog~CatalogView
             * @type        {Object}
             */
            history: {

                /**
                 * Обработчик изменения состояния истории браузера
                 *
                 * @method      stateChange
                 * @memberOf    module:enter.catalog~CatalogView.history
                 */
                stateChange: function() {
                    var
                        state = History.getState(),
                        ajaxUrl = urlHelper.addParams(state.url, {
                            ajax: true
                        });

                    console.info('history.statechange');
                    console.log(state);

                    this.disposeAjax();

                    this.ajax({
                        type: 'GET',
                        url: ajaxUrl,
                        loader: this.loader,
                        success: this.render.bind(this)
                    });

                    return;
                },

                /**
                 * Функция изменения состояния в истории браузера
                 *
                 * @method      stateChange
                 * @memberOf    module:enter.catalog~CatalogView.history
                 */
                updateState: function( url, callback, silent ) {
                    var
                        state = {
                            title: document.title,
                            url: url,
                            data: {
                                scrollTop: $(window).scrollTop(),
                                _silent: !!silent
                            }
                        };

                    if ( !History.enabled ) {
                        document.location.href = url;

                        return;
                    }

                    History.pushState(state, state.title, state.url);

                    return;
                }
            },

            /**
             * Формирование нового URL с учетом фильтров и активной сортировки.
             * При передачи номера страницы, так же подставит и ее.
             *
             * @method      createCatalogUrl
             * @memberOf    module:enter.catalog~CatalogView#
             *
             * @param       {Number}      page  Номер страницы
             *
             * @return      {String}
             */
            createCatalogUrl: function( page ) {
                var
                    filterUrl = this.subViews.filterView.createFilterUrl(),
                    sorting   = this.getActiveSorting();

                if ( page && !_.isNumber(page) ) {
                    page =  ''
                }

                return window.location.pathname + urlHelper.addParams(filterUrl, {
                    sort: sorting,
                    page: page
                });
            },

            /**
             * Переключение сортировок
             *
             * @method      toggleSorting
             * @memberOf    module:enter.catalog~CatalogView#
             *
             * @param       {jQuery.Event}      event
             */
            toggleSorting: function( event ) {
                var
                    currentTarget = $(event.currentTarget),
                    sort          = currentTarget.attr('data-sort');

                if ( !currentTarget.hasClass(CSS_CLASSES.SORTING_ACTIVE) ) {
                    this.subViews.sortings.removeClass(CSS_CLASSES.SORTING_ACTIVE);
                    currentTarget.addClass(CSS_CLASSES.SORTING_ACTIVE);
                    this.updateListing();
                }

                return false;
            },

            /**
             * Включение бесконечного скролла
             *
             * @method      enableInfScroll
             * @memberOf    module:enter.catalog~CatalogView#
             */
            enableInfScroll: function() {
                this.subViews.pagination.removeClass(CSS_CLASSES.PAGINATION_ACTIVE);
                this.subViews.infScroll.addClass(CSS_CLASSES.INF_SCROLL_ACTIVE);
            },

            /**
             * Выключение бесконечного скролла
             *
             * @method      disableInfScroll
             * @memberOf    module:enter.catalog~CatalogView#
             */
            disableInfScroll: function() {
                this.subViews.infScroll.removeClass(CSS_CLASSES.INF_SCROLL_ACTIVE);
                this.subViews.pagination.filter('[data-page="1"]').addClass(CSS_CLASSES.PAGINATION_ACTIVE);
            },

            /**
             * Переключение бесконечного скрола
             *
             * @method      toggleInfinityScroll
             * @memberOf    module:enter.catalog~CatalogView#
             */
            toggleInfinityScroll: function() {
                console.info('enter.catalog~CatalogView#toggleInfinityScroll');

                if ( !this.subViews.infScroll.hasClass(CSS_CLASSES.INF_SCROLL_ACTIVE) ) {
                    this.enableInfScroll();
                } else {
                    this.disableInfScroll();
                }

                return false;
            },

            /**
             * Переключение страницы
             *
             * @method      togglePage
             * @memberOf    module:enter.catalog~CatalogView#
             *
             * @param       {jQuery.Event}      event
             */
            togglePage: function( event ) {
                var
                    currentTarget = $(event.currentTarget),
                    page          = parseInt(currentTarget.attr('data-page'), 10);

                if ( !currentTarget.hasClass(CSS_CLASSES.PAGINATION_ACTIVE) ) {
                    this.subViews.pagination.removeClass(CSS_CLASSES.PAGINATION_ACTIVE);
                    this.subViews.pagination.filter('[data-page="' + page + '"]').addClass(CSS_CLASSES.PAGINATION_ACTIVE);
                    this.updateListing(page);
                }

                return false;
            },

            /**
             * Получение текущей активной сортировки
             *
             * @method      getActiveSorting
             * @memberOf    module:enter.catalog~CatalogView#
             */
            getActiveSorting: function() {
                var
                    activeSort = this.subViews.sortings.filter('.' + CSS_CLASSES.SORTING_ACTIVE);

                return activeSort.attr('data-sort');
            },

            /**
             * Вызов обновления листинга.
             * Формирование нового URL с учетом фильтров и активной сортировки.
             * При передачи номера страницы, так же подставит и ее.
             * Сформированный URL отправляет в history.updateState
             *
             * @method      updateListing
             * @memberOf    module:enter.catalog~CatalogView#
             *
             * @param       {Number}      page  Номер страницы
             */
            updateListing: function( page ) {
                var
                    filterUrl = this.subViews.filterView.createFilterUrl(),
                    sorting   = this.getActiveSorting(),
                    url       = '';

                if ( page && !_.isNumber(page) ) {
                    page =  ''
                }

                url = window.location.pathname + urlHelper.addParams(filterUrl, {
                    sort: sorting,
                    page: page
                });

                this.history.updateState(url);
                this.subViews.wrapper.empty();

                return false;
            },

            /**
             * Вызов отрисовки листинга
             *
             * @method      render
             * @memberOf    module:enter.catalog~CatalogView#
             */
            render: function( data ) {
                var
                    renderProducts = function( products ) {
                        var
                            i, html = '';

                        for ( i = 0; i < products.length; i++ ) {
                            html += mustache.render(TEMPLATES.LISTING_ITEM, products[i]);
                        }

                        return html;
                    },

                    renderPagination = function( pagination ) {
                        return mustache.render(TEMPLATES.PAGINATION, pagination);
                    },

                    renderSelectedFilters = function( selectedFilters ) {
                        return mustache.render(TEMPLATES.SELECTED_FILTERS, selectedFilters);
                    },

                    productsHtml, paginationHtml, selectedFiltersHtml;

                console.info('enter.catalog~CatalogView#render');

                // Validation
                if ( !_.isObject(data) || !_.isObject(data.list) || !_.isArray(data.list.products) || !data.list.products.length ) {
                    console.warn('Render empty listing');
                    // render error
                    return;
                }

                productsHtml        = renderProducts(data.list.products);
                paginationHtml      = renderPagination(data.pagination);
                selectedFiltersHtml = renderSelectedFilters(data.selectedFilter);

                this.subViews.paginationWrapper.replaceWithPush(paginationHtml);
                this.subViews.selectedFilters.empty()
                this.subViews.selectedFilters.html(selectedFiltersHtml);
                this.subViews.pagination.update();
                this.subViews.wrapper.html(productsHtml);

                this.delegateEvents();
            }
        }));
    }
);


/**
 * Модуль инициализации каталога. Создает экземпляр класса CatalogView
 *
 * @module      enter.catalog
 * @version     0.1
 *
 * @requires    enter.catalog.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.catalog',
        [
            'enter.catalog.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, CatalogView ) {
        'use strict';

        provide({
            init: function( el ) {
                var
                    catalog = new CatalogView({
                        el: el
                    });
            }
        });
    }
);
