/**
 * @module      enter.recommendations
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.recommendations',
        [
            'jQuery',
            'enter.recommendation.view',
            'jquery.slick'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, RecommendationView ) {
        'use strict';

        provide({
            init: function( el ) {
                var
                    $el         = $(el),
                    inited      = $el.prop('inited');

                if ( inited ) {
                    // console.warn('--- element %s initialized! ---', $el);
                    return;
                }

                $el.prop('inited', true);

                new RecommendationView({
                    el: $(el)
                })
            }
        });
    }
);
