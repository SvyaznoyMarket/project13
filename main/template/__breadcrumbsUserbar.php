<? return function(
    array $links = []
) { ?>
    <ul class="fixedTopBar__crumbsList">
        <? foreach ($links as $link): ?>
            <li class="fixedTopBar__crumbsListItem<? if ($link['last']): ?> mLast<? endif ?>">
                <? if ($link['last']): ?>
                    <?= $link['name'] ?>
                <? else: ?>
                    <a class="fixedTopBar__crumbsListLink" href="<?= $link['url'] ?>"><?= $link['name'] ?></a>
                <? endif ?>
            </li>
        <? endforeach ?>
    </ul>
<? };