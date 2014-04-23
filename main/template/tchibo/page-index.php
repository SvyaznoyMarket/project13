<?php
/**
 * @var $page               \View\DefaultLayout
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity
 * @var $catalogCategories  \Model\Product\Category\TreeEntity[]
 * @var $catalogConfig      array
 * @var $slideData          array
 * @var $content            string
 * @var $bannerBottom       string
 */


$helper = new \Helper\TemplateHelper();
$siblingCategories = $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : [];

if ((bool)$siblingCategories) {
    /* <!-- TCHIBO - слайдер-меню разделов Чибо --> */
    echo $helper->render('product-category/__sibling-list',
        [
            'categories' => $siblingCategories, // категории-соседи
            'catalogConfig' => $catalogConfig
        ]);
    /* <!--/ TCHIBO - слайдер-меню разделов Чибо -->*/
}

// TCHIBO - крутилка разделов Чибо на рутовой странице
if ((bool)$slideData) {
    echo $helper->render('tchibo/promo-catalog', ['slideData' => $slideData]);
} ?>

<!--TCHIBO - каталог разделов, баннеров, товаров Чибо -->
<div class="tchiboCatalog clearfix">
    <? foreach($catalogCategories as $catalogCategory): ?>
        <?
        /** @var \Model\Product\Category\TreeEntity $catalogCategory */
        $imgSrc = $catalogCategory->getImageUrl(3);
        if (empty($imgSrc)) {
            // TODO: изображение заглушки
            $imgSrc = '/styles/tchiboCatalog/img/woman.jpg';
            //$imgSrc = '/styles/tchiboCatalog/img/man.jpg';
        }

        $categoryChildren = $catalogCategory->getChild();
        if (count($categoryChildren) > 5) {
            $categoryChildren = array_slice($categoryChildren, 5);
        }
        ?>
        <div class="tchiboCatalogInner">
            <a href="<?= $catalogCategory->getLink() ?>">
                <img class="tchiboCatalog__img"
                     src="<?= $imgSrc ?>" alt="<?= $catalogCategory->getName() ?>" />
            </a>

            <div class="tchiboCatalog__title">
                <a class="titleCat" href="<?= $catalogCategory->getLink() ?>">
                    <?= $catalogCategory->getName() ?>
                </a>

                <? if ($categoryChildren): ?>
                    <ul class="tchiboCatalog__list">
                        <? foreach($categoryChildren as $child): ?>
                        <li class="item">
                            <a class="link" href="<?= $child->getLink() ?>">
                                <?= $child->getName() ?>
                            </a>
                        </li>
                        <? endforeach; ?>
                    </ul><? /* <!--/ список подкатегории --> */ ?>
                <? endif; ?>
            </div>
        </div><? /* <!--/ категория --> */ ?>
    <? endforeach; ?>

    <? if (!empty($bannerBottom)): ?>
    <div class="tchiboCatalogInnerBanner">
        <?= $bannerBottom ?>
    </div> <? /* <!--/ вывод баннера или категории без списка подкатегорий и верхней плашкой-заголовком --> */ ?>
    <? endif; ?>
</div>
<!--/ TCHIBO - каталог разделов, баннеров, товаров Чибо -->