<?php
/**
 * @var $line ProductLine
 * @var $productLine ProductLineEntity
 * @var $view
 */

slot('title', 'Серия '.$productLine->getName());

slot('navigation');
  include_component('default', 'navigation', array('list' => $productLine->getNavigation()));
end_slot();

render_partial('line/templates/_main_product.php', array('productLine' => $productLine));

render_partial('line/templates/_product_list.php', array(
  'view' => $view,
  'productLine' => $productLine,
));
