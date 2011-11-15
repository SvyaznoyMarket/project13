<?php if (!count($list)): ?>
  <p>нет товаров</p>

<?php else: ?>
  <?php include_partial('line/list_'.((isset($ajax_flag) && $ajax_flag) ? 'ajax_' : '').$view, $sf_data) ?>
<?php endif ?>