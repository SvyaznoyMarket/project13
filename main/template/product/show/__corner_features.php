<?
/**
 * @var $product       \Model\Product\ExpandedEntity
 *
 */

?>

<? if ($product->isInShopShowroomOnly()): ?>
    <div class="notBuying font12">
        <div class="corner"><div></div></div>
        Только на витрине
    </div>
<? elseif ($product->isInShopStockOnly()): ?>
    <div class="notBuying font12">
        <div class="corner"><div></div></div>
        Только в магазине
    </div>
<? elseif (!$product->getIsBuyable()): ?>
    <div class="notBuying font12">
        <div class="corner"><div></div></div>
        Нет в наличии
    </div>
<? endif ?>
