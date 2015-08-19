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
            'enter.point.model'
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
            initialize: function( models, options ) {
                console.info('module:enter.points.popup.collection~PointsPopupCollection#initialize');

                this.popupData = options.popupData;
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
