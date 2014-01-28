<?php

return function(
    \Helper\TemplateHelper $helper,
    array $categories,
    $catalogConfig = []
) {
    /** @var $categories \Model\Product\Category\Entity[] */


?>

    <!-- TCHIBO - слайдер-меню разделов Чибо -->
    <div class="tchiboNav">
        <div class="tchiboNav__title"<? if (!empty($catalogConfig['root_category_menu']['image'])): ?> style="background-image: url('<?= $catalogConfig['root_category_menu']['image'] ?>')"<? endif ?>></div>

        <ul class="tchiboNav__list">
        <? foreach ($categories as $category): ?>
            <li class="item jsItemListTchibo">
                <a class="link" href="<?= $category->getLink() ?>">
                    <?= $category->getName() ?>
                </a>

                <? if ((bool)$category->getChild()): ?>
                <ul class="tchiboNav__sublist">
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