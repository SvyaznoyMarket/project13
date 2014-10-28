<div class="basketLine basketline clearfix" ref="" data-product-id="" data-category-id="" data-bind="">
    <div class="basketLine__img">
        <a class="basketLine__imgLink" href="" data-bind="attr: { href: link }">
            <img src="" alt="" data-bind="attr: { src: img, alt: name}">
        </a>
    </div>

    <div class="basketLine__desc">
        <div class="basketLine__desc__name">
            <a href="" data-bind="text: name"></a>
            <noindex><div class="basketLine__desc__available">Есть в наличии</div></noindex>
        </div>

        <div class="basketLine__desc__info basketinfo">
            <div class="descPriceLine">
                <div class="descPriceOne">
                    <span class="price one" data-bind="text: window.printPrice(price)"></span>
                    <span class="rubl">p</span>
                </div>
                <div class="descCount">

                    <div class="numerbox">
                        <a href="" data-bind="attr: { href: '/cart/add-product/' + id + '?quantity=' + (quantity() - 1) }" class="ajaxLess"><b class="ajaless" title="Уменьшить"></b></a>
                        <input maxlength="2" class="ajaquant" value="" data-bind="value: quantity()" disabled>
                        <a href="" data-bind="attr: { href: '/cart/add-product/' + id + '?quantity=' + (quantity() + 1) }" class="ajaxMore"><b class="ajamore" title="Увеличить"></b></a>
                    </div>                    </div>
            </div>

            <div class="descPrice">
                <span class="price sum" data-bind="text: window.printPrice(price * quantity())"></span> <span class="rubl">p</span>
                <a href="" class="button whitelink jsCartDeleteProduct" data-bind="attr: { href: '/cart/delete-product/' + id }">Удалить</a>
            </div>
        </div>

        <div class="clear pb15"></div>

    </div>
</div>