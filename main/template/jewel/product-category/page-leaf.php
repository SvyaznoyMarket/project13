<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 * @var $seoContent            string
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>
<div class="clear"></div>

<div class="clear"></div>
<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<div class="bBrandCatalog">

  <? require __DIR__ . '/_branch.php'; ?>

  <? if(!empty($promoContent)): ?>
      <?= $promoContent ?>
  <? endif ?>

  <div id="smalltabs" data-scrollto-passed="<?= (bool)$scrollTo ?>" class="bBrandSubNav clearfix">
    <?= $page->render('jewel/product-category/filter/_tabs', [
        'filters'           => $productFilter->getFilterCollection(),
        'catalogJson'       => $catalogJson,
        'productFilter'     => $productFilter,
        'category'          => $category,
        'scrollTo'          => $scrollTo,
        'isAddInfo'         => true,
    ]) ?>
  </div>

  <div class="bBrandSorting">
    <?= $page->render('jewel/product-category/_filters', [
        'page'              => $page,
        'filters'           => $productFilter->getFilterCollection(),
        'catalogJson'       => $catalogJson,
        'productSorting'    => $productSorting,
        'productPager'      => $productPager,
        'productFilter'     => $productFilter,
        'category'          => $category,
        'scrollTo'          => $scrollTo,
        'isAjax'            => true,
        'isAddInfo'         => true,
    ]) ?>
  </div>

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
      'isAddInfo'              => true,
  )) ?>

  <div class="clear"></div>

  <? if(!empty($seoContent)): ?>
      <div class="bSeoText">
          <?= $seoContent ?>
      </div>
  <? endif ?>

<? /*

  <div id="pagerWrapper">
    <div style="padding:70px 0;">
      <? //чтобы лоадер корректно показывался при первоначальной загрузке ?>
    </div>
  </div>

*/ ?>

</div>