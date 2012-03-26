<?php
/**
 * @var $list ProductEntity[]
 */
foreach ($list as $item) {
  include_partial('show_', array('view' => 'expanded', 'item' => $item));
}
