/**
 * ==========================
 * ========= FILTER =========
 * ==========================
 */
/**
 * @module      enter.catalog.filter
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.catalog.filter',
        [
            'jQuery',
            'enter.BaseViewClass',
            'urlHelper',
            'Mustache',
            'jquery.ui'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, urlHelper, mustache, jQueryUI ) {
        'use strict';

        var
            CatalogFilterView = BaseViewClass.extend({

                /**
                 * @classdesc   Представление каталога
                 * @memberOf    module:enter.catalog.filter~
                 * @augments    module:BaseViewClass
                 * @constructs  CatalogFilterView
                 */
                initialize: function( options ) {
                    console.info('CatalogFilterView initialized');
                    console.log(this);

                    this.catalogView = options.catalogView;
                    this.sliders     = this.$el.find('.js-category-filter-rangeSlider');

                    this.sliders.each(this.initSlider.bind(this));
                },

                events: {
                    'click .js-category-v2-filter-dropBox-opener': 'toggleDropdown',
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
                                sliderWrap      = slider.find('.js-category-filter-rangeSlider-slider'),
                                sliderConfig    = sliderWrap.data('config'),

                                sliderFromInput = slider.find('.js-category-filter-rangeSlider-from'),
                                sliderToInput   = slider.find('.js-category-v2-filter-element-price-to'),

                                sliderFromVal   = parseFloat(sliderFromInput.val()),
                                sliderToVal     = parseFloat(sliderToInput.val()),

                                // initalFromValue = parseFloat(sliderConfig.initalFromValue),
                                // initalToValue   = parseFloat(sliderConfig.initalToValue),

                                min             = sliderConfig.min,
                                max             = sliderConfig.max;
                            // end of vars


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

                    // url = this.addActiveSorting(url);

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
                        tickPercentage  = [20, 40, 60, 80],

                        slider          = $(element),
                        sliderWrap      = slider.find('.js-category-filter-rangeSlider-slider'),
                        tickWrap        = slider.find('.js-slider-tick-wrapper'),
                        config          = sliderWrap.data('config'),

                        fromVal         = slider.find('.js-category-filter-rangeSlider-from'),
                        toVal           = slider.find('.js-category-v2-filter-element-price-to'),

                        self            = this,

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
                    console.info('enter.catalog.filter~CatalogFilterView#toggleDropdown');

                    var
                        ddClass         = 'js-category-v2-filter-dropBox',
                        currentTarget   = $(event.currentTarget),
                        dropdowns       = this.$el.find('.' + ddClass),
                        currentDropdown = currentTarget.closest('.' + ddClass),
                        ddOpenClass     = 'opn',
                        isOpen          = currentDropdown.hasClass(ddOpenClass);

                    dropdowns.removeClass(ddOpenClass);

                    if ( !isOpen ) {
                        currentDropdown.addClass(ddOpenClass);
                    }
                },

                /**
                 * Хандлер изменения фильтра
                 *
                 * @method      filterChanged
                 * @memberOf    module:enter.catalog.filter~CatalogFilterView#
                 */
                filterChanged: function() {
                    console.info('enter.catalog.filter~CatalogFilterView#filterChanged');

                    this.catalogView.updateListing();

                    return false;
                }
            });

        provide(CatalogFilterView);
    }
);



/**
 * ===========================
 * ========= CATALOG =========
 * ===========================
 */

/**
 * @module      enter.catalog
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.catalog',
        [
            'enter.BaseViewClass',
            'enter.catalog.filter',
            'urlHelper',
            'history'
        ],
        module
    );
}(
    this.modules,
    function( provide, BaseViewClass, FilterView, urlHelper, History ) {
        'use strict';

        var
            CatalogView = BaseViewClass.extend({

                sortingActiveClass: 'act',

                /**
                 * @classdesc   Представление каталога
                 * @memberOf    module:enter.catalog~
                 * @augments    module:BaseViewClass
                 * @constructs  CatalogView
                 */
                initialize: function( options ) {
                    console.info('CatalogView initialized');
                    console.log(this);

                    this.subViews = {
                        filterView: new FilterView({
                            el: this.$el.find('.js-category-filter'),
                            catalogView: this
                        }),

                        sortings: this.$el.find('.js-category-sorting-item')
                    };

                    // Init History
                    History.Adapter.bind(window, 'statechange', this.history.stateChange);
                },

                events: {
                    'click .js-category-sorting-item': 'toggleSorting',
                    'click .js-category-pagination-infinity': 'toggleInfinityScroll'
                },

                /**
                 * Комплекс методов по работе с историей браузера
                 *
                 * @type  {Object}
                 */
                history: {
                    stateChange: function() {
                        var
                            state = History.getState();

                        console.info('history.statechange');
                        console.log(state);

                        return;
                    },

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

                        console.log('history.updateState');

                        if ( !History.enabled ) {
                            document.location.href = url;

                            return;
                        }

                        History.pushState(state, state.title, state.url);

                        return;
                    }
                },

                /**
                 * Переключение сортировок
                 *
                 * @method      toggleSorting
                 * @memberOf    module:enter.catalog~CatalogView#
                 */
                toggleSorting: function( event ) {
                    var
                        currentTarget = $(event.currentTarget),
                        sort          = currentTarget.attr('data-sort');

                    console.info('enter.catalog~CatalogView#toggleSorting');

                    if ( !currentTarget.hasClass(this.sortingActiveClass) ) {
                        this.subViews.sortings.removeClass(this.sortingActiveClass);
                        currentTarget.addClass(this.sortingActiveClass);
                        this.updateListing();
                    }

                    return false;
                },

                /**
                 * Переключение бесконечного скрола
                 *
                 * @method      toggleInfinityScroll
                 * @memberOf    module:enter.catalog~CatalogView#
                 */
                toggleInfinityScroll: function() {
                    console.info('enter.catalog~CatalogView#toggleInfinityScroll');

                    return false;
                },

                getActiveSorting: function() {
                    var
                        activeSort = this.subViews.sortings.filter('.' + this.sortingActiveClass);

                    return activeSort.find('.jsSorting').attr('data-sort');
                },

                updateListing: function() {
                    var
                        filterUrl = this.subViews.filterView.createFilterUrl(),
                        sorting   = this.getActiveSorting(),
                        url       = window.location.pathname + urlHelper.addParams(filterUrl, {
                            sort: sorting
                        });

                    this.history.updateState(url)

                    return false;
                }
            });

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
