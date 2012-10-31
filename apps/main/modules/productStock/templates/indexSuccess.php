<?php
/**
 * @var $product ProductEntity
 */

slot('navigation');
$list = array();
/** @var $category ProductCategoryEntity */
foreach($product->getCategoryList() as $category)
  $list[] = array(
    'name' => $category->getName(),
    'url' => $category->getLink(),
  );
$list[] = array(
    'name' => $product->getName(),
    'url' => $product->getLink(),
);
$list[] = array(
  'name' => 'Где купить ' . mb_lcfirst($product->getName()),
  'url' => $product->getLink(),
);
include_component('default', 'navigation', array('list' => $list));
end_slot();

slot('title', 'Где купить '.mb_lcfirst($product->getName()));

slot('after_body_block');
render_partial('product_/templates/_oneclickTemplate.php');
end_slot();

render_partial('productStock/templates/_show.php', array('product' => $product));
