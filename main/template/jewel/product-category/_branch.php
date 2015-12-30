<?php return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    $isBranchPage = false,
    $catalogJson = []
) { ?>

    <?php
    // список категорий
    /** @var \Model\Product\Category\Entity[] $categories */
    $categories = [];

    // включить в список категорию, если она не рутовая (root) и внутренняя (inner)
    // и страница не branch (например на главной пандоры саму пандору не включаем в список)
    if (!$category->isRoot() && $category->isBranch() && empty($isBranchPage)) {
        $categories[] = $category;
    }

    $parent = $category->isRoot() || $category->isBranch() ? null : $category->getParent();
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
        <table class="bBrandNavList">
            <tr class="bBrandNavList__eRow">
                <? if ($parent): ?>
                    <td class="bBrandNavList__eCell">
                        <a class="bBrandNavList__eLink" href="<?= $parent->getLink() ?>">
                            <? if (!empty($catalogJson['logo_path']) && !empty($catalogJson['use_logo'])): ?>
                                <img src="<?= $helper->escape($catalogJson['logo_path']) ?>">
                            <? else: ?>
                                <?= $parent->getName() ?>
                            <? endif ?>
                        </a>
                    </td>
                <? endif ?>

                <? foreach ($categories as $node): ?>
                    <td class="bBrandNavList__eCell"><a class="bBrandNavList__eLink" href="<?= $node->getLink()  . (\App::request()->get('instore') ? '?instore=1' : '') ?>"><?= $node->getName() ?></a></td>
                <? endforeach ?>
            </tr>
        </table>
    <? } else { ?>
    <? } ?>

<? };
