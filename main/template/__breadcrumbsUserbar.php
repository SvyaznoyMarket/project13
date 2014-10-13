<? return function(
    array $links = []
) { ?>
    <ul class="topbarfix_crumbsList">
        <? foreach ($links as $link): ?>
            <li class="topbarfix_crumbsListItem<? if ($link['last']): ?> mLast<? endif ?>">
                <? if ($link['url']): ?>
                    <a class="topbarfix_crumbsListLink" href="<?= $link['url'] ?>"><?= $link['name'] ?></a>
                <? else: ?>
                    <?= $link['name'] ?>
                <? endif ?>
            </li>
        <? endforeach ?>
    </ul>
<? };