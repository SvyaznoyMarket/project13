<?php
/**
 * @var $page               \View\DefaultLayout
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity
 * @var $catalogCategories  \Model\Product\Category\TreeEntity[]
 * @var $catalogConfig      array
 * @var $slideData          array
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

<div class="tchiboSubscribe subscribe-form clearfix">
    <div class="tchiboSubscribe_title">Новые коллекции каждую неделю! Узнай первым</div>
    <input type="text" placeholder="Введите Ваш e-mail адрес" class="tchiboSubscribe_input subscribe-form__email" name="email" />
    <input type="hidden" value="13" name="channel" />
    <button data-url="<?= $page->url('subscribe.create') ?>" class="tchiboSubscribe_btn subscribe-form__btn">Подписаться</button> 
    <div class="subscribecheck">Хочу получать рассылку о новых коллекциях Tchibo</div>
</div>

<!--TCHIBO - каталог разделов, баннеров, товаров Чибо -->
<div class="tchiboCatalog clearfix">
    <? foreach($catalogCategories as $key => $catalogCategory): ?>
        <?
        /** @var \Model\Product\Category\TreeEntity $catalogCategory */
        $imgSrc = $catalogCategory->getImageUrl(3);
        if (empty($imgSrc)) {
            // TODO: изображение заглушки
            $imgSrc = '/styles/tchiboCatalog/img/woman.jpg';
            //$imgSrc = '/styles/tchiboCatalog/img/man.jpg';
        }

        $categoryChildren = $catalogCategory->getChild();

        $lastCategoryOdd = '';

        if ($key == count($catalogCategories) - 1 && count($catalogCategories) % 2 == 1) {
            $lastCategoryOdd = 'mFullWidth';
        }
        ?>

        <div class="tchiboCatalogInner <?= $lastCategoryOdd ?>">
            <a href="<?= $catalogCategory->getLink() ?>">
                <img class="tchiboCatalog__img"
                     src="<?= $imgSrc ?>" alt="<?= $catalogCategory->getName() ?>" />
            </a>

            <div class="tchiboCatalog__title <?= $lastCategoryOdd ?>">
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
