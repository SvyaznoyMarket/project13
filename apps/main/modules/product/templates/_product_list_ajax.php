<?php $empty = 0 == $productPager->getNbResults() ?>

<?php include_component('product', 'pager', array('pager' => $productPager, 'view' => isset($view) ? $view : null, 'ajax_flag' => true)) ?>




