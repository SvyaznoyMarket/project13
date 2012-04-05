<?php
/**
 * @var $categoryTagList
 * @var $maxPerPage
 */
foreach ($categoryTagList as $productTagCategory)
{
  include_partial('productCategory_/show_carousel', array(
    'productTagCategory' => $productTagCategory,
    'maxPerPage' => $maxPerPage,
  ));
}
