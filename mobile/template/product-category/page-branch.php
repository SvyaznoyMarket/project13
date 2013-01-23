<?php
/**
 * @var $page     \View\Layout
 * @var $category \Model\Product\Category\Entity
 */
?>

<? if ($parent = $category->getParent()): ?>
    <a class="bMenuBack mBlackLink" href="<?= $page->url('product.category.show', ['categoryPath' => $parent->getPath()]) ?>"><?= $parent->getName() ?></a>
<? else: ?>
    <a class="bMenuBack mBlackLink" href="<?= $page->url('product.category') ?>">Каталог</a>
<? endif ?>
<nav class="bContentMenu">
    <ul>
        <li class="bContentMenu_eItem"><a class="bContentMenu_eLink mGrayLink" href="<?= $page->url('product.category.product', ['categoryPath' => $category->getPath()]) ?>">Все товары из категории</a></li>
        <? foreach ($category->getChild() as $child): ?>
        <li class="bContentMenu_eItem">
            <a class="bContentMenu_eLink mBlackLink" href="<?= $page->url('product.category.show', ['categoryPath' => $child->getPath()]) ?>"><?= $child->getName() ?></a>
        </li>
        <? endforeach ?>
    </ul>
</nav>