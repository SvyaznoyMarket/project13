<? return function(
    \Helper\TemplateHelper $helper,
    array $links = []
) { ?>
    <ul class="userbar-crumbs-list">
        <? foreach ($links as $link): ?>
            <li class="userbar-crumbs-list__i<? if ($link['last']): ?> userbar-crumbs-list__i--last<? endif ?>">
                <? if ($link['url']): ?>
                    <a class="userbar-crumbs-list__lk" href="<?= $link['url'] ?>"><?= $helper->escape($link['name']) ?></a>
                <? else: ?>
                    <?= $helper->escape($link['name']) ?>
                <? endif ?>
            </li>
        <? endforeach ?>
    </ul>
<? };