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
            'enter.BaseViewClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, BaseViewClass ) {
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

                    this.catalogView = options.catalogView
                },

                events: {
                    'change': 'filterChanged'
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
