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
                DROPDOWN_OPENER: 'js-category-v2-filter-dropBox-opener',
                DROPDOWN_OPEN: 'opn',
                RANGE_SLIDER: 'js-category-filter-rangeSlider',
                SLIDER: 'js-category-filter-rangeSlider-slider',
                SLIDER_FROM: 'js-category-filter-rangeSlider-from',
                SLIDER_TO: 'js-category-filter-rangeSlider-to',
                SLIDER_TICK: 'js-slider-tick-wrapper',
                BRANDS: 'js-category-v2-filter-otherBrands',
                BRANDS_OPENER: 'js-category-v2-filter-otherBrandsOpener',
                BRANDS_OPEN: 'open',
                CLEAR_FILTER: 'filter-selected-clear'
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
                this.events['click .' + CSS_CLASSES.DROPDOWN_OPENER] = 'toggleDropdown';
                this.events['click .' + CSS_CLASSES.BRANDS_OPENER]   = 'toggleBrands';
                this.events['click .' + CSS_CLASSES.CLEAR_FILTER]    = 'clearFilter';

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

                    percent, res, html, i,

                    setSliderValues = function() {
                        var
                            from = parseFloat(fromVal.val()),
                            to   = parseFloat(toVal.val());

                        from = ( from < config.min ) ? config.min : from;
                        to   = ( to > config.max ) ? config.max : to;

                        fromVal.val(from);
                        toVal.val(to);

                        sliderWrap.slider('values', 0, from);
                        sliderWrap.slider('values', 1, to);
                    };

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

                fromVal.change(setSliderValues);
                toVal.change(setSliderValues);

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
             * Скрытие и раскрытие дропдаунов
             *
             * @method      toggleDropdown
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             *
             * @param       {jQuery.Event}      event
             */
            toggleDropdown: function( event ) {
                var
                    currentOpener   = $(event.currentTarget),
                    currentDropdown = currentOpener.parents('.' + CSS_CLASSES.DROPDOWN),
                    dropdowns       = this.$el.find('.' + CSS_CLASSES.DROPDOWN),
                    isOpen          = currentDropdown.hasClass(CSS_CLASSES.DROPDOWN_OPEN);

                dropdowns.removeClass(CSS_CLASSES.DROPDOWN_OPEN);

                if ( !isOpen ) {
                    currentDropdown.addClass(CSS_CLASSES.DROPDOWN_OPEN);
                }
            },

            /**
             * Очистка фильтров
             *
             * @method      toggleDropdown
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             *
             * @param       {jQuery.Event}      event
             */
            clearFilter: function( event ) {
                /**
                 * @todo тут надо что-то сделать)
                 */
                return false;
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
