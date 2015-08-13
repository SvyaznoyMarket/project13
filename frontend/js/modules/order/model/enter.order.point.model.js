/**
 * @module      enter.order.point.model
 * @version     0.1
 *
 * @requires    Backbone
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.point.model',
        [
            'Backbone'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone ) {
        'use strict';

        provide(Backbone.Model.extend(/** @lends module:enter.order.point.model~PointModel */{
            /**
             * @member      defaults
             * @memberOf    module:enter.order.point.model~PointModel
             * @type        {Object}
             */
            defaults: {
                shown: true
            },
            /**
             * @classdesc   Модель точки самовывоза
             * @memberOf    module:enter.order.point.model~
             * @augments    module:Backbone.Model
             * @constructs  PointModel
             */
            initialize: function() {}
        }));
    }
);
