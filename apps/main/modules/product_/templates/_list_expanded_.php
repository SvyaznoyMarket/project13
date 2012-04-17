<?php
/**
 * @var $list ProductEntity[]
 */
foreach ($list as $item) {
  include_partial('product_/show_', array('view' => 'expanded', 'item' => $item));
}
