<?php use_helper('I18N') ?>

<?php include_partial('product/product_list_ajax', array('productPager' => $pagers['product'],'view' => $view, 'noSorting' => true,)) ?>
