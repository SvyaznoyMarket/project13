/**
 * @module      deliveryPage.point.model
 * @version     0.1
 *
 * @requires    Backbone
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'deliveryPage.point.model',
        [
            'underscore',
            'Backbone'
        ],
        module
    );
}(
    this.modules,
    function( provide, _, Backbone ) {
        'use strict';

        provide(Backbone.Model.extend(/** @lends module:deliveryPage.point.model~PointModel */{
            idAttribute: 'uid',

            /**
             * @member      defaults
             * @memberOf    module:deliveryPage.point.model~PointModel
             * @type        {Object}
             */
            defaults: {
                shown: true
            },
            /**
             * @classdesc   Модель точки самовывоза
             * @memberOf    module:deliveryPage.point.model~
             * @augments    module:Backbone.Model
             * @constructs  PointModel
             */
            initialize: function() {}
        }));
    }
);