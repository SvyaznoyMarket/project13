<? return function(
    array $links = []
) { ?>
    <ul class="userbar-crumbs-list">
        <? foreach ($links as $link): ?>
            <li class="userbar-crumbs-list__i<? if ($link['last']): ?> userbar-crumbs-list__i--last<? endif ?>">
                <? if ($link['url']): ?>
                    <a class="userbar-crumbs-list__lk" href="<?= $link['url'] ?>"><?= $link['name'] ?></a>
                <? else: ?>
                    <?= $link['name'] ?>
                <? endif ?>
            </li>
        <? endforeach ?>
    </ul>
<? };