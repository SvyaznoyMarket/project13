/**
 * @module      enter.recommendation.view
 * @version     0.1
 *
 * @requires    enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.recommendation.view',
        [
            'jQuery',
            'App',
            'Backbone',
            'enter.BaseViewClass',
            'jquery.replaceWithPush'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, App, Backbone, BaseViewClass ) {
        'use strict';

        provide(BaseViewClass.extend({

            /**
             * @classdesc   Представление страницы сайта
             * @memberOf    module:enter.recommendation.view~
             * @augments    module:BaseViewClass
             * @constructs  RecommendationView
             */
            initialize: function( options ) {

                if (this.$el && this.$el.data('url')) {
                    this.get(this.$el.data('url'));
                }
            },

            get: function(url) {

                var self = this;

                $.get(url).done(function(data){
                    var content;
                    if (data.recommend && data.recommend[self.$el.data('slider').type]) {
                        content = data.recommend[self.$el.data('slider').type].content;
                        self.changeContent(content);
                    }
                });
            },

            changeContent: function(content) {
                this.$el.replaceWithPush(content);
            }

        }));
    }
);
