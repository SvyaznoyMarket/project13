/**
 * Базовый класс представления
 *
 * @module      enter.BaseModelClass
 *
 * @requires    extendBackbone
 * @requires    underscore
 * @requires    ajaxCall
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.BaseModelClass',
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
             * @memberOf    module:enter.BaseModelClass~
             * @augments    module:Backbone.Model
             * @constructs  BaseModel
             */
            BaseModel = function( options ) {
                Backbone.Model.apply(this, arguments);
            };


        _.extend(BaseModel.prototype, {
            /**
             * User opts
             */
        }, Backbone.Model.prototype, ajaxCall);

        BaseModel.extend = Backbone.Model.extend;

        provide(BaseModel);
    }
);
