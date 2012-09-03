<?php
/**
/* @var $categoryList ProductCategoryEntity[]
 * @var $productCategory ProductCategoryEntity
 * @var $item ProductCategoryEntity
 * @var $categoryTree
 * @var $quantity
 */
$renderList = function($categoryList) use ($productCategory, &$renderList)
{
  $render = '';
  foreach ($categoryList as $item) {

    if ($productCategory->isRoot() && $productCategory->getId() == $item->getId()) $class = "hidden";
    elseif ($item->getHasChild($productCategory->getId())) $class = "mBold";
    elseif ($productCategory->getId() == $item->getId()) $class = "mSelected";
    elseif ($item->getParentId() == $productCategory->getParentId()) $class = '';
    elseif ($item->getParentId() == $productCategory->getId()) $class = '';
    else $class = 'hidden';

    $render .= sprintf('<li class="bCtg__eL%d %s"><a href="%s"><span>%s</span></a>%s</li>',
      $item->getLevel(),
      $class,
      $item->getLink(),
      $item->getName(),
      $item->getHasChildren() ? $renderList($item->getChildren()) : ''
    );
  }
  return $render;
}
?>
<div class="catProductNum"><b>Всего <?php echo $quantity . ($productCategory->getHasLine() ? ' серий' : ' товаров') ?></b>
</div>
<div class="line pb10"></div>
<dl class="bCtg">
  <dd>
    <ul>
      <?php echo $renderList($categoryTree); ?>
    </ul>
  </dd>
</dl>

