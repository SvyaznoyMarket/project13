<?php
/**
 * @var ProductCorePagerContainer $productPager
 * @var $productSorting
 * @var $sf_data
 * @var $infinityUrl
 * @var $view
 * @var ProductCoreFormFilterSimple $productFilter
 */
$request = sfContext::getInstance()->getRequest();
$page = $request->getParameter('page');
$view = $request->getParameter('view', isset($view) ? $view : null);

$empty = 0 == $productPager->getNbResults();

$dataFilter = null;
if(isset($productFilter)){
  $dataFilter = $productFilter->getUrlParams();
}else{
  $dataFilter = '';
}

?>

<?php if ($view == 'expanded') : ?>
<input type="hidden" id="dlvrlinks" data-shoplink="<?php echo url_for('shop') ?>"
       data-calclink="<?php echo url_for('product_delivery') ?>"/>
<?php endif ?>


<?php if (count($productPager->getLinks()) > 1): ?>
<?php
  $dataAr = array();
  $module = sfContext::getInstance()->getModuleName();
  if ($module == 'tag') {
    //страница тега
    $tag = $request->getParameter('tag');
    $productType = $request->getParameter('productType');
    $dataAr['tag'] = $tag;
    if (isset($productType)) {
      $dataAr['productType'] = $productType;
    }
    $infinityUrl = url_for('tag_showAjax', $dataAr);

  } elseif ($module == 'search') {
    //страница поиска
    $q = $request->getParameter('q');
    $dataAr['q'] = $q;
    if (isset($productType)) {
      $dataAr['product_type'] = $productType['id'];
    }
    $infinityUrl = url_for('search_ajax', $dataAr);
  } else {
    //страница каталога (любая. возможно, с фильтрами и тегами)
    $dataAr['productCategory'] = $productCategory->getTokenPrefix() ? ($productCategory->getTokenPrefix().'/'.$productCategory->getToken()) : $productCategory->getToken();
    $infinityUrl = url_for('productCatalog_categoryAjax', $dataAr);
  }
  ?>
<div data-url="<?php echo $infinityUrl; ?>"
     data-page="<?php  echo $page; ?>"
     data-mode="<?php  echo $view; ?>"
     data-lastpage="<?php echo $productPager->getLastPage(); ?>"
     data-filter="<?php echo $dataFilter; ?>"
     class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"></div>
<?php endif ?>

<?php if (count($productPager->getLinks()) > 1): ?>
<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
<?php endif ?>

<?php if (!$empty && !(isset($list_view) && false === $list_view)): ?>
<?php include_component('product', 'list_view', array('view' => $view)) ?>
<?php endif ?>

<?php if (!isset($noSorting) || !$noSorting): ?>
<?php include_component('product', 'sorting', array('productSorting' => $productSorting)) ?>
<?php endif ?>

<?php if (!$empty): ?>
<div class="line"></div>
<?php endif ?>

<?php render_partial('product_/templates/_list_.php', array('productPager' => $productPager, 'view' => $view,)) ?>

<?php if (count($productPager->getLinks()) > 1): ?>
<div data-url="<?php echo $infinityUrl; ?>"
     data-page="<?php  echo $page; ?>"
     data-mode="<?php  echo $view; ?>"
     data-lastpage="<?php echo $productPager->getLastPage(); ?>"
     data-filter="<?php echo $dataFilter; ?>"
     class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"></div>
<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
<?php endif ?>