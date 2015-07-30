/**
 * @module      enter.recommendations
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.recommendations',
        ['jQuery', 'enter.recommendation.view', 'jquery.slick'],
        module
    );
}(
    this.modules,
    function( provide, $, RecommendationView ) {
        'use strict';

        provide({
            init: function(el){
                new RecommendationView({
                    el: $(el)
                })
            }
        });
    }
);
