<?php
/**
 * @var $page               \View\DefaultLayout
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity
 * @var $catalogCategories  \Model\Product\Category\TreeEntity[]
 * @var $catalogConfig      array
 * @var $slideData          array
 * @var $bannerBottom       string
 * @var $promoContent       string
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

?>

<div class="slider2">
    <?
    // TCHIBO - крутилка разделов Чибо на рутовой странице
    if ((bool)$slideData) {
        echo $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => 'tchibo']);
    } ?>
</div>

<div class="s-sales-grid">
    <div class="s-sales-grid__row grid-3cell cell-h-220">
        <? if ($isCategoriesOddCount && isset($promoContent) && !empty($promoContent)): ?>
            <div class="tchiboCatalogInner">
                <?= $promoContent ?>
            </div>
        <? endif ?>
    </div>
</div>

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

<!--TCHIBO - каталог разделов, баннеров, товаров Чибо -->
<div class="s-sales-grid">
    <div class="s-sales-grid__row grid-2cell cell-h-340">
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
            ?>

            <div class="s-sales-grid__cell">
                <a class="s-sales-grid__link" href="<?= $catalogCategory->getLink() ?>">
                    <img src="<?= $imgSrc ?>" alt="<?= $catalogCategory->getName() ?>" class="s-sales-grid__img">
                    <span class="s-sales-grid-desc">
                        <span class="s-sales-grid-desc__title">
                            <?= $catalogCategory->getName() ?>
                        </span>
                    </span>
                </a>
            </div>
        <? endforeach; ?>
    </div>
</div>

<? if (!empty($bannerBottom)): ?>
    <div class="tchiboCatalogInnerBanner">
        <?= $bannerBottom ?>
    </div> <? /* <!--/ вывод баннера или категории без списка подкатегорий и верхней плашкой-заголовком --> */ ?>
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

<div class="clear"></div>