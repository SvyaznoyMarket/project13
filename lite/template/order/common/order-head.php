<!-- шапка оформления заказа -->
<header class="checkout-header">
    <div class="wrapper wrapper-order table">
        <div class="checkout-header__left table-cell">
            <img class="checkout-header__logo" src="/public/images/logo.png">
        </div>

        <? if (empty($hasErrors)): ?>
            <ul class="checkout-header-steps table-cell">
                <li class="checkout-header-steps__item checkout-header-steps__item_title">
                    Оформление заказа
                </li>

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
        <? endif ?>
    </div>
</header>
<!--/ шапка оформления заказа -->