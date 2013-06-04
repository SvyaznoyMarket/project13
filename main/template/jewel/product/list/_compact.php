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
  <div class="items-section">
    <ul class="items-section__list">
<? endif ?>

    <? $i = 0; foreach ($pager as $product): $i++ ?>
        <?= $page->render('jewel/product/show/_compact', array('product' => $product)) ?>
    <? endforeach ?>

<? if (!$isAjax): // убрать декорацию div-ом, если ajax-запрос ?>
    </ul>
  </div>
<? endif ?>
