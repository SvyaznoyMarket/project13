<?php
/**
 * @var $page           \View\Product\SetPage
 * @var $pager          \Iterator\EntityPager
 * @var $categoriesById \Model\Product\Category\Entity[]
 */
?>

<? $limit = 8 ?>
<dl class="bCtg border-none">
    <dt class="bCtg__eOrange">
        Похожие товары можно найти <?= $pager->count() > 1 ? 'в категориях' : 'в категории' ?>:
    </dt>
    <dd>
        <ul>
            <? foreach ($categoriesById as $category): ?>
            <li class="bCtg__eL2">
                <a href="<?= $category->getLink() ?>"><span><?= $category->getName() ?></span></a>
            </li>
            <? endforeach ?>
        </ul>
    </dd>
</dl>
