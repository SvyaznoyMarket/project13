<?
/** @var $prod          array */
/** @var $prod_cats     array */
/** @var $cart_prods    array */


?>
<div id="smanticPageJS" <?
    if (!empty($prod))       { ?>   data-prod="<?=       $page->json($prod)      ?>" <? }
    if (!empty($prod_cats))  { ?>   data-prod-cats="<?=  $page->json($prod_cats) ?>" <? }
    if (!empty($cart_prods)) { ?>   data-cart-prods="<?= $page->json($cart_prods)?>" <? }
    ?> class="jsanalytics"></div>