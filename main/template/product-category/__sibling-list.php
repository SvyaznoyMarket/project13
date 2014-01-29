<?php

return function(
    \Helper\TemplateHelper $helper,
    array $categories,
    $catalogConfig = [],
    \Model\Product\Category\Entity $currentCategory
) {
    /** @var $categories \Model\Product\Category\Entity[] */


?>

    <!-- TCHIBO - слайдер-меню разделов Чибо -->
    <div class="tchiboNav">
        <div class="tchiboNav__title"<? if (!empty($catalogConfig['root_category_menu']['image'])): ?> style="background-image: url('<?= $catalogConfig['root_category_menu']['image'] ?>')"<? endif ?>></div>

        <ul class="tchiboNav__list">
        <? foreach ($categories as $category): ?>
            <?
            $active = ($category->getId() === $currentCategory->getParentId()) && !$currentCategory->getHasChild() ? true : false;
            ?>

            <li class="item jsItemListTchibo<? if ($active): ?> active<? endif ?>">
                <a class="link" href="<?= $category->getLink() ?>">
                    <?= $category->getName() ?>
                </a>

                <? if ((bool)$category->getChild()): ?>
                <ul class="tchiboNav__sublist<? if ($active): ?> active<? endif ?>">
                <? foreach ($category->getChild() as $child): ?>
                    <li class="sublistItem jsItemListTchibo"><a class="link" href="<?= $child->getLink() ?>"><?= $child->getName() ?></a></li>
                <? endforeach ?>
                </ul>
                <? endif ?>
            </li>
        <? endforeach ?>
        </ul>
    </div>
    <!--/ TCHIBO - слайдер-меню разделов Чибо -->

<? };