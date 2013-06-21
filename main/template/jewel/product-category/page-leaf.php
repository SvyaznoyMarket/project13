<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>
<div class="clear"></div>

<div class="clear"></div>
<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<div class="brandSection brandSectionPandora brandSectionPandora__catalog">

  <? require __DIR__ . '/_branch.php' ?>

  <? if(!empty($promoContent)): ?>
      <?= $promoContent ?>
  <? endif ?>

  <div id="smalltabs" data-scrollto-passed="<?= (bool)$scrollTo ?>" class="brand-subnav clearfix"></div>

  <div class="filter-section"></div>

  <?= $page->render('jewel/product-category/_loading_top') ?>

  <div id="pagerWrapper">
    <div style="padding:70px 0;">
      <? //чтобы лоадер корректно показывался при первоначальной загрузке ?>
    </div>
  </div>

</div>