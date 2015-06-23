<?
$text = null;
switch (\App::user()->getRegionId()) {
    case 18074:
        $text = '25 точек выдачи в Воронеже';
        break;
    case 99958:
        $text = '19 точек выдачи в Нижнем Новгороде';
        break;
    case 10374:
        $text = '11 точек выдачи в Рязани';
        break;
}

?>

<a class="header__bann" href="/self-points">
    <? if (\App::abTest()->isOrderMinSumRestriction()) : ?>
        <div class="header__bann-tl">БЕСПЛАТНЫЙ САМОВЫВОЗ!</div>
        <div class="header__bann-c">* Минимальная сумма заказа — 1990 <span class="p">p</span></div>
        <div class="header__bann-msg"><?= $text ?></div>
    <? else :?>
        <div class="header__bann-tl">САМОВЫВОЗ В ТВОЕМ ГОРОДЕ!</div>
        <div class="header__bann-btn">Найти</div>
        <div class="header__bann-msg">Более 1300 точек по России.</div>
    <? endif; ?>
    <div class="close-btn jsMainOrderSumBannerCloser"></div>
</a>