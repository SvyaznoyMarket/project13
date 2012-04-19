<?php
/**
 * @var $categoryTagList
 * @var $maxPerPage
 */
foreach ($categoryTagList as $productTagCategory)
{
  render_partial('productCategory_/templates/_show_carousel.php', array(
    'productTagCategory' => $productTagCategory,
    'maxPerPage' => $maxPerPage,
  ));
}
