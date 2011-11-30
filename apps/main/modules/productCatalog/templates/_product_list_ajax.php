<?php $empty = 0 == $productPager->getNbResults() ?>

<?php include_component('product', 'pager', array('pager' => $productPager, 'view' => $view, 'ajax_flag' => true)) ?>





