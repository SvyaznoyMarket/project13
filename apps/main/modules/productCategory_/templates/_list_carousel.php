<?php
/**
 * @var $categoryTagList
 */
foreach ($categoryTagList as $productTagCategory)
{
  //include_component('productCategory', 'show', array('view' => 'carousel', 'productCategory' => $productCategory));
  include_partial('productCategory_/show_carousel', array('productTagCategory' => $productTagCategory));
}
