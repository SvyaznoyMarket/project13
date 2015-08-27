<?
    $helper = new \Helper\TemplateHelper();
    $region = \App::user()->getRegion();

    $pathInfo = \App::request()->getPathInfo();

    // SITE-5853
    $availableParentRegions = [
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
    ];
?>
<? if (in_array($region->parentId, [76/* Воронежская обл */, 90/* Ярославская обл */])): ?>
<?
    // TODO удалить даный блок if после реализации FCMS-779
    if ($region->name) {
        $scmsResponse = \App::scmsClient()->query('api/word-inflect', ['names' => [$region->name]], []);

        if (isset($scmsResponse[$region->name])) {
            $region->names = new \Model\Inflections($scmsResponse[$region->name]);
        }
    }
?>
    <span class="header__bann stripe-bann">
        Бесплатные <a href="/dostavka">доставка</a> домой и в офис и <a href="<?= $helper->url('delivery') ?>">самовывоз</a> из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?> <? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>
        <span class="stripe-bann__small">Для заказов от 1990 <span class="rubl">p</span></span>
    </span>
<? elseif (('/delivery' !== $pathInfo) && $region->pointCount && in_array($region->parentId, $availableParentRegions)): ?>
<?
    // TODO удалить даный блок if после реализации FCMS-779
    if ($region->name) {
        $scmsResponse = \App::scmsClient()->query('api/word-inflect', ['names' => [$region->name]], []);

        if (isset($scmsResponse[$region->name])) {
            $region->names = new \Model\Inflections($scmsResponse[$region->name]);
        }
    }
?>

    <a class="header__bann stripe-bann" href="<?= $helper->url('delivery') ?>">
        Бесплатный самовывоз из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?><? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>.
        <span class="stripe-bann__small">Для заказов от 1990 <span class="rubl">p</span></span>
    </a>
<? endif ?>