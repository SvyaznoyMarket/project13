<? return function(
    array $links = []
) { ?>
    <ul class="fixedTopBar__crumbsList">
        <? foreach ($links as $link): ?>
            <li class="fixedTopBar__crumbsListItem<? if ($link['last']): ?> mLast<? endif ?>">
                <? if ($link['url']): ?>
                    <a class="fixedTopBar__crumbsListLink" href="<?= $link['url'] ?>"><?= $link['name'] ?></a>
                <? else: ?>
                    <?= $link['name'] ?>
                <? endif ?>
            </li>
        <? endforeach ?>
    </ul>
<? };