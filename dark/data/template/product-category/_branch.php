<?php
/**
 * @var $page     \View\DefaultLayout
 * @var $category \Model\Product\Category\Entity
 */
?>

<?php
$categories = $category->getAncestor();
// включить в список категорию, если она не рутовая (root) и внутренняя (inner)
if (!$category->isRoot() && $category->isBranch()) {
    $categories[] = $category;
}

$parent = ($category->isRoot() || $category->isBranch()) ? $category : $category->getParent();
foreach ($parent->getChild() as $child) {
    $categories[] = $child;
}
?>


<div class="catProductNum"><b>Всего <?= ($category->getProductCount() . ($category->getHasLine() ? ' серий' : ' товаров')) ?></b>
</div>
<div class="line pb10"></div>
<dl class="bCtg">
    <dd>
        <ul>
        <? foreach ($categories as $node): ?>
            <?
            $class = 'bCtg__eL' . $node->getLevel();
            if ($node->getLevel() < $category->getLevel()) $class .= ' mBold';
            if ($node->getId() == $category->getId()) $class .= ' mSelected';
            ?>

            <li class="<?= $class ?>"><a href="<?= $node->getLink() ?>"><span><?= $node->getName() ?></span></a></li>
        <? endforeach ?>
        </ul>
    </dd>
</dl>
