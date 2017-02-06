<?php

use Model\Product\Category\Entity as Category;
/**
 * @var $page               \View\DefaultLayout
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity
 * @var $catalogCategories  \Model\Product\Category\TreeEntity[]
 * @var $catalogConfig      array
 * @var $slideData          array
 * @var $bannerBottom       string
 * @var $promoContent       string
 * @var $promoBanners       \Model\Promo\Entity
 * @var $collectionBanners  \Model\Promo\Entity
 * @var $categoryWithChilds Category
 */


$helper = new \Helper\TemplateHelper();
$siblingCategories = $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : [];

$isCategoriesOddCount = (bool)(count($catalogCategories) % 2 == 1);

if ((bool)$siblingCategories) {
    /* <!-- TCHIBO - слайдер-меню разделов Чибо --> */
    echo $helper->render('product-category/__sibling-list',
        [
            'categories' => $siblingCategories, // категории-соседи
            'catalogConfig' => $catalogConfig
        ]);
    /* <!--/ TCHIBO - слайдер-меню разделов Чибо -->*/
}

/** @var Category[] $categoryByUi */
$categoryByUi = [];
foreach ($categoryWithChilds->getChild() as $ct) {
    $categoryByUi[$ct->getUi()] = $ct;
}

?>

<div class="slider2">
    <?
    // TCHIBO - крутилка разделов Чибо на рутовой странице
    if ((bool)$slideData) {
        echo $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => 'tchibo']);
    } ?>
</div>

<?= $page->render('tchibo/banners-row', [ 'promo' => $promoBanners ]) ?>

<!-- Форма подписки -->
<div class="b-subscribe-to-sale subscribe-form">
    <div class="b-subscribe-to-sale__title">Узнай первым о новинках и акциях</div>

    <div class="b-subscribe-to-sale__form">
        <input type="text" placeholder="Введите Ваш e-mail адрес" class="b-subscribe-to-sale__input subscribe-form__email" name="email" />
        <input type="hidden" value="13" name="channel" />

        <button
            class="b-subscribe-to-sale__button subscribe-form__btn"
            data-url="<?= $page->url('subscribe.create') ?>"
            data-error-msg="<?= $page->escape('Вы уже подписаны на рассылку! О всех проблемах сообщайте на my.enter.ru/feedback/') ?>">
            Подписаться
        </button>

        <div class="b-subscribe-to-sale__check">Хочу получать рассылку о коллекциях Tchibo</div>
    </div>
</div>
<!-- END Форма подписки -->

<!-- Разделы -->
<div class="s-sales-grid s-sales-grid--category">
    <div class="s-sales-grid__row grid-2cell cell-h-340">
        <? foreach($catalogCategories as $key => $catalogCategory): ?>
            <?
            /** @var \Model\Product\Category\TreeEntity $catalogCategory */
            $imgSrc = null;

            if ($catalogCategory->ui === Category::UI_TCHIBO_COLLECTIONS
                || $catalogCategory->ui === Category::UI_TCHIBO_SALE) {
                unset($catalogCategories[$key]);
                continue;
            }

            if (array_key_exists($catalogCategory->getUi(), $categoryByUi)) {
                $imgSrc = $categoryByUi[$catalogCategory->getUi()]->getMediaSource('category_grid_366x488', 'category_grid')->url;
            }

            if (empty($imgSrc)) {
                $imgSrc = $catalogCategory->getImageUrl(3);
            }
            ?>

            <div class="s-sales-grid__cell">
                <a class="s-sales-grid__link" href="<?= $catalogCategory->getLink() ?>">
                    <img src="<?= $imgSrc ?>" alt="<?= $catalogCategory->getName() ?>" class="s-sales-grid__img">
                    <span class="s-sales-grid-desc">
                        <span class="s-sales-grid-desc__title">
                            <span class="s-sales-grid-desc__title-name"><?= $catalogCategory->getName() ?></span>
                            <span class="s-sales-grid-desc__title-product-count"><?= (new \Helper\TemplateHelper())->numberChoiceWithCount($catalogCategory->getProductCount(), ['товар', 'товара', 'товаров'])?></span>
                        </span>
                    </span>
                </a>
            </div>
        <? endforeach; ?>

        <? if (count($catalogCategories) % 2 === 1) : ?>
            <!-- Дополняем до четного количества баннером -->
            <div class="s-sales-grid__cell">
                <a class="s-sales-grid__link" href="">
                    <img src='//www.imgenter.ru/uploads/media/c5/30/4c/036ebabd7dadfefba83f9d5a24a6c40099f8ac8a.jpeg' class="s-sales-grid__img">
                </a>
            </div>
        <? endif ?>
    </div>

    <?= $page->render('tchibo/banners-row', [ 'promo' => $collectionBanners ]) ?>

</div>
<!-- END Разделы -->

<? if (!empty($bannerBottom)): ?>
    <div>
        <?= $bannerBottom ?>
    </div>
<? endif; ?>

<div style="margin: 0 0 30px;">
    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended'),
            'sender'    => [
                'name'     => 'enter',
                'position' => 'Viewed',
                'from'     => 'categoryPage',
            ],
        ]) ?>
    <? endif ?>
</div>