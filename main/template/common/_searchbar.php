<?
/**
 * @var $page \View\DefaultLayout
 * @var $menu \Model\Menu\BasicMenuEntity[]|null
 */
$menu = $page->getGlobalParam('menu');
?>

<!-- поиск -->
<div class="header_c clearfix">
    <a href="/" class="header_i sitelogo"></a>

    <!-- для АВ-теста строки поиска, к hdsearch подключить класс hdsearch-v2 -->
    <div class="header_i hdsearch jsKnockoutSearch" data-bind="css: { 'hdsearch-v2': advancedSearch }">
        <form action="<?= $page->url('search')?>" class="hdsearch_f">

            <label class="hdsearch_lbl" for="">Все товары для жизни по выгодным ценам!</label>

            <div class="hdsearch_itb">

                <? if ($menu) : ?>

                <div class="searchcat" style="display: none" data-bind="visible: advancedSearch">
                    <!-- нужно делать .toggleClass('searchcat_tl-act') при клике по - Все товары  -->
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
                        class="hdsearch_it"
                        name="q"
                        placeholder="Поиск по товарам..."
                        data-bind="value: searchInput, valueUpdate: ['input', 'afterkeydown'], hasFocus: searchFocus" /></div>
                </div>

            <button class="hdsearch_btn btn3">Найти</button>

        </form>

        <!-- саджест поиска -->
        <div class="searchdd" style="display: none;" data-bind="visible: searchResultsVisibility() && searchInput().length > 2 && !isNoSearchResult()">
            <div class="searchdd_t" data-bind="visible: searchResultCategories().length > 0"><span class="searchdd_t_tx" >Категории</span></div>
                <!-- ko foreach:  searchResultCategories -->
                <a href="" class="searchdd_lk" data-bind="attr: { href: link }"><span class="undrlh" data-bind="text: name"></span></a>
                <!-- /ko -->
            <div class="searchdd_t" data-bind="visible: searchResultProducts().length > 0"><span class="searchdd_t_tx">Товары</span></div>
                <!-- ko foreach:  searchResultProducts -->
                <a href="" class="searchdd_lk" data-bind="attr: { href: link }">
                    <img alt="" class="searchdd_img" data-bind="attr: { src: image }" />
                    <span class="searchdd_tx"><span class="undrlh" data-bind="text: name"></span></span>
                </a>
                <!-- /ko -->
            </a>
        </div>
        <!--/ саджест поиска -->
    </div>

    <div class="header_i hdep">
        <div class="hdep_h">Больше скидок</div>
        <a href="" class="i-header i-header-ep"></a>
    </div>
</div>
<!--/ поиск -->