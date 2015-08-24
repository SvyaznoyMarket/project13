/**
 * @module      deliveryPage.points.collection
 * @version     0.1
 *
 * @requires    underscore
 * @requires    enter.BaseCollectionClass
 * @requires    deliveryPage.point.model
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'deliveryPage.points.collection',
        [
            'underscore',
            'enter.BaseCollectionClass',
            'deliveryPage.point.model'
        ],
        module
    );
}(
    this.modules,
    function( provide, _, BaseCollectionClass, PointModel ) {
        'use strict';

        provide(BaseCollectionClass.extend(/** @lends module:deliveryPage.points.collection~PointsCollection */{
            model: PointModel,

            /**
             * @classdesc   Коллекция точек самовывоза
             * @memberOf    module:deliveryPage.points.collection~
             * @augments    module:enter.BaseCollectionClass
             * @constructs  PointsCollection
             */
            initialize: function( models, options ) {
                console.info('module:deliveryPage.points.collection~PointsCollection#initialize');
            },

            filterMyPoints: function( params ) {
                var
                    selectedPoints = [],
                    key;

                selectedPoints = this.filter(function( models ) {
                    for ( key in models.attributes ) {

                        if ( params[key] && params[key].length && models.attributes.hasOwnProperty(key) ) {
                            var
                                index = params[key].indexOf(models.attributes[key].toString());

                            if ( index === -1 ) {
                                models.set({'shown': false});

                                return false;
                            }
                        }
                    }

                    models.set({'shown': true});
                    return true;
                });

                return selectedPoints;
            }
        }));
    }
);
