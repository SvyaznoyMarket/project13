;(function($){

    var
        $orderWrapper = $('.js-order-wrapper'),
        minValue      = 1,
        maxValue      = 99;

    $orderWrapper.on('click', '.orderCol_data-count', function(e){
        var $this = $(this);
        e.stopPropagation();
        $this.hide().siblings('.orderCol_data-summ, .orderCol_data-price').hide();
        $this.siblings('.orderCol_data-edit').show();
    });

    function _counter() {

    }

    window.ENTER.OrderV3.constructors.counter = _counter;

}(jQuery));