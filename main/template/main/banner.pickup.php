<a class="header__bann" href="/self-points">
    <div class="header__bann-tl">БЕСПЛАТНЫЙ САМОВЫВОЗ!</div>
<!--    <div class="header__bann-c">* Минимальная сумма заказа — 1990 <span class="p">p</span></div>-->
    <!-- Кнопка вместо сноски -->
    <div class="header__bann-msg">25 точек выдачи в <?= \App::abTest()->isOrderMinSumRestriction() ? 'Воронеже' : 'Название города' ?></div>
    <div class="close-btn jsMainOrderSumBannerCloser"></div>
</a>