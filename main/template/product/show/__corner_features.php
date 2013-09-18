<?
/**
 * @var $product       \Model\Product\ExpandedEntity
 *
 */

?>
<? if (!$product->getIsBuyable()) { ?>
    <? if ($product->getState()->getIsShop()):/* ?>
        <div class="notBuying font12">
            <div class="corner"><div></div></div>
            Только в магазинах
        </div>
    <? elseif ($product->getIsInShowroomsOnly()): */?>
        <div class="notBuying font12">
            <div class="corner"><div></div></div>
            На витрине
        </div>
    <? else: ?>
        <div class="notBuying font12">
            <div class="corner"><div></div></div>
            Нет в наличии
        </div>
    <? endif ?>
<? }