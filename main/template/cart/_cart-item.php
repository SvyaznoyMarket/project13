<div class="basketLine basketline clearfix" ref="" data-product-id="" data-category-id="" data-bind="css: { 'not-available': !isAvailable }">
    <div class="basketLine__img">
        <a class="basketLine__imgLink" href="" data-bind="attr: { href: link }">
            <img src="" alt="" data-bind="attr: { src: img, alt: name}">
        </a>

        <!-- ko if: !isAvailable -->
            <noindex><div class="not-available__sticker">Распродано</div></noindex>
        <!-- /ko -->
    </div>

    <div class="basketLine__desc">
        <div class="basketLine__desc__name">
            <a href="" data-bind="text: name, attr: {href: link}"></a>
            <!-- ko if: isAvailable -->
                <noindex><div class="basketLine__desc__available">Есть в наличии</div></noindex>
            <!-- /ko -->
            <!-- ko if: !isAvailable -->
                <noindex><div class="basketLine__desc__notAvailable">Нет в наличии</div></noindex>
            <!-- /ko -->
        </div>

        <div class="basketLine__desc__info basketinfo">
            <div class="descPriceLine">
                <div class="descPriceOne">
                    <span class="price one" data-bind="html: window.printPrice(price)"></span>
                    <span class="rubl">p</span>
                </div>
                <div class="descCount">
                    <div class="numerbox jsCartNumber">
                        <a href="" data-bind="attr: { href: decreaseUrl }" class="ajaxLess jsCartNumberBoxLess"><b class="ajaless" title="Уменьшить"></b></a>
                        <input type="text" maxlength="2" class="ajaquant jsCartNumberBoxInput" value="" data-bind="value: quantity(), attr: {'data-product-ui': ui, value: quantity()}">
                        <a href="" data-bind="attr: { href: increaseUrl }" class="ajaxMore jsCartNumberBoxMore"><b class="ajamore" title="Увеличить"></b></a>
                    </div>
                </div>
            </div>

            <div class="descPrice">
                <span class="price sum" data-bind="html: window.printPrice(price * quantity())"></span> <span class="rubl">p</span>
                <a href="" class="button whitelink jsCartDeleteProduct" data-bind="attr: { href: deleteUrl }">Удалить</a>
            </div>
        </div>
    </div>
</div>