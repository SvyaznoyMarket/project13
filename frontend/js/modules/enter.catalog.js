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
            'Mustache',
            'jquery.ui'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, mustache, jQueryUI ) {
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
            'enter.catalog.filter'
        ],
        module
    );
}(
    this.modules,
    function( provide, BaseViewClass, FilterView ) {
        'use strict';

        var
            CatalogView = BaseViewClass.extend({

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
                        })
                    };
                },

                events: {
                    'click .js-category-sorting-item': 'changeSorting',
                    'click .js-category-pagination-infinity': 'toggleInfinityScroll'
                },

                /**
                 * Переключение сортировок
                 *
                 * @method      changeSorting
                 * @memberOf    module:enter.catalog~CatalogView#
                 */
                changeSorting: function() {
                    console.info('enter.catalog~CatalogView#changeSorting');

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
