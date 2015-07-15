<!--noindex-->

<form action="<?= $page->url('search')?>" class="search-bar jsKnockoutSearch" >

    <i class="search-bar__icon i-controls i-controls--search"></i>

    <input
        name="q"
        type="text"
        class="search-bar__it it jsSearchInput"
        placeholder="Поиск товаров"
        autocomplete="off"
        data-bind="value: searchInput, valueUpdate: ['input', 'afterkeydown'], hasFocus: searchFocus, event: { keydown: searchResultNavigation }" >
    <button class="search-bar__btn btn-normal">Найти</button>

    <div class="search-suggest"
         style="display: none"
         data-bind="visible: searchResultsVisibility() && searchInput().length > 2 && !isNoSearchResult()">

        <ul class="search-suggest-list search-suggest-list_categories" data-bind="visible: searchResultCategories().length > 0">

            <li class="search-suggest-list__title">
                <span class="search-suggest-list__title-text">Категории</span>
            </li>

            <!-- ko foreach:  searchResultCategories -->

            <li class="search-suggest-list__item">
                <a class="search-suggest-list__link" href="" data-bind="attr: { href: link }, html: name"></a>
            </li>

            <!-- /ko -->

        </ul>

        <ul class="search-suggest-list" data-bind="visible: searchResultProducts().length > 0">

            <li class="search-suggest-list__title">
                <span class="search-suggest-list__title-text">Товары</span>
            </li>

            <!-- ko foreach:  searchResultProducts -->

            <li class="search-suggest-list__item">
                <a class="search-suggest-list__link" href="" data-bind="attr: { href: link }">
                    <span class="search-suggest-list__img">
                        <img class="image" src="" alt="" data-bind="attr: { src: image }">
                    </span>
                    <span class="search-suggest-list__text" data-bind="text: name"></span>
                </a>
            </li>

            <!-- /ko -->

        </ul>
    </div>
</form>

<!--/noindex-->