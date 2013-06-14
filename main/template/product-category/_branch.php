<?php
/**
 * @var $page          \View\Layout
 * @var $request       \Http\Request
 * @var $user          \Session\User
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 */
?>

<?php
// флаг: показывать все товары в категории
$isGlobal = $productFilter->isGlobal();

// название региона в предложном падеже
$regionInflectedName = $user->getRegion()->getInflectedName(5);

// список категорий
$categories = $category->getAncestor();
// включить в список категорию, если она не рутовая (root) и внутренняя (inner)
if (!$category->isRoot() && $category->isBranch()) {
    $categories[] = $category;
}

$parent = ($category->isRoot() || $category->isBranch()) ? $category : $category->getParent();
if ($parent) {
    foreach ($parent->getChild() as $child) {
        $categories[] = $child;
    }
}
//var_dump($category->isLeaf()); exit;
// total text
$totalText = $category->getProductCount() . ' ' . ($category->getHasLine()
    ? $page->helper->numberChoice($category->getProductCount(), array('серия', 'серии', 'серий'))
    : $page->helper->numberChoice($category->getProductCount(), array('товар', 'товара', 'товаров'))
);
//     for global
$globalTotalText = $category->getGlobalProductCount() . ' ' .$page->helper->numberChoice($category->getGlobalProductCount(), array('товар', 'товара', 'товаров'));
?>

<div class="catProductNum">
    <? if ($category->getProductCount()): ?>
        <? if (!$category->isLeaf()) { ?>
            <b>В <?= $regionInflectedName ?> <?= $totalText ?></b>
        <? } elseif ($category->isLeaf() && !$productFilter->getShop()) { ?>
            <b>В <?= $regionInflectedName ?> <?= $totalText ?></b>
        <? } ?>
        <?
            if ($productFilter->getShop() && !$category->isRoot() && $page->hasGlobalParam('productCount')) : ?>
                <br><b>В магазине <?= $productFilter->getShop()->getAddress() ?><br><?=$page->getGlobalParam('productCount').' '.$page->helper->numberChoice($page->getGlobalParam('productCount'), array('товар', 'товара', 'товаров')); ?></b>
            <? endif ?>
    <? endif ?>
    <? if ($category->getGlobalProductCount() && \App::config()->product['globalListEnabled'] && $user->getRegion()->getHasTransportCompany()): ?>
        <br />
        Всего в категории <?= $globalTotalText ?>
        <noindex>
            <a rel="nofollow" href="<?= $page->url('product.category.global', array('categoryPath' => $category->getPath(), 'global' => $isGlobal ? 0 : 1)) ?>"><?= ($isGlobal ? ('показать товары в ' . $regionInflectedName) : 'показать все товары') ?></a>
        </noindex>
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

            <li class="<?= $class ?>"><a href="<?= $node->getLink()  . (\App::request()->get('instore') ? '?instore=1' : '') ?>"><span><?= $node->getName() ?></span></a></li>
        <? endforeach ?>
        </ul>
    </dd>
</dl>
