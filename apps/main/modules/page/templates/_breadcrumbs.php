<div class="breadcrumbs">
  <a href="<?php echo url_for('homepage') ?>">Enter.ru</a> &gt; <?php
  if (!is_null($addToBreadcrumbs)) {
    $part_lines = array();
    //$part_lines[] = '<a href="'.url_for('default_show', array('page' => 'how_make_order')).'">Помощь пользователю</a>';
    foreach ($addToBreadcrumbs as $part) {
      $part_lines[] = (isset($part['url']) ? "<a href=\"{$part['url']}\">{$part['name']}</a>" : "<strong>{$part['name']}</strong>");
    }
  }
  else {
    $part_lines = array('<strong>Помощь пользователю</strong>');
  }

  echo implode(' &gt; ', $part_lines);
  ?>
</div>