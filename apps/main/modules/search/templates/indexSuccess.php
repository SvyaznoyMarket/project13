<?php
/**
 * @var $resultCount
 * @var $searchString
 * @var $forceSearch
 * @var $meanSearchString
 * @var $originalSearchString_quantity
 * @var $productTypeList
 * @var $productType
 * @var $pagers
 */
?>
<?php use_helper('I18N') ?>

<?php slot('title', trim(get_partial('search/product_count', array(
  'count'         => $resultCount,
  'searchString'  => $searchString,
  'forceSearch'               => $forceSearch,
  'meanSearchString'          => $meanSearchString,
  'originalSearchString_quantity' => $originalSearchString_quantity,
)))) ?>

<?php slot('navigation') ?>
  <?php include_component('search', 'navigation', array('searchString' => $searchString)) ?>
<?php end_slot() ?>

<?php slot('page_head') ?>
  <?php include_partial('search/page_head', array('searchString' => $searchString)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('search', 'filter_productType', array('searchString' => $searchString, 'productTypeList' => $productTypeList, 'productType' => $productType)) ?>
<?php end_slot() ?>

<?php include_partial('product/product_list', array('productPager' => $pagers['product'], 'noSorting' => true, 'productType' => $productType)) ?>
