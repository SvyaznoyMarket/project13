<?php
/**
 * @var $product ProductEntity
 * @var $view string
 * @var $relatedPagesNum int
 * @var $accessoryPagesNum int
 * @var $showRelatedUpper boolean
 * @var $showAccessoryUpper boolean
 */


?>
<?php slot('header_meta_og') ?>
  <?php include_component('productCard_', 'header_meta_og', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('navigation') ?>
  <?php include_component('productCard_', 'navigation', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('title', $product->getName()) ?>

<?php render_partial('product_/templates/_show_.php', array(
  'item' => $product,
  'view' => 'default',
  'relatedPagesNum' => $relatedPagesNum,
  'accessoryPagesNum' => $accessoryPagesNum,
  'showRelatedUpper' => $showRelatedUpper,
  'showAccessoryUpper' => $showAccessoryUpper,
)) ?>


<?php if ('kit' == $product->getView()): ?>
  <?php render_partial('product_/templates/_kit.php', array('product' => $product)) ?>

  <div class="clear pb25"></div>

  <h2 class="bold"><?php echo $product->getName() ?> - Характеристики</h2>
  <div class="line pb25"></div>
  <div class="descriptionlist">
    <?php render_partial('product_/templates/_property_grouped.php', array('item' => $product)) ?>
  </div>

  <?php render_partial('product_/templates/_tags.php', array('item' => $product)) ?>
<?php endif ?>

<?php
if (!$showAccessoryUpper && count($product->getAccessoryList())){
  render_partial('product_/templates/_product_accessory.php', array(
    'product' => $product,
    'accessoryPagesNum' => $accessoryPagesNum,
  ));
}

if (!$showRelatedUpper && count($product->getRelatedList())){
  render_partial('product_/templates/_product_related.php', array(
    'item' => $product,
    'relatedPagesNum' => $relatedPagesNum,
  ));
}
?>

<?php render_partial('product_/templates/_bottom_button_block.php', array(
  'product' => $product,
)) ?>

<br class="clear" />

<?php if (has_slot('additional_data')): ?>
    <?php include_slot('additional_data') ?>
<?php endif ?>

<?php include_component('productCard_', 'navigation', array('product' => $product, 'seo' => true)) ?>


<?php slot('seo_counters_advance') ?>

<?php
$rootCat = $product->getMainCategory();

if ($rootCat) {
  include_component('productCategory', 'seo_counters_advance', array('unitId' => $rootCat->getId()));
}
?>

<div id="heiasProduct" data-vars="<?php echo $product->getId(); ?>" class="jsanalytics"></div>
<div id="marketgidProd" class="jsanalytics"></div>

<?php end_slot() ?>

<?php if (sfConfig::get('app_smartengine_push')): ?>
  <div id="product_view-container" data-url="<?php echo url_for('smartengine_view', array('product' => $product->getId())) ?>"></div>
<?php endif ?>
