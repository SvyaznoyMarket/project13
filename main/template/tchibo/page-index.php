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

<div class="tchiboSubscribe subscribe-form clearfix">
    <div class="tchiboSubscribe_title">Узнай первым о новинках и акциях</div>
    <input type="text" placeholder="Введите Ваш e-mail адрес" class="tchiboSubscribe_input subscribe-form__email" name="email" />
    <input type="hidden" value="13" name="channel" />
    <button data-url="<?= $page->url('subscribe.create') ?>" data-error-msg="<?= $page->escape('Вы уже подписаны на рассылку! О всех проблемах сообщайте на my.enter.ru/feedback/') ?>" class="tchiboSubscribe_btn subscribe-form__btn">Подписаться</button>
    <div class="subscribecheck">Хочу получать рассылку о коллекциях Tchibo</div>
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

                        <?  // Шильдики NEW и дата действия коллекции

                        $newCategory = false;
                        $oldCategory = '';

                        if (isset($catalogConfig['category_timing'])
                            && is_array($catalogConfig['category_timing'])
                            && in_array($child->getToken(), array_keys($catalogConfig['category_timing']))) {

                                $catalogTiming = $catalogConfig['category_timing'][$child->getToken()];
                                $until = strtotime($catalogTiming['until']);
                                if (time() < $until) {
                                    if ($catalogTiming['type'] == 'new') $newCategory = true;
                                    if ($catalogTiming['type'] == 'old') $oldCategory = '<br /><span style="color: #e21f26; font-weight: bold">до '.$page->helper->monthDeclension(strftime('%e %B')).'</span>';
                                }
                        }
                        // Шильдики NEW и дата действия коллекции
                        ?>

                        <li class="item <?= $newCategory ? 'new' : '' ?>">
                            <a class="link" href="<?= $child->getLink() ?>">
                                <?= $child->getName().$oldCategory ?>
                            </a>
                        </li>
                        <? endforeach; ?>
                    </ul><? /* <!--/ список подкатегории --> */ ?>
                <? endif; ?>
            </div>
        </div><? /* <!--/ категория --> */ ?>
    <? endforeach; ?>

    <? if ($isCategoriesOddCount && isset($promoContent) && !empty($promoContent)): ?>
        <div class="tchiboCatalogInner">
            <?= $promoContent ?>
        </div>
    <? endif ?>

    <? if (!empty($bannerBottom)): ?>
    <div class="tchiboCatalogInnerBanner">
        <?= $bannerBottom ?>
    </div> <? /* <!--/ вывод баннера или категории без списка подкатегорий и верхней плашкой-заголовком --> */ ?>
    <? endif; ?>
</div>
<!--/ TCHIBO - каталог разделов, баннеров, товаров Чибо -->

<div class="clear"></div>

<div style="margin: 0 0 30px;">
    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'count'     => null,
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