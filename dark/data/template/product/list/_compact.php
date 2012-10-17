<?php
/**
 * @var $page \View\DefaultLayout
 * @var $pager \Iterator\EntityPager
 * @var $product \Model\Product\Entity
 * @var $isAjax bool
 * */
?>

<?php
$itemsPerRow = isset($itemsPerRow) ? $itemsPerRow : 3;
$hasLastLine = isset($hasLastLine) ? $hasLastLine : true;
?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
<div class="goodslist"<? if (4 == $itemsPerRow): ?>  style="width: 940px; float: none; margin: 0;"<? endif ?>>
<? endif ?>

    <? $i = 0; foreach ($pager as $product): $i++ ?>
        <?= $page->render('product/show/_compact', array('product' => $product)) ?>
        <? if (!($i % $itemsPerRow) && ($i == $pager->count() ? $hasLastLine : true) && !$isAjax): ?>
            <div class="line"></div>
        <? endif ?>
    <? endforeach ?>

    <? if (($i % $itemsPerRow) && $hasLastLine && !$isAjax): ?>
        <div class="line"></div>
    <? endif ?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
</div>
<? endif ?>
