/**
 * @module      enter.region
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.region',
        [
            'jQuery',
            'jquery.slick',
            'jquery.lightbox_me'
        ],
        module
    );
}(
    this.modules,
    function( provide ) {
        'use strict';


        // Первоначальная загрузка

        var $body = $(document.body), $popup, $slider, module = {};

        function queryAutocompleteVariants(term, onSuccess) {
            $.ajax({
                url: $popup.data('autocomplete-url'),
                dataType: 'json',
                data: {
                    q: term
                },
                success: function( data ) {
                    if (onSuccess) {
                        onSuccess(data.data.slice(0, 15));
                    }
                }
            });
        }

        module.show = function(){
            $popup.lightbox_me({
                closeSelector: '.js-popup-close'
            });
        };

        module.init = function(){

            $popup = $('.js-popup-region');

            // Слайдер
            $slider = $popup.find('.js-slider-goods-region-list');
            $slider.slick($slider.data('slick'));

            // Показываем остальные города
            $('.js-region-show-more-cities').on('click', function(e){
                e.preventDefault();
                $('.js-region-more-cities-wrapper').toggleClass('show');
                $slider.slick('reinit'); // потому что слайдер изначально не может выставить правильные css-значения для скрытого div-а
            });

            // Автокомплит
            $popup.find('#jscity').on('focus', function(){
                var $input = $(this),
                    $autocompleteResults = $('.js-region-autocomplete-results');

                modules.require('jquery.ui', function(){

                    /**
                     * Настройка автодополнения поля для ввода региона
                     */
                    $input.autocomplete( {
                        autoFocus: true,
                        appendTo: '.js-region-autocomplete-results',
                        source: function( request, response ) {
                            queryAutocompleteVariants(request.term, function(res) {
                                response( $.map( res, function( item ) {
                                    return {
                                        label: item.name,
                                        value: item.name,
                                        url: item.url
                                    };
                                }));
                            });
                        },
                        minLength: 2,
                        select: function( event, ui ) {
                            if (ui.item) {
                                $popup.find('.js-region-change-form').attr('action', ui.item.url);
                            }
                        },
                        open: function() {
                            $autocompleteResults.show()
                        },
                        close: function() {
                            $autocompleteResults.hide()
                        }
                    })
                        .autocomplete('instance')._renderItem = function( ul, item ) {
                        return $( '<li class="region-suggest-list__item">' )
                            .append( '<a class="region-suggest-list__link">' + item.label + '</a>' )
                            .appendTo( ul );
                    };
                })
            })
        };

        module.fillInput = false;

        $.get('/region/init')
            .done(function (res) {
                if (res.result) {
                    var $result = $(res.result),
                        region = $result.data('region'),
                        $form =  $result.find('.js-region-change-form');
                    $body.append($result);
                    module.init();
                    if (module.fillInput) {
                        $result.find('#jscity').val(region.name);
                        $form.attr('action', $form.attr('action') + region.id);
                    }
                    module.show();
                }
            });


        provide(module);
    }
);
