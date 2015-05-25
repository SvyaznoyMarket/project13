+function($){

    var $supplierLoginButton = $('.jsSupplierLoginButton'),
        $authPopup = $('#auth-block'),
        authClass = 'supplier-login';

    console.log($);

    $supplierLoginButton.on('click', function(){
        $authPopup.addClass(authClass);
        $authPopup.lightbox_me({
            onClose: function() {
                $authPopup.removeClass(authClass)
            }
        })
    })

}(jQuery);