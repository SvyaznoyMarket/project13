<?php
/**
 * @var $productCategory
 * @var $categoryTree
 * @var $quantity
 */
$renderList = function($categoryList) use ($productCategory, &$renderList)
{
  /** @var $categoryList ProductCategoryEntity[] */
  /** @var $productCategory ProductCategory */
  $render = '';
  foreach ($categoryList as $item) {

    if ($productCategory->isRoot() && $productCategory->core_id == $item->getId()) $class = "hidden";
    elseif ($item->getHasChild($productCategory->core_id)) $class = "mBold";
    elseif ($productCategory->core_id == $item->getId()) $class = "mSelected";
    elseif ($item->getParentId() == $productCategory->core_parent_id) $class = '';
    elseif ($item->getParentId() == $productCategory->core_id) $class = '';
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
<div class="catProductNum">
	<b>Всего <?php echo $quantity . ($productCategory->has_line ? ' серий' : ' товаров') ?></b>
	<a href="#">Показать товары в<br/>Самаре</a>
	<a href="#" class="hidden">Показать все товары</a>
</div>
<div class="line pb10"></div>
<dl class="bCtg">
  <dd>
    <ul>
      <?php echo $renderList($categoryTree); ?>
    </ul>
  </dd>
</dl>

