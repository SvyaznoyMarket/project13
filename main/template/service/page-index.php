<?php
/**
 * @var $page         \View\Service\IndexPage
 * @var $categories   \Model\Product\Service\Category\Entity[]
 * @var $category     \Model\Product\Service\Category\Entity
 */
?>

<?
/** @var $helper \View\Service\Helper */
$helper = $page->helper;
?>

<div class="servicebanner"></div>
<div class="slogan">
     <strong>Доставим радость, настроим комфорт!</strong>
     Специалисты F1 привезут и соберут шкаф, повесят телевизор, куда скажете, и установят стиральную машину по всем правилам.
</div>

<?php
$icons = array('icon1', 'icon2', 'icon3', 'icon4');

$categoriesByIcon = [];
foreach ($categories as $category) {
    $icon = $helper->categoryIconClass($category);

    if (!in_array($icon, $icons)) continue;
    $categoriesByIcon[$icon] = $category;
}
?>

<? $i = 0; foreach ($icons as $icon): $i++ ?>
    <?
        if (!isset($categoriesByIcon[$icon])) continue;
        $category = $categoriesByIcon[$icon];
        $children = $category->getChild();
        /** @var $firstChild \Model\Product\Service\Category\Entity */
        $firstChild = reset($children);
        $link = $firstChild ? $firstChild->getLink() : $category->getLink();
    ?>
    <div class="servicebox fl">
        <a href="<?= $link ?>">
            <i class="<?= $helper->categoryIconClass($category) ?>"></i>
            <strong class="font16"><?= $category->getName() ?></strong>
            <?= $category->getDescription() ?>
        </a>
        <a class="servicebox__choice" href="<?= $link ?>">
            выбрать услуги &rsaquo;
        </a>
    </div>
    <? if ($i % 2 == 0): ?><div class="pb30 clear"></div><? endif ?>
<? endforeach ?>
