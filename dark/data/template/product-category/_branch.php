<?php
/**
 * @var $page     \View\Layout
 * @var $user     \Session\User
 * @var $category \Model\Product\Category\Entity
 */
?>

<?php
// флаг: показывать все товары в категории
$isGlobal = \App::request()->get('global', 0) ? true : false;

// название региона в предложном падеже
$regionInflectedName = $user->getRegion()->getInflectedName(5);

// список категорий
$categories = $category->getAncestor();
// включить в список категорию, если она не рутовая (root) и внутренняя (inner)
if (!$category->isRoot() && $category->isBranch()) {
    $categories[] = $category;
}

$parent = ($category->isRoot() || $category->isBranch()) ? $category : $category->getParent();
foreach ($parent->getChild() as $child) {
    $categories[] = $child;
}

// total text
$textTemplate =
    $category->getHasLine()
        ? '{n: n > 10 && n < 20}%count% серий|{n: n % 10 == 1}%count% серия|{n: n % 10 > 1 && n % 10 < 5}%count% серии|(1,+Inf]%count% серий'
        : '{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров';
//     for current region
$totalText = $page->helper->formatNumberChoice($textTemplate, array('%count%' => $category->getProductCount()), $category->getProductCount());
//     for global
$globalTotalText = $page->helper->formatNumberChoice($textTemplate, array('%count%' => $category->getGlobalProductCount()), $category->getGlobalProductCount());
?>

<div class="catProductNum">
    <b>В <?= $regionInflectedName ?> <?= $totalText ?></b>
    <? if (\App::config()->product['globalListEnabled']): ?>
        <br />
        Всего в категории <?= $globalTotalText ?>
        <a href="<?= $page->helper->replacedUrl(array('global' => $isGlobal ? null : 1)) ?>"><?= ($isGlobal ? ('показать товары в ' . $regionInflectedName) : 'показать все товары') ?></a>
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
