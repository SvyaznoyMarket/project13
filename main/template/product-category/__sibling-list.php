<?php

return function(
    \Helper\TemplateHelper $helper,
    array $categories,
    $catalogConfig = [],
    \Model\Product\Category\Entity $currentCategory = null
) {
    /** @var $categories \Model\Product\Category\Entity[] */


?>

    <!-- TCHIBO - слайдер-меню разделов Чибо -->
    <div class="tchiboNav">
        <div class="tchiboNav__title"<? if (!empty($catalogConfig['root_category_menu']['image'])): ?> <? /*style="background-image: url('<?= $catalogConfig['root_category_menu']['image'] ?>')"<? endif ?>> */?>> </div>

        <ul class="tchiboNav__list clearfix">
        <? foreach ($categories as $category):
            $active = $currentCategory && in_array($category->getId(), [$currentCategory->getParentId(), $currentCategory->getId()]) ? true : false; ?>

            <li class="item jsItemListTchibo<? if ($active): ?> active<? endif ?>">
                <a class="link" href="<?= $category->getLink() ?>">
                    <span class="itemText"><?= $category->getName() ?></span>
                </a>

                <? if ((bool)$category->getChild() && ($active || !$currentCategory)): ?>
                    <ul class="tchiboNav__sublist<? if ($active): ?> active<? endif ?>">
                    <? foreach ($category->getChild() as $child):
                        $activeChild = $currentCategory && ($child->getId() === $currentCategory->getId()) ? true : false; ?>

                        <li class="sublistItem jsItemListTchibo<? if ($activeChild): ?> mActive<? endif ?>"><a class="link" href="<?= $child->getLink() ?>"><?= $child->getName() ?></a></li>
                    <? endforeach ?>
                    </ul>
                <? endif ?>
            </li>
        <? endforeach ?>

            <li class="item" style="float:right; margin: 0;"><a class="link" href="/about_tchibo">Подробнее о Tchibo</a></li>
        </ul>
    </div>
    <!--/ TCHIBO - слайдер-меню разделов Чибо -->

<? };