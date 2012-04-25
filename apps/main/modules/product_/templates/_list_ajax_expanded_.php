<?php
/**
 * @var $list
 */
foreach ($list as $item)
  render_partial('product_/templates/_show_.php', array('view' => 'expanded', 'item' => $item));
