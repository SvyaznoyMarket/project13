<?php
/**
 * @var $page    \View\Layout
 * @var $pager   \Iterator\EntityPager
 * @var $product \Model\Product\Entity
 * @var $isAjax  bool
 * */
?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
<div class="goodslist">
<? endif ?>

    <? $i = 0; foreach ($pager as $product): $i++ ?>
    <?= $page->render('product/show/_line', array('product' => $product)) ?>

    <? if (!($i % 3) && !$isAjax): ?>
        <div class="line"></div>
    <? endif ?>

    <? endforeach ?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
</div>
<? endif ?>