<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>
<?php slot('navigation_seo') ?>
    <?php include_component('productCatalog', 'navigation_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'leftCategoryList', array('productCategory' => $productCategory)) ?>
  <?php include_component('productCatalog', 'tag', array('productCategory' => $productCategory)) ?>
  <?php include_partial('default/banner_left') ?>
  <?php include_component('productCatalog', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php include_partial('productCatalog/plugs/plug') ?>

<div class="clear"></div>

<?php include_component('productCategory', 'child_list', array('view' => 'preview', 'productCategory' => $productCategory)) ?>

<?php //include_component('productCategory', 'productType_list', array('productCategory' => $productCategory)) ?>

<?php slot('seo_counters_advance') ?>
  <?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>

<?php if( 7 == $productCategory['id']): ?>
  <?php slot('sport_sale_design') ?>
    <a class='snow_link' href='<?php echo url_for('productCatalog_category', array('productCategory' => 'sport/zimnie-vidi-sporta-710')) ?>'></a>
    <div class='snow_wrap'><div class='snow_left'><div class='snow_right'></div></div></div>
  <?php end_slot() ?>
<?php endif; ?>