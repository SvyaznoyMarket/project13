<?
/**
 * @var $page \View\DefaultLayout
 */
$btnTypes = array(
        1 => "hdgift--old",
        2 => "hdgift--new",
        3 => "hdgift--new hdgift--new-cursive"
    );
$showCEnterBanner = in_array(\App::user()->getRegion()->parentId, [
    82, // Москва
    14974, // Москва
    83, // Московская область
]);
?>

<!-- поиск -->
<div class="header__width">
    <div class="header__inn clearfix">
        <div class="header_c header_c-v2">
            <? if ('cart' !== (\App::request()->routeName) || (\App::abTest()->isOrderWithCart() && !\App::user()->getCart()->count())): ?>
                <a href="/" class="hdlogo sitelogo"></a>
            <? else: ?>
                <span class="hdlogo sitelogo"></span>
            <? endif ?>

            <div class="hdsearch jsKnockoutSearch">
                <!--noindex-->
                <form action="<?= $page->url('search')?>" class="hdsearch_f">

                    <label class="hdsearch_lbl" for="">Все товары для жизни по выгодным ценам!</label>

                    <div class="hdsearch_itb <? if (!$showCEnterBanner): ?>hdsearch_itb--long<? endif ?>" data-bind="css: { 'hdsearch_itb-focus': searchFocus() }">
                        <div class="hdsearch_itw">
                            <input type="text"
                                class="hdsearch_it jsSearchInput"
                                name="q"
                                placeholder="Поиск среди 80 000 товаров"
                                autocomplete="off"
                                data-bind="value: searchInput, valueUpdate: ['input', 'afterkeydown'], hasFocus: searchFocus, event: { keydown: searchResultNavigation }" /></div>
                        </div>

                    <button class="hdsearch_btn btn3" data-bind="enable: searchInput().length > 1">Найти</button>

        </form>
        <!--/noindex-->

                <!-- саджест поиска -->
                <div class="searchdd jsSearchbarResults" style="display: none;" data-bind="visible: searchResultsVisibility() && searchInput().length > 2 && !isNoSearchResult()">
                    <div class="searchdd_t" data-bind="visible: searchResultCategories().length > 0"><span class="searchdd_t_tx" >Категории</span></div>
                    <!-- ko foreach:  searchResultCategories -->
                    <a href="" class="searchdd_lk jsSearchSuggestCategory" data-bind="attr: { href: link }"><span class="undrlh" data-bind="text: name"></span></a>
                    <!-- /ko -->
                    <div class="searchdd_t" data-bind="visible: searchResultProducts().length > 0"><span class="searchdd_t_tx">Товары</span></div>
                    <!-- ko foreach:  searchResultProducts -->
                    <a href="" class="searchdd_lk jsSearchSuggestProduct" data-bind="attr: { href: link }">
                        <span class="searchdd_img-wrap">
                            <img alt="" class="searchdd_img" data-bind="attr: { src: image }" />
                        </span>
                        <span class="searchdd_tx"><span class="undrlh" data-bind="text: name"></span></span>
                    </a>
                    <!-- /ko -->
                </div>
                <!--/ саджест поиска -->
            </div>

            <? if ($showCEnterBanner): ?>
                <div class="headerBanner">
                    <a href="<?= \App::helper()->url('product.category', ['categoryPath' => 'shop', 'f-shop' => 2]) ?>"><img width="240" height="70" src="/images/cENTER-banner.png" /></a>
                </div>
            <? endif ?>

            <? /*
            <a href="<?= \App::router()->generateUrl('product.category', ['categoryPath' => Model\Product\Category\Entity::FAKE_SHOP_TOKEN]) ?>" class="shops-btn">
                <img class="shops-btn__icon" src="/styles/shops/img/shop-icon.png">
                <span class="shops-btn__txt">Товары<br>в магазине</span>
            </a>
            */ ?>

            <? if (false): // SITE-5833 ?>
            <div class="hdgift <?= $btnTypes[ $page->escape(\Session\AbTest\ABHelperTrait::getGiftButtonNumber()) ] ?>">
                <a class="hdgift_i hdgift_lk jsGiftInSearchBarButton" href="<?= \App::router()->generateUrl('product.gift') ?>">
                    <img class="hdgift_i hdgift_img" src="/styles/header/img/icon-gift.png" alt="">
                    <span class="hdgift_i hdgift_tx">Выбери подарки!</span>
                </a>
            </div>
            <? endif ?>
        </div>
    </div>
</div>
<!--/ поиск -->