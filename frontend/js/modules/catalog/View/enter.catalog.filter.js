/**
 * @module      enter.catalog.filter
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    Mustache
 * @requires    enter.BaseViewClass
 * @requires    urlHelper
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
            overlayTransparent = $('.js-overlay-transparent'),
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {
                DROPDOWN: 'js-category-filter-dropBox',
                DROPDOWN_OPENER: 'js-category-filter-dropBox-opener',
                DROPDOWN_OPEN: 'opn',
                DROPDOWN_ACTIVE: 'actv',
                RANGE_SLIDER: 'js-category-filter-rangeSlider',
                RANGE_SLIDER_PRICE: 'js-category-filter-rangeSlider-price',
                SLIDER: 'js-category-filter-rangeSlider-slider',
                SLIDER_FROM: 'js-category-filter-rangeSlider-from',
                SLIDER_TO: 'js-category-filter-rangeSlider-to',
                SLIDER_TICK: 'js-slider-tick-wrapper',
                BRANDS: 'js-category-filter-otherBrands',
                BRANDS_OPENER: 'js-category-filter-otherBrandsOpener',
                BRANDS_OPEN: 'open',
                CLEAR_FILTER: 'js-filter-clear',
                FILTER_GROUP_NAME: 'js-category-filter-param',
                FILTER_GROUP_NAME_ACTIVE: 'mActive',
                FILTER_GROUP: 'js-category-filter-group',
                FILTER_GROUP_HIDE: 'hf',
                SILECT_PRICE_RANGE: 'js-filter-select-price-range'
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

                this.subViews = {
                    filterGroups: this.$el.find('.' + CSS_CLASSES.FILTER_GROUP),
                    filterGroupsName: this.$el.find('.' + CSS_CLASSES.FILTER_GROUP_NAME),
                    dropDowns: this.$el.find('.' + CSS_CLASSES.DROPDOWN)
                };

                this.sliders.each(this.initSlider.bind(this));

                this.dropdownCloseBinded = this.dropdownClose.bind(this);
                overlayTransparent.on('click', this.dropdownCloseBinded);

                // Setup events
                this.events['click .' + CSS_CLASSES.DROPDOWN_OPENER]    = 'toggleDropdown';
                this.events['click .' + CSS_CLASSES.BRANDS_OPENER]      = 'toggleBrands';
                this.events['click .' + CSS_CLASSES.CLEAR_FILTER]       = 'clearFilter';
                this.events['click .' + CSS_CLASSES.FILTER_GROUP_NAME]  = 'selectFilterGroup';
                this.events['click .' + CSS_CLASSES.SILECT_PRICE_RANGE] = 'selectPriceRange';

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
                'change': 'filterChanged',
                'click .js-overlay-transparent': 'dropdownClose',
            },

            selectPriceRange: function( event ) {
                var
                    target     = $(event.currentTarget),
                    from       = target.attr('data-from'),
                    to         = target.attr('data-to'),
                    slider     = this.subViews.priceSlider,
                    sliderWrap, fromVal, toVal, config;

                if ( !slider ) {
                    return false;
                }

                sliderWrap = slider.find('.' + CSS_CLASSES.SLIDER);
                fromVal    = slider.find('.' + CSS_CLASSES.SLIDER_FROM);
                toVal      = slider.find('.' + CSS_CLASSES.SLIDER_TO);
                config     = sliderWrap.data('config');

                from = ( from < config.min ) ? config.min : from;
                to   = ( to > config.max ) ? config.max : to;

                fromVal.val(from);
                sliderWrap.slider('values', 0, from);

                toVal.val(to);
                sliderWrap.slider('values', 1, to);

                return false;
            },

            selectFilterGroup: function( event ) {
                var
                    target = $(event.currentTarget),
                    index  = target.index();

                if ( target.hasClass(CSS_CLASSES.FILTER_GROUP_NAME_ACTIVE) ) {
                    return false;
                }

                this.subViews.filterGroups.addClass(CSS_CLASSES.FILTER_GROUP_HIDE);
                this.subViews.filterGroups.eq(index).removeClass(CSS_CLASSES.FILTER_GROUP_HIDE);

                this.subViews.filterGroupsName.removeClass(CSS_CLASSES.FILTER_GROUP_NAME_ACTIVE);
                target.addClass(CSS_CLASSES.FILTER_GROUP_NAME_ACTIVE);

                return false;
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
                    isPrice        = slider.hasClass(CSS_CLASSES.RANGE_SLIDER_PRICE),
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

                    change: self.filterChanged.bind(self)
                });

                fromVal.change(setSliderValues);
                toVal.change(setSliderValues);

                if ( isPrice ) {
                    this.subViews.priceSlider = slider;
                }

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
                overlayTransparent.hide();

                if ( !isOpen ) {
                    currentDropdown.addClass(CSS_CLASSES.DROPDOWN_OPEN);
                    overlayTransparent.show();
                }
            },

            dropdownClose: function() {
                this.subViews.dropDowns.removeClass(CSS_CLASSES.DROPDOWN_OPEN);
                overlayTransparent.hide();
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
                var
                    target = $(event.currentTarget),
                    url    = target.attr('href');

                this.catalogView.clearListingAndLoadNew(url);

                return false;
            },

            /**
             * Очистка формы
             *
             * @method      resetForm
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             */
            resetForm: function() {
                var
                    resetRadio = function resetRadio( nf, input ) {
                        var
                            self  = $(input),
                            id    = self.attr('id'),
                            label = this.$el.find('label[for="'+id+'"]');

                        self.prop('checked', false);
                        // self;
                    },

                    resetCheckbox = function resetCheckbox( nf, input ) {
                        $(input).prop('checked', false);
                    },

                    resetSliders = function resetSliders() {
                        var
                            sliderWrap      = $(this),
                            slider          = sliderWrap.find('.' + CSS_CLASSES.SLIDER),
                            sliderFromInput = sliderWrap.find('.' + CSS_CLASSES.SLIDER_FROM),
                            sliderToInput   = sliderWrap.find('.' + CSS_CLASSES.SLIDER_TO);

                        sliderFromInput.val(slider.slider('option', 'min'));
                        sliderToInput.val(slider.slider('option', 'max'));
                    },

                    resetText = function( nf, input ) {
                        $(input).val('');
                    };

                this.subViews.dropDowns.removeClass(CSS_CLASSES.DROPDOWN_ACTIVE);
                this.$el.find(':input:radio:checked').each(resetRadio.bind(this));
                this.$el.find(':input:checkbox:checked').each(resetCheckbox.bind(this));
                this.$el.find(':input:text:not(.js-category-filter-rangeSlider-from):not(.js-category-filter-rangeSlider-to)').each(resetText.bind(this));
                this.sliders.each(resetSliders);
            },

            /**
             * Обновление значений выбранных фильтров
             *
             * @method      update
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             */
            update: function() {
                var
                    values = urlHelper.getURLParams(window.location.href),
                    self   = this,

                    input, val, type, fieldName,

                    updateInput = {
                        'text': function( input, val ) {
                            var
                                dropdown = input.parents('.' + CSS_CLASSES.DROPDOWN);

                            input.val(val);
                            dropdown.addClass(CSS_CLASSES.DROPDOWN_ACTIVE);
                        },

                        'radio': function( input, val ) {
                            var
                                target   = input.filter('[value="'+val+'"]'),
                                dropdown = target.parents('.' + CSS_CLASSES.DROPDOWN),
                                id       = target.attr('id'),
                                label    = self.$el.find('label[for="'+id+'"]');

                            target.prop('checked', true);
                            dropdown.addClass(CSS_CLASSES.DROPDOWN_ACTIVE);
                        },

                        'checkbox': function( input, val ) {
                            var
                                target   = input.filter('[value="'+val+'"]'),
                                dropdown = target.parents('.' + CSS_CLASSES.DROPDOWN);

                            target.prop('checked', true);
                            dropdown.addClass(CSS_CLASSES.DROPDOWN_ACTIVE);
                        }
                    };

                this.resetForm();

                for ( fieldName in values ) {
                    if ( !values.hasOwnProperty(fieldName) ) {
                        return;
                    }

                    input = this.$el.find('input[name="' + fieldName + '"]');
                    val   = values[fieldName];
                    type  = input.attr('type');

                    if ( updateInput.hasOwnProperty(type) ) {
                        updateInput[type](input, val);
                    }
                }
            },

            /**
             * Хандлер изменения фильтра
             *
             * @method      filterChanged
             * @memberOf    module:enter.catalog.filter~CatalogFilterView#
             */
            filterChanged: function( event ) {
                var
                    target   = $(event.target),
                    dropdown = target.parents('.' + CSS_CLASSES.DROPDOWN),
                    inputs   = $('input, select, textarea', dropdown),
                    isSelected;

                inputs.each(function(index, element) {
                    var $element = $(element);
                    if (
                        ($element.is('input[type="text"], textarea') && ('' != $element.val() || (null != $element.data('min') && $element.val() != $element.data('min')) || (null != $element.data('max') && $element.val() != $element.data('max'))))
                        || ($element.is('input[type="checkbox"], input[type="radio"]') && $element[0].checked)
                        || ($element.is('select') && null != $element.val())
                    ) {
                        isSelected = true;
                        return false;
                    }
                });

                if ( isSelected ) {
                    dropdown.addClass(CSS_CLASSES.DROPDOWN_ACTIVE);
                } else {
                    dropdown.removeClass(CSS_CLASSES.DROPDOWN_ACTIVE);
                }

                this.catalogView.updateListing();

                return false;
            }
        }));
    }
);
