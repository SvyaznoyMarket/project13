;(function($){

    var $orderContent = $('#js-order-content');

    $orderContent.on('click', '.orderCol_data-count', function(e){
        var $this = $(this);
        e.stopPropagation();
        $this.hide().siblings('.orderCol_data-summ, .orderCol_data-price').hide();
        $this.siblings('.orderCol_data-edit').show();
    });

    $orderContent.on('click', '.bCountSection__eP, .bCountSection__eM', function(e){

        var $this = $(this),
            $input = $this.siblings('input'),
            stock = parseInt($input.data('stock'), 10),
            quantity = parseInt($input.val(), 10);

        if ($this.hasClass('bCountSection__eP')) {
            if (stock > quantity) $input.val(quantity + 1);
        }

        if ($this.hasClass('bCountSection__eM')) {
            if (quantity > 1) $input.val(quantity - 1);
        }

        e.preventDefault();
        e.stopPropagation();

    });

    $orderContent.on('change', '.bCountSection__eNum', function(e){
        e.stopPropagation();
    });


    function _counter() {

    }

    window.ENTER.OrderV3.constructors.counter = _counter;

}(jQuery));