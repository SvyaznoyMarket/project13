<?
    $helper = new \Helper\TemplateHelper();
    $region = \App::user()->getRegion();
?>

<? if (\App::request()->getPathInfo() !== '/delivery' && $region->pointCount): ?>
    <?
        // TODO удалить даный блок if после реализации FCMS-779
        if ($region->name) {
            $scmsResponse = \App::scmsClient()->query('api/word-inflect', ['names' => [$region->name]], []);

            if (isset($scmsResponse[$region->name])) {
                $region->names = new \Model\Inflections($scmsResponse[$region->name]);
            }
        }
    ?>

    <a class="header__bann stripe-bann" href="<?= \App::helper()->url('delivery') ?>">
        Бесплатный самовывоз из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точка', 'точки', 'точек']) ?> <? if ($region->names->locativus): ?>в <?= $helper->escape($region->names->locativus) ?><? endif ?>.
        <span class="stripe-bann__small">Для заказов от 1990 <span class="rubl">p</span></span>
    </a>
<? endif ?>