/**
 * Базовый класс представления
 *
 * @module      enter.BaseViewClass
 *
 * @requires    extendBackbone
 * @requires    underscore
 * @requires    ajaxCall
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.BaseViewClass',
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
             * @memberOf    module:enter.BaseViewClass~
             * @augments    module:Backbone.View
             * @constructs  BaseView
             */
            BaseView = function( options ) {
                /**
                 * Вложенные представления
                 *
                 * @member      subViews
                 * @memberOf    module:enter.BaseViewClass~BaseView
                 * @type        {Object}
                 */
                this.subViews = {};

                this.loader.hide = this.loader.hide.bind(this);
                this.loader.show = this.loader.show.bind(this);

                Backbone.View.apply(this, arguments);
            };


        _.extend(BaseView.prototype, {
            /**
             * Объект лоадера
             *
             * @member      loader
             * @memberOf    module:enter.BaseViewClass~BaseView
             * @type        {Object}
             */
            loader: {
                show: function() {},
                hide: function() {}
            },

            /**
             * Удаление преставлений всегда осуществляем через destroy
             *
             * @method      destroy
             * @memberOf    module:enter.BaseViewClass~BaseView#
             */
            destroy: function() {
                var
                    subView;

                for ( subView in this.subViews ) {
                    if ( this.subViews.hasOwnProperty(subView) ) {
                        if ( typeof this.subViews[subView].off === 'function' ) {
                            this.subViews[subView].off();
                        }

                        if ( typeof this.subViews[subView].destroy === 'function' ) {
                            this.subViews[subView].destroy();
                        } else if ( typeof this.subViews[subView].remove === 'function' ) {
                            this.subViews[subView].remove();
                        }

                        delete this.subViews[subView];
                    }
                }

                delete this.subViews;

                // COMPLETELY UNBIND THE VIEW
                this.undelegateEvents();
                this.stopListening();

                this.$el.removeData().unbind();
                this.$el.empty();

                // Remove view from DOM
                this.remove();
                Backbone.View.prototype.remove.call(this);
            }
        }, Backbone.View.prototype, ajaxCall);

        BaseView.extend = Backbone.View.extend;

        provide(BaseView);
    }
);
