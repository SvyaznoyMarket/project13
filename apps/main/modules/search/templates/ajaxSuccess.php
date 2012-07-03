<?php
/**
 * @var $productPager
 * @var $view
 */
?>
<?php use_helper('I18N') ?>

<?php render_partial('product_/templates/_list_.php', array('productPager' => $productPager, 'view' => $view, 'ajax_flag' => true)) ?>