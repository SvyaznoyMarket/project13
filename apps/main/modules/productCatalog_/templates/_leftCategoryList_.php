<?php
/**
 * @var $productCategory
 * @var $categoryTree
 * @var $quantity
 */
if (!$productCategory || !$categoryTree) {
  return;
}
$renderList = function($categoryList) use ($productCategory, &$renderList)
{
  /** @var $categoryList ProductCategoryEntity[] */
  /** @var $productCategory ProductCategory */

  foreach ($categoryList as $item) {

    echo '<li class="bCtg__eL', $item->getLevel(), ' ';
    if ($productCategory->isRoot() && $productCategory->core_id == $item->getId()) echo "hidden";
    elseif ($item->getHasChild($productCategory->core_id)) echo "mBold";
    elseif ($productCategory->core_id == $item->getId()) echo "mSelected";
    elseif ($item->getParentId() == $productCategory->core_parent_id) echo '';
    else echo 'hidden';
    echo '"><a href="', $item->getLink(), '"><span>', $item->getName(), '</span></a>';
    if ($item->getHasChildren()) $renderList($item->getChildren());
    echo '</li>';
  }
}
?>
<div class="catProductNum"><b>Всего <?php echo $quantity . ($productCategory->has_line ? ' серий' : ' товаров') ?></b>
</div>
<div class="line pb10"></div>
<dl class="bCtg">
  <dd>
    <ul>
      <?php $renderList($categoryTree); ?>
    </ul>
  </dd>
</dl>

