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
// и страница не branch (например на главной пандоры саму пандору не включаем в список)
if (!$category->isRoot() && $category->isBranch() && empty($isBranchPage)) {
    $categories[] = $category;
}

$parent = ($category->isRoot() || $category->isBranch()) ? null : $category->getParent();
if(!empty($isBranchPage)) $parent = $category;

$showMenu = false;
if (!empty($catalogJson['show_branch_menu']) && ($parent && !$parent->isRoot() || !empty($isBranchPage))) {
    $showMenu = true;
    foreach ($parent->getChild() as $child) {
        $categories[] = $child;
    }
}
?>

<? if($showMenu) { ?>
    <div class="brand-nav">
        <table class="brand-nav__list">
            <tr>
                <td><a href="<?= $parent->getLink() ?>"><span><?= $page->helper->getCategoryLogoOrName($catalogJson, $parent) ?></span></a></td>
                <? foreach ($categories as $node): ?>
                    <td><a href="<?= $node->getLink()  . (\App::request()->get('instore') ? '?instore=1' : '') ?>"><span><?= $node->getName() ?></span></a></td>
                <? endforeach ?>
            </tr>
        </table>
    </div>
<? } else { ?>
    <div class="pt20 pb20"></div>
<? } ?>
