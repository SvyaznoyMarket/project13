<?php
/**
 * @var $page                   \View\Layout
 * @var $pager                  \Iterator\EntityPager
 * @var $isAjax                 bool
 * @var $productVideosByProduct array
 * @var $isAddInfo              bool
 * @var $itemsPerRow            int
 * @var $view                   array
 **/
?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
  <div class="bBrandGoods">
    <ul class="bBrandGoodsList clearfix<? if(isset($itemsPerRow) && 3 == $itemsPerRow): ?> eItemBig<? endif ?>">
<? endif ?>

    <? $i = 0; foreach ($pager as $product): $i++ ?>
        <? /** @var \Model\Product\Entity $product */ ?>
        <?= $page->render('jewel/product/show/_compact', [
            'product' => $product,
            'addInfo' => $isAddInfo ? \Kissmetrics\Manager::getProductSearchEvent($product, $i, $pager->getPage()) : [],
            'itemsPerRow' => $itemsPerRow,
            'productVideo' => isset($productVideosByProduct[$product->getId()]) ? reset($productVideosByProduct[$product->getId()]) : null,
            'view' => $view,
        ]) ?>
    <? endforeach ?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
    </ul>
  </div>
<? endif ?>
