<?php $ajax_flag = isset($ajax_flag) ? $ajax_flag : false ?>

<?php if (!isset($list[0])): ?>
  <p>нет товаров</p>

<?php else: ?>

  <?php if ($ajax_flag): ?>
    <?php
    if ($view == 'line') {
        $include = 'product/list_ajax_compact';        
    } else {
        $include = 'product/list_ajax_'.$view;
    }
    include_partial($include, $sf_data)
    ?>
  <?php else: ?>
    <?php include_partial('product/list_'.$view, $sf_data) ?>
  <?php endif ?>

<?php endif ?>