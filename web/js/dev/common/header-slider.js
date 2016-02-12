/**
 * Created by alexandr.anpilogov on 12.02.16.
 */

;(function($){
    var $body = $(body);

    console.log('pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1');
    alert('pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1pkfhdjivdfkjn1');

    $body.on('click', function(e){
        e.preventDefault();

        var $this = $(this),
            container = $this.closest($('.js-header-slider')),
            item = container.find($('.js-header-slider-item')),
            itemW = item.outerWidth(),
            itemN = item.length;

        console.log(itemW, itemN);

/*        if($this.hasClass('js-header-slider-btn-prev')){

        }*/
    });
})(jQuery);