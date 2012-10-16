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


<div class="catProductNum">
<? if ($category->getHasLine()): ?>
    <b>Всего <?= $page->helper->formatNumberChoice('{n: n > 10 && n < 20}%count% серий|{n: n % 10 == 1}%count% серия|{n: n % 10 > 1 && n % 10 < 5}%count% серии|(1,+Inf]%count% серий', array('%count%' => $category->getProductCount()), $category->getProductCount()) ?></b>
<? else: ?>
    <b>Всего <?= $page->helper->formatNumberChoice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $category->getProductCount()), $category->getProductCount()) ?></b>
<? endif ?>
</div>
<div class="line pb10"></div>
<dl class="bCtg">
    <dd>
        <ul>
        <? foreach ($categories as $node): ?>
            <?
            $class = 'bCtg__eL' . ($node->getLevel() <= 4 ? $node->getLevel() : 4);
            if ($node->getLevel() < $category->getLevel()) $class .= ' mBold';
            if ($node->getId() == $category->getId()) $class .= ' mSelected';
            ?>

            <li class="<?= $class ?>"><a href="<?= $node->getLink() ?>"><span><?= $node->getName() ?></span></a></li>
        <? endforeach ?>
        </ul>
    </dd>
</dl>
