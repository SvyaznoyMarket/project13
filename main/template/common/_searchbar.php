<?
/**
 * @var $page \View\DefaultLayout
 * @var $menu \Model\Menu\BasicMenuEntity[]|null
 */
$menu = $page->getGlobalParam('menu');
$btnTypes = array(
        1 => "hdgift--old",
        2 => "hdgift--new",
        3 => "hdgift--new hdgift--new-cursive"
    );
?>

<!-- поиск -->
<div class="header__width">
    <div class="header__inn clearfix">
        <div class="header_c header_c-v2">
            <a href="/" class="hdlogo sitelogo"></a>

            <div class="hdsearch jsKnockoutSearch" data-bind="css: { 'hdsearch-v2': advancedSearch }">
                <!--noindex-->
                <form action="<?= $page->url('search')?>" class="hdsearch_f">

                    <label class="hdsearch_lbl" for="">Все товары для жизни по выгодным ценам!</label>

                    <div class="hdsearch_itb" data-bind="css: { 'hdsearch_itb-focus': searchFocus() }">

                        <? if ($menu) : ?>

                        <div class="searchcat" style="display: none" data-bind="visible: advancedSearch">

                            <div class="searchcat_tl" data-bind="css: { 'searchcat_tl-act': !searchCategoryVisible() }, click: toggleCategoryVisibility">
                                <span class="searchcat_tl_tx" data-bind="text: currentCategory() == null ? 'Все товары' : currentCategory().name "></span>
                            </div>

                            <ul class="searchcat_dd" data-bind="visible: searchCategoryVisible()">

                                <li class="searchcat_dd_i" data-bind="visible: currentCategory() != null">
                                    <span class="undrlh">
                                        <a href="" class="searchcat_dd_lk" data-bind="click: categoryReset, clickBubble: false">Все товары</a>
                                    </span>
                                </li>

                                <? foreach ($menu as $item) : ?>
                                <?  if ($item->id == null) continue; ?>

                                <li class="searchcat_dd_i">
                                    <span class="undrlh">
                                        <a href="" class="searchcat_dd_lk"
                                           data-value="<?= $page->json(['id' => $item->id, 'name' => $item->name]) ?>"
                                           data-bind="click: categoryClick, clickBubble: false"><?= $item->name ?></a>
                                    </span>
                                </li>

                                <? endforeach; ?>
                            </ul>
                        </div>

                        <? endif; ?>

                        <div class="hdsearch_itw">
                            <input type="text"
                                class="hdsearch_it jsSearchInput"
                                name="q"
                                placeholder="Поиск товаров"
                                autocomplete="off"
                                data-bind="value: searchInput, valueUpdate: ['input', 'afterkeydown'], hasFocus: searchFocus, event: { keydown: searchResultNavigation }" /></div>
                        </div>

                        <?= $page->blockInputCategory() ?>

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
                    </a>
                </div>
                <!--/ саджест поиска -->
            </div>

            <div class="hdep">
                <div class="hdep_h">Больше скидок</div>
                <a href="<?= \App::router()->generate('enterprize') ?>" class="i-header i-header--ep jsEnterprizeInSearchBarButton"></a>
            </div>

            <? if (false): // SITE-5833 ?>
            <div class="hdgift <?= $btnTypes[ $page->escape(\Session\AbTest\ABHelperTrait::getGiftButtonNumber()) ] ?>">
                <a class="hdgift_i hdgift_lk jsGiftInSearchBarButton" href="<?= \App::router()->generate('product.gift') ?>">
                    <img class="hdgift_i hdgift_img" src="/styles/header/img/icon-gift.png" alt="">
                    <span class="hdgift_i hdgift_tx">Выбери подарки!</span>
                </a>
            </div>
            <? endif ?>
        </div>
    </div>
</div>
<!--/ поиск -->