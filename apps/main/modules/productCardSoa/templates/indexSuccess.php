<?php slot('header_meta_og') ?>
<?php include_component('productCardSoa', 'header_meta_og', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('navigation') ?>
<?php include_component('productCardSoa', 'navigation', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('title', $product->name) ?>

<?php //include_partial('product/name', array('product' => $product)) ?>
<?php include_component('productSoa', 'show', array('product' => $product)) ?>
<?php #include_component('service', 'listByProduct', array('product' => $product)) ?>

<?php if ('kit' == $product->view): ?>
  <?php //include_partial('productSoa/kit', $sf_data) ?>
  <?php include_component('productSoa', 'kit', array('product' => $product)) ?>

  <div class="clear pb25"></div>

  <h2 class="bold"><?php echo $product->name ?> - Характеристики</h2>
  <div class="line pb25"></div>
  <div class="descriptionlist">
    <?php include_component('productSoa', 'property_grouped', array('product' => $product)) ?>
  </div>

  <?php include_component('productSoa', 'tags', array('product' => $product)) ?>
<?php endif ?>
<?php /* include_component('productComment', 'list', array(
  'product' => $product,
  'page' => 1,
  'sort' => 'rating_desc',
  'showSort' => false,
  'showPage' => false
  )) */ ?>

<?php if (count($product->related)): ?>
<?php include_partial('productSoa/product_related', $sf_data) ?>
<?php endif ?>

<?php if (count($product->accessories)): ?>
<?php include_partial('productSoa/product_accessory', $sf_data) ?>
<?php endif ?>

<?php //echo link_to('Комментарии', 'productComment', $sf_data->getRaw('product')) ?>

<?php //echo link_to('Аналогичные товары', 'similarProduct', $sf_data->getRaw('product')) ?>

<?php //echo link_to('Наличие в сети', 'productStock', $sf_data->getRaw('product')) ?>

<br class="clear" />

<?php if (has_slot('additional_data')): ?>
    <?php include_slot('additional_data') ?>
<?php endif ?>

<?php include_component('productCardSoa', 'navigation', array('product' => $product, 'seo' => true)) ?>

<?php //include_component('productCatalog', 'navigation_seo', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>


<?php slot('seo_counters_advance') ?>
<?php

$rotCat = $product->getMainCategory();
include_component('productCategory', 'seo_counters_advance', array('unitId' => $rotCat['root_id']))
?>
<script type="text/javascript">
    (function(d){
        var HEIAS_PARAMS = [];
        HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
        HEIAS_PARAMS.push(['pb', '1']);
        HEIAS_PARAMS.push(['product_id', '<?php echo $product->id; ?>']);
        if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
        window.HEIAS.push(HEIAS_PARAMS);
        var scr = d.createElement('script');
        scr.async = true;
        scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
        var elem = d.getElementsByTagName('script')[0];
        elem.parentNode.insertBefore(scr, elem);
    }(document));
</script>
<?php end_slot() ?>

<?php if( 7 == $rotCat->root_id): ?>
<?php slot('sport_sale_design') ?>
<a class='snow_link' href='<?php echo url_for('productCatalog_category', array('productCategory' => 'sport/zimnie-vidi-sporta-710')) ?>'></a>
<div class='snow_wrap'><div class='snow_left'><div class='snow_right'></div></div></div>
<?php end_slot() ?>
<?php endif; ?>