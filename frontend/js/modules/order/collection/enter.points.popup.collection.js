/**
 * @module      enter.points.popup.collection
 * @version     0.1
 *
 * @requires    underscore
 * @requires    enter.BaseCollectionClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.points.popup.collection',
        [
            'underscore',
            'enter.BaseCollectionClass',
            'enter.order.point.model'
        ],
        module
    );
}(
    this.modules,
    function( provide, _, BaseCollectionClass, PointModel ) {
        'use strict';

        provide(BaseCollectionClass.extend(/** @lends module:enter.points.popup.collection~PointsPopupCollection */{
            model: PointModel,

            /**
             * @classdesc   Коллекция точек самовывоза
             * @memberOf    module:enter.points.popup.collection~
             * @augments    module:enter.BaseCollectionClass
             * @constructs  PointsPopupCollection
             */
            initialize: function() {
                console.info('module:enter.points.popup.collection~PointsPopupCollection#initialize');
            }
        }));
    }
);
