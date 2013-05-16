<?php
/**
 * @var $page                   \View\Layout
 * @var $pager                  \Iterator\EntityPager
 * @var $product                \Model\Product\Entity
 * @var $isAjax                 bool
 * @var $productVideosByProduct array
 * @var $isAddInfo              bool
 **/
?>

<?php
$itemsPerRow = isset($itemsPerRow) ? $itemsPerRow : 3;
$hasLastLine = isset($hasLastLine) ? $hasLastLine : true;
?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
<div class="goodslist"<? if (4 == $itemsPerRow): ?>  style="width: 940px; float: none; margin: 0;"<? endif ?>>
<? endif ?>

    <? $i = 0; foreach ($pager as $product): $i++ ?>
        <?= $page->render('product/show/_compact', array('product' => $product, 'productVideos' => isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [], 'addInfo' => $isAddInfo?\Kissmetrics\Manager::getProductSearchEvent($product, $i, $pager->getPage()):[])) ?>
        <? if (!($i % $itemsPerRow) && ($i == $pager->count() ? $hasLastLine : true)): ?>
            <div class="clear"></div>
        <? endif ?>
    <? endforeach ?>

    <? if (($i % $itemsPerRow) && $hasLastLine): ?>
        <div class="clear"></div>
    <? endif ?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
</div>
<? endif ?>
