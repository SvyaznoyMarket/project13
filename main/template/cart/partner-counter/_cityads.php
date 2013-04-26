<?php
/**
 * @var $user    \Session\User
 */
?>

<?
$productIds = [];
$productQuantities = [];
foreach ($user->getCart()->getProducts() as $cartProduct) {
    $productIds[] = $cartProduct->getId();
    $productQuantities[] = $cartProduct->getQuantity();
}
?>

<script id="xcntmyAsync" type="text/javascript">
    /*<![CDATA[*/
    // стр. корзины
    var xcnt_basket_products = '<?= implode(',', $productIds) ?>'; // где XX,YY,ZZ – это ID товаров в корзине через запятую.
        var xcnt_basket_quantity = '<?= implode(',', $productQuantities) ?>'; // где X,Y,Z – это количество соответствующих товаров (опционально).
        /*]]>*/
        (function(){
            var xscr = document.createElement( 'script' );
            var xcntr = escape(document.referrer); xscr.async = true;
            xscr.src = ( document.location.protocol === 'https:' ? 'https:' : 'http:' )
            + '//x.cnt.my/async/track/?r=' + Math.random();
            var x = document.getElementById( 'xcntmyAsync' );
            x.parentNode.insertBefore( xscr, x );
        }());
</script>
