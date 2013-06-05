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

<div class="logo-section" style="background: url('/css/pandoraCatalog/img/pandora_logo.gif') no-repeat 50% 0;">Ювелирные Украшения</div>

<? require __DIR__ . '/_branch.php' ?>

<? if(!empty($promoContent)): ?>
    <?= $promoContent ?>
<? endif ?>

<? $filters = $productFilter->getFilterCollection() ?>

<nav class="brand-subnav clearfix">
  <div class="brand-subnav__title">Подвески - шармы</div>
  <? foreach($filters as $key => $filter) { ?>
    <? if(mb_strtolower($catalogJson['sub_category_filter_menu']) == mb_strtolower($filter->getName())) { ?>
      <? require __DIR__ . '/filter/_tabs.php' ?>
    <? } ?>
  <? } ?>
</nav>

<div class="filter-section">
  <ul class="clearfix">
    <? foreach($filters as $key => $filter) { ?>
      <? // не выводим фильтры, запрещенные в json и указанный в качестве табов
        if((!empty($catalogJson['sub_category_filters_exclude']) && is_array($catalogJson['sub_category_filters_exclude']) &&
            in_array(mb_strtolower($filter->getName()), array_map(function($filterName){
              return mb_strtolower($filterName);
            }, $catalogJson['sub_category_filters_exclude']))) ||
            (mb_strtolower($catalogJson['sub_category_filter_menu']) == mb_strtolower($filter->getName()))) {
          continue;
        } 
      ?>
      <? require __DIR__ . '/filter/_dropdown.php' ?>
    <? } ?>

    <? if ($productSorting && $productPager->count()): ?>
      <?= $page->render('jewel/product/_sorting', array('productSorting' => $productSorting)) ?>
    <? endif ?>
   </ul>
</div>


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
