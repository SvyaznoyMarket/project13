<?php
/**
 * @var $productList
 */
render_partial('product_/templates/_list_compact_.php', array(
  'list' => $productList,
  'in_row' => 4,
));