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
$categories = [];

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
?>

<div class="brand-nav">
    <table class="brand-nav__list">
        <tr>
            <td><a href="<?= $category->getParent()->getLink() ?>"><span><?= $page->helper->getCategoryLogoOrName($catalogJson, $category->getParent()) ?></span></a></td>
            <? foreach ($categories as $node): ?>
                <td><a href="<?= $node->getLink()  . (\App::request()->get('instore') ? '?instore=1' : '') ?>"><span><?= $node->getName() ?></span></a></td>
            <? endforeach ?>
        </tr>
    </table>
</div>

