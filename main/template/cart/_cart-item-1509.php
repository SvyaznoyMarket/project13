<div class="order-cart__item clearfix" ref="" data-product-id="" data-category-id="" data-bind="">
    <a href="" class="order-cart__item-del jsCartDeleteProduct" data-bind="attr: { href: deleteUrl }">Удалить</a>
    <div class="order-cart__img">
        <a class="order-cart__img-lk" href="" data-bind="attr: { href: link }">
            <img src="" alt="" data-bind="attr: { src: img, alt: name}">
        </a>
    </div>

    <div class="order-cart__desc">
        <div class="order-cart__name" data-bind="css: { unavailable: !isAvailable }">
            <a class="order-cart__name-lk" href="" data-bind="text: name, attr: {href: link}"></a>
            <!-- ko if: isAvailable -->
                <noindex><div class="order-cart__is-available">Есть в наличии</div></noindex>
            <!-- /ko -->
            <!-- ko if: !isAvailable -->
                <noindex><div class="order-cart__is-available">Нет в наличии</div></noindex>
            <!-- /ko -->
            <div class="order-cart__price-counter">
                <div class="order-cart__price">
                    <span class="price" data-bind="html: window.printPrice(price)"></span>
                    <span class="rubl">p</span>
                </div>
                <div class="order-cart__count count jsCartNumber">
                        <a href="" data-bind="attr: { href: decreaseUrl }" class="count__ctrl count__ctrl--less ajaxLess jsCartNumberBoxLess" title="Уменьшить">−</a>
                        <input type="text" maxlength="2" class="ajaquant jsCartNumberBoxInput count__num" value="" data-bind="value: quantity(), attr: {'data-product-ui': ui, value: quantity()}">
                        <a href="" data-bind="attr: { href: increaseUrl }" class="count__ctrl count__ctrl--more ajaxMore jsCartNumberBoxMore" title="Увеличить">+</a>
                </div>
                <span class="order-cart__units">шт.</span>
            </div>
        </div>
    </div>
    <div class="order-cart__info">

        <div class="order-cart__total">
            <span class="order-cart__total-sum" data-bind="html: window.printPrice(price * quantity())"></span> <span class="rubl">p</span>
        </div>
    </div>

</div>