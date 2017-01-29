/**
 * Adblender
 */
ANALYTICS.adblenderJS = function() {

    var $el = $('#adblenderJS'),
        data = $el.data('value');

    console.info('adblender', data);

    $LAB.script('//bn.adblender.ru/c/enter/all.js?' + Math.random());

    if ('orderV3.complete' === data.type) {
        $LAB.script('//bn.adblender.ru/c/enter/basket.js?' + Math.random()).wait(function() {
            var Adblender = window.Adblender || [];

            $.each(data.orders, function(i, item) {
                console.info('adblender.push', item);
                Adblender.push(item);
            });
        });
    } else if ('cart' === data.type) {
        $LAB.script('//bn.adblender.ru/c/enter/basket.js?' + Math.random());
    }
};
