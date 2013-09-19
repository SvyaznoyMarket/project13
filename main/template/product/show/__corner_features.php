<?
/**
 * @var $product       \Model\Product\ExpandedEntity
 *
 */

?>
<? if (!$product->getIsBuyable()) { ?>
    <div class="notBuying font12">
        <div class="corner"><div></div></div>
        Нет в наличии
    </div>
<? } ?>

<? if ($product->getIsInShopsOnly()): ?>
    <div class="notBuying font12">
        <div class="corner"><div></div></div>
        Только в магазине
    </div>
<? elseif ($product->getState()->getIsShop()): ?>
    <div class="notBuying font12">
        <div class="corner"><div></div></div>
        Только на витрине
    </div>
<? endif ?>
<?