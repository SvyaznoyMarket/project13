<!-- шапка оформления заказа -->
<header class="checkout-header table">
    <div class="checkout-header__left table-cell">
        <img class="checkout-header__logo" src="/public/images/logo.png">
        <div class="checkout-header__title">Оформление заказа</div>
    </div>

    <ul class="checkout-header-steps table-cell">

        <li class="checkout-header-steps__item <?= isset($step) && $step == 1 ? 'active' : null ?>">
            Получатель
        </li>

        <li class="checkout-header-steps__item <?= isset($step) && $step == 2 ? 'active' : null ?>">
            Самовывоз и доставка
        </li>

        <li class="checkout-header-steps__item <?= isset($step) && $step == 3 ? 'active' : null ?>">
            Способы оплаты
        </li>
    </ul>
</header>

<!--/ шапка оформления заказа -->