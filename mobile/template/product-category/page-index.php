<?php
/**
 * @var $page    \View\Layout
 * @var $categories \Model\Product\Category\Entity[]
 */
?>

<nav class="bCategoryMenu clearfix">
    <ul>
    <? foreach ($categories as $category): ?>
        <li class="bCategoryItem">
            <a class="bCategoryItem_eLink" href="<?= $page->url('product.category.show', ['categoryPath' => $category->getPath()]) ?>">
                <span class="bCategoryItem_eIcon mCategoryIcon_<?= $category->getId() ?>"></span>
                <span class="bCategoryItem_eName"><?= $category->getName() ?></span>
            </a>
        </li>
    <? endforeach ?>
    </ul>
</nav>
