;(function($) {
    var $phoneInput = $('.jsMobiDengiPhoneInput:first'),
        $form = $('.jsMobiDengiForm:first'),
        mask;

    if ($phoneInput.length == 0) return;

    $.mask.definitions['0'] = '[0-9]';
    $phoneInput.mask('+7 (000) 000-00-00');

    $.fn.center = function () {
        this.css("position","fixed");
        this.css("top", ($(window).height() / 2) - (this.outerHeight() / 2));
        this.css("left", ($(window).width() / 2) - (this.outerWidth() / 2));
        return this;
    };

    $form.on('submit', function(e){
        e.preventDefault();
        if (!/\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}/.test($phoneInput.val())) {
            console.error('Form validation error');
            return;
        }
        $.ajax({
            type: 'POST',
            data: {
                phone: $phoneInput.val()
            }
        }).done(function(data){
            console.log('Response', data);
/*            if (data.result && data.result.code) {
                switch (data.result.code) {
                    case 200: break;
                    case 201: break;
                }
            }*/
            $(document.body).append(data.result);
            $('.js-wrapper').center();
        }).fail(function(jqXHR) {
            var error = JSON.parse(jqXHR.responseText);
            console.log('Error data', error, jqXHR)
        })
    });

    $(document.body).on('click', '.jsCloseModal', function(e){
        e.preventDefault();
        $('.jsModal').remove()
    })

}(jQuery));
