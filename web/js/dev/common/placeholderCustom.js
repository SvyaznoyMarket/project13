/**
 * Created by alexandr.anpilogov on 29.02.16.
 */
(function () {
    var $body = $('body'),
        $inputs     = $('.js-input-custom-plaseholder'),
        lblPosition = function () {
            var $this  = $(this),
                $label = $this.parent().find('.js-placeholder');

            if ( $this.is(":focus") || ($this.val() !== '') ) {
                $label.addClass('top');
            } else {
                $label.removeClass('top');
            }
        };

    //$.each($inputs, lblPosition);
    $(document).ready(function(){
        $.each($inputs, lblPosition);
    });

    $body.on('focus', '.js-input-custom-plaseholder', function(){
        $.each($inputs, lblPosition);
    });
    $body.on('blur', '.js-input-custom-plaseholder', function(){
        $.each($inputs, lblPosition);
    });

    $body.on('input', '.js-input-custom-plaseholder', function(){
        setTimeout(function(){
            $.each($inputs, lblPosition);
        }, 300);
    });
})();