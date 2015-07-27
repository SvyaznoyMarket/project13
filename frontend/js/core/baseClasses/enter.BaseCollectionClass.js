/**
 * Базовый класс представления
 *
 * @module      enter.BaseCollectionClass
 *
 * @requires    extendBackbone
 * @requires    underscore
 * @requires    ajaxCall
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.BaseCollectionClass',
        [
            'extendBackbone',
            'underscore',
            'ajaxCall'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone, _, ajaxCall ) {
        'use strict';

        var
            /**
             * @classdesc   Класс базового представления
             * @memberOf    module:enter.BaseCollectionClass~
             * @augments    module:Backbone.Collection
             * @constructs  BaseCollection
             */
            BaseCollection = function( options ) {
                Backbone.Collection.apply(this, arguments);
            };


        _.extend(BaseCollection.prototype, {
            /**
             * User opts
             */
        }, Backbone.Collection.prototype, ajaxCall);

        BaseCollection.extend = Backbone.Collection.extend;

        provide(BaseCollection);
    }
);
