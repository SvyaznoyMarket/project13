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

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
  <div class="bBrandGoods">
    <ul class="bBrandGoodsList clearfix<? if(isset($itemsPerRow) && 3 == $itemsPerRow): ?> eItemBig<? endif ?>">
<? endif ?>

    <? $i = 0; foreach ($pager as $product): $i++ ?>
        <?= $page->render('jewel/product/show/_compact', [
            'product' => $product,
            'addInfo' => $isAddInfo ? \Kissmetrics\Manager::getProductSearchEvent($product, $i, $pager->getPage()) : [],
            'itemsPerRow' => $itemsPerRow
        ]) ?>
    <? endforeach ?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
    </ul>
  </div>
<? endif ?>
