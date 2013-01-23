<?php
/**
 * @var $page    \View\Layout
 * @var $categories \Model\Product\Category\Entity[]
 */
?>

<a class="bMenuBack mBlackLink" href="<?= $page->url('homepage') ?>">Главная</a>
<nav class="bContentMenu">
    <ul>
    <? foreach ($categories as $category): ?>
        <li class="bContentMenu_eItem">
            <a class="bContentMenu_eLink mBlackLink" href="<?= $page->url('product.category.show', ['categoryPath' => $category->getPath()]) ?>"><?= $category->getName() ?></a>
        </li>
    <? endforeach ?>
    </ul>
</nav>