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

  <div class="logo-section" style="background: url('/css/pandoraCatalog/img/pandora_logo.gif') no-repeat 50% 0;">Ювелирные Украшения</div>

  <? require __DIR__ . '/_branch.php' ?>

  <? if(!empty($promoContent)): ?>
      <?= $promoContent ?>
  <? endif ?>

  <? $filters = $productFilter->getFilterCollection() ?>
  
  <div id="smalltabs" data-scrollto-passed="<?= $scrollToPassed ?>" class="brand-subnav clearfix">
    <nav>
      <div class="brand-subnav__title"><?= $category->getName() ?></div>
      <? foreach($filters as $key => $filter) { ?>
        <? if(mb_strtolower($catalogJson['sub_category_filter_menu']) == mb_strtolower($filter->getName())) { ?>
          <? require __DIR__ . '/filter/_tabs.php' ?>
        <? } ?>
      <? } ?>
    </nav>
  </div>

  <?= $page->render('jewel/product-category/_filters', ['filters' => $filters, 'catalogJson' => $catalogJson, 'productSorting' => $productSorting, 'productPager' => $productPager, 'productFilter' => $productFilter, 'category' => $category, 'scrollTo' => $scrollTo]) ?>

  <?= $page->render('jewel/product-category/_loading_top') ?>

  <?= $page->render('jewel/product/_pager', array(
      'request'                => $request,
      'pager'                  => $productPager,
      'productFilter'          => $productFilter,
      'productSorting'         => $productSorting,
      'hasListView'            => true,
      'category'               => $category,
      'view'                   => $productView,
      'productVideosByProduct' => $productVideosByProduct,
      'itemsPerRow'            => $itemsPerRow,
  )) ?>

</div>