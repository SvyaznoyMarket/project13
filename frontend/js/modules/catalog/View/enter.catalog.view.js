/**
 * @module      enter.catalog.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    Mustache
 * @requires    docCookies
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
            'underscore',
            'Mustache',
            'docCookies',
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
    function( provide, $, _, mustache, docCookies, BaseViewClass, FilterView, urlHelper, History, replaceWithPush, jUpdate ) {
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
                PAGINATION_BTN: 'js-category-pagination-paging',
                INF_SCROLL_BTN: 'js-category-pagination-infinity',
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
            },

            INF_SCROLL_COOKIE = '1';


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
                    paginationBtn: this.$el.find('.' + CSS_CLASSES.PAGINATION_BTN),
                    infScrollBtn: this.$el.find('.' + CSS_CLASSES.INF_SCROLL),
                    paginationWrapper: this.$el.find('.' + CSS_CLASSES.PAGINATION_WRAPPER),
                    selectedFilters: this.$el.find('.' + CSS_CLASSES.SELECTED_FILTERS_WRAPPER)
                };

                // Init History
                History.Adapter.bind(window, 'statechange', this.history.stateChange.bind(this));

                // Check infinity scroll
                this.checkInfScroll();

                // Setup events
                this.events['click .' + CSS_CLASSES.SORTING]        = 'toggleSorting';
                this.events['click .' + CSS_CLASSES.INF_SCROLL_BTN] = 'enableInfScroll';
                this.events['click .' + CSS_CLASSES.PAGINATION_BTN] = 'disableInfScroll';
                this.events['click .' + CSS_CLASSES.PAGINATION]     = 'togglePage';

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
                this.subViews.paginationBtn.show();
                this.subViews.pagination.hide();
                this.subViews.infScrollBtn.addClass(CSS_CLASSES.INF_SCROLL_ACTIVE);

                docCookies.setItem('infScroll', INF_SCROLL_COOKIE, 4 * 7 * 24 * 60 * 60, '/' );
            },

            /**
             * Выключение бесконечного скролла
             *
             * @method      disableInfScroll
             * @memberOf    module:enter.catalog~CatalogView#
             */
            disableInfScroll: function() {
                this.subViews.infScrollBtn.removeClass(CSS_CLASSES.INF_SCROLL_ACTIVE);
                this.subViews.paginationBtn.hide();
                this.subViews.pagination.show();
                this.subViews.pagination.filter('[data-page="1"]').addClass(CSS_CLASSES.PAGINATION_ACTIVE);
                this.updateListing(1);

                docCookies.setItem('infScroll', 0, 4 * 7 * 24 * 60 * 60, '/' );
            },

            /**
             * Проверка, включен ли бесконечный скролл
             *
             * @method      checkInfScroll
             * @memberOf    module:enter.catalog~CatalogView#
             */
            checkInfScroll: function() {
                if ( docCookies.getItem( 'infScroll' ) === INF_SCROLL_COOKIE ) {
                    this.enableInfScroll();
                }
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
             * Формирования урл с учетом номера страницы, выбранной сортировки и фильтров
             *
             * @method      createUrl
             * @memberOf    module:enter.catalog~CatalogView#
             *
             * @param       {Number}      page  Номер страницы
             *
             * @return      {String}
             */
            createUrl: function( page ) {
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
                    url       = this.createUrl(page);

                this.history.updateState(url);
                this.subViews.wrapper.empty();

                return false;
            },

            /**
             * Загрузка следующей страницы листинга при включенном бесконечно скролле
             *
             * @method      loadNextPage
             * @memberOf    module:enter.catalog~CatalogView#
             */
            loadNextPage: function() {
                var
                    url;

                this.infScrollPage = (this.infScrollPage || 0) + 1;

                url  = this.createUrl(this.infScrollPage)

                console.info('enter.catalog~CatalogView#loadNextPage');
                console.log(url);

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
