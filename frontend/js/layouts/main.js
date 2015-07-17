+function(){

    modules.require(
        ['jQuery'],
        function($) {

            /**
             * lightbox не всегда нужен, поэтому запросим его только в случае необходимости
             */
            $('.js-popup-show').on('click', function( event ) {

                var current = $(this).data('popup');
                event.preventDefault();

                modules.require('jquery.lightbox_me', function(){
                    $('.js-popup-' + current ).lightbox_me({
                        closeSelector: '.js-popup-close',
                        onLoad: function() {
                            //$('.js-slider-goods').slick('reinit');
                        }
                    });
                });

            })
        }
    );

}();