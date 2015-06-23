<a class="header__bann" href="/self-points">
    <? if (\App::abTest()->isOrderMinSumRestriction()) : ?>
        <div class="header__bann-tl">БЕСПЛАТНЫЙ САМОВЫВОЗ!</div>
        <div class="header__bann-c">* Минимальная сумма заказа — 1990 <span class="p">p</span></div>
        <div class="header__bann-msg">(25) точек выдачи в (Название города) </div>
    <? else :?>
        <div class="header__bann-tl">САМОВЫВОЗ В ТВОЕМ ГОРОДЕ!</div>
        <div class="header__bann-btn">Найти</div>
        <div class="header__bann-msg">Более 1300 точек по России.</div>
    <? endif; ?>
    <div class="close-btn jsMainOrderSumBannerCloser"></div>
</a>