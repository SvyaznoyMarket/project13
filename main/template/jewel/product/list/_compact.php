<?php
/**
 * @var $page                   \View\Layout
 * @var $pager                  \Iterator\EntityPager
 * @var $isAjax                 bool
 * @var $isAddInfo              bool
 * @var $itemsPerRow            int
 **/
?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
<ul class="lstn lstn-jewel clearfix<? if(isset($itemsPerRow) && 3 == $itemsPerRow): ?> lstn-jewel-3col<? endif ?> js-jewel-category">
<? endif ?>

<? $i = 0; foreach ($pager as $product): $i++ ?>
    <? /** @var \Model\Product\Entity $product */ ?>
    <?= $page->render('jewel/product/show/_compact', [
        'product' => $product,
        'itemsPerRow' => $itemsPerRow,
        'category' => $category,
    ]) ?>
<? endforeach ?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
</ul>
<? endif ?>
