<? if (\App::request()->getPathInfo() !== '/delivery'): ?>
    <?
    $helper = new \Helper\TemplateHelper();
    
    $region = \App::user()->getRegion();

    // TODO удалить даный блок if после реализации FCMS-779
    if ($region->name) {
        $scmsResponse = \App::scmsClient()->query('api/word-inflect', ['names' => [$region->name]], []);

        if (isset($scmsResponse[$region->name])) {
            $region->names = new \Model\Inflections($scmsResponse[$region->name]);
        }
    }
    ?>

    <a class="header__bann" href="<?= \App::helper()->url('delivery') ?>">
        Бесплатный самовывоз из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точка', 'точки', 'точек']) ?> в <?= $helper->escape($region->names->locativus) ?>. Для заказов от 1990 р.
    </a>
<? endif ?>