<?
/**
 * // var $products \Model\Product\CartEntity[] //disabled
 * var $cart_prods
 * from /main/view/DefaultLayout.php
 **/

?>
<? if ( is_array($cart_prods) and !empty($cart_prods) ): ?>
    <div id="sociomanticBasket" data-cart-prods="<?= $page->json($cart_prods) ?>" class="jsanalytics"></div>
<? endif;

/* examples:
{ identifier: '461-1177_msk', amount: 4990.00, currency: 'RUB', quantity: 1 },
{ identifier: '452-9682_msk', amount: 23990.00, currency: 'RUB', quantity: 1 }
*/
