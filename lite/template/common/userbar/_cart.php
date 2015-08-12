<div class="header__side header-cart table-cell js-cart <? if (\App::user() && \App::user()->getCart()->count()) : ?><? endif ?>">

    <div class="notice-show">
        <a class="header-cart__link" href="<?= \App::router()->generate('cart') ?>" title="">
            <i class="header-cart__icon i-controls i-controls--cart"><span class="header-cart__count disc-count js-cart-quantity"></span></i>
            <span class="header-cart__text">Корзина</span>
        </a>

        <div class="notice-dd notice-dd_cart js-cart-notice">
            <div class="notice-dd__inn js-cart-notice-content">
                <div class="notice-dd__height">
                    <ul class="notice-cart js-cart-items-wrapper"></ul>
                </div>
            </div>

            <a href="<?= \App::router()->generate('cart') ?>" class="notice-cart__on-cart btn-simple btn-simple_width">Перейти в корзину</a>
            <a href="<?= \App::router()->generate('orderV3') ?>" class="btn-primary btn-primary_bigger btn-primary_width">Оформить заказ</a>
        </div>
    </div>
</div>

<script type="text/html" id="js-cart-item-template">
    <li class="notice-cart__row">
        <a class="notice-cart__img notice-cart__cell" href="{{link}}">
            <img alt="" src="{{img}}" class="image">
        </a>

        <a class="notice-cart__name notice-cart__cell" href="{{link}}"><span class="notice-cart__name-inn">{{name}}</span></a>

        <div class="notice-cart__desc notice-cart__cell">
            <div class="notice-cart__price">{{formattedPrice}} <span class="rubl-css">P</span></div>
            <span class="notice-cart__quan">{{quantity}} шт.</span>
            <a class="notice-cart__del js-cart-item-delete" href="{{deleteUrl}}"><i class="notice-cart__icon icon-clear"></i></a>
        </div>
    </li>
</script>