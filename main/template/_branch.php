<?php return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    $isBranchPage = false
) { ?>

    <?php
    // список категорий
    /** @var \Model\Product\Category\Entity[] $categories */
    $categories = [];

    if ($isBranchPage) {
        $parent = $category->getParent();
    } else {
        $parent = $category;
    }

    foreach ($parent->getChild() as $child) {
        $categories[] = $child;
    }
    ?>

    <table class="bBrandNavList">
        <tr class="bBrandNavList__eRow">
            <td class="bBrandNavList__eCell">
                <a class="bBrandNavList__eLink" href="<?= $parent->getLink() ?>">
                    <? if (!empty($category->catalogJson['logo_path_for_listing_menu'])): ?>
                        <img src="<?= $helper->escape($category->catalogJson['logo_path_for_listing_menu']) ?>">
                    <? else: ?>
                        <?= $parent->getName() ?>
                    <? endif ?>
                </a>
            </td>

            <? foreach ($categories as $node): ?>
                <td class="bBrandNavList__eCell"><a class="bBrandNavList__eLink" href="<?= $node->getLink() ?>"><?= $node->getName() ?></a></td>
            <? endforeach ?>
        </tr>
    </table>
<? };
