<? if (0) { ?>
<?
    // SITE-5853б SITE-6062

    $helper = new \Helper\TemplateHelper();
    $region = \App::user()->getRegion();

    $pathInfo = \App::request()->getPathInfo();
?>
<? if (in_array($region->id, [
    14974, // Москва
    108136, // Санкт-Петербург
    83210, // Брянск
    96423, // Владимир
    18074, // Воронеж
    124229, // Казань
    148110, // Калуга
    74562, // Курск
    99, // Липецк
    99958, // Нижний Новгород
    18073, // Тверь
    74358, // Тула
    124232, // Чебоксары
    93746, // Ярославль
    13241, // Белгород
    93747, // Иваново
    88434, // Смоленск
    13242, // Орел
    83209, // Тамбов
    10374, // Рязань
])): ?>
    <span class="header__bann stripe-bann">
        Бесплатные <a href="/dostavka">доставка</a> домой и в офис и <a href="/delivery">самовывоз</a> из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?> <? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>
        <span class="stripe-bann__small">Для заказов от 1990 <span class="rubl">p</span></span>
    </span>
<? elseif (in_array($region->id, [
    119623, // Ростов-на-Дону
    124201, // Саратов
    124190, // Краснодар
])): ?>
    <? if ('/dostavka' !== $pathInfo): ?>
        <a class="header__bann stripe-bann" href="/dostavka">
            Бесплатная доставка домой и в офис <? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>
            <span class="stripe-bann__small">Для заказов от 1990 <span class="rubl">p</span></span>
        </a>
    <? endif ?>
<? elseif (in_array($region->parentId, [
    82, // Москва
    14974, // Москва
    83, // Московская область

    14975, // Санкт-Петербург г
    39, // Санкт-Петербург г
    108136, // Санкт-Петербург
    34, // Ленинградская обл

    73, // Белгородская обл
    74, // Брянская обл
    75, // Владимирская обл
    76, // Воронежская обл
    77, // Ивановская обл
    78, // Калужская обл
    79, // Костромская обл
    80, // Курская обл
    81, // Липецкая обл
    18, // Нижегородская обл
    84, // Орловская обл
    98, // Рязанская обл
    86, // Смоленская обл
    87, // Тамбовская обл
    88, // Тверская обл
    89, // Тульская обл
    90, // Ярославская обл
    27, // Чувашская Республика - Чувашия
    24, // Татарстан Респ
])): ?>
    <? if ('/delivery' !== $pathInfo && $region->pointCount): ?>
        <a class="header__bann stripe-bann" href="/delivery">
            Бесплатный самовывоз из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?><? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>.
            <span class="stripe-bann__small">Для заказов от 1990 <span class="rubl">p</span></span>
        </a>
    <? endif ?>
<? endif ?>
<? } ?>
<a class="header__bann stripe-bann" href="/delivery">
    Оплачивать заказы стало Проще!   Платите удобным способом   <span class="payments-bann"><img src="/images/payments-bann.png"></span>
</a>
