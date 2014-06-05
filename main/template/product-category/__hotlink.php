<?php

return function(
    array $hotlinks,
    $promoStyle
) {

    if (empty($hotlinks)) return '';
?>

<!-- SEO теги -->
<? $hotlinksGroups = [];
foreach ($hotlinks as $hotlink) {
    $hotlinksGroups[$hotlink['group_name']][] = $hotlink;
} ?>

<? foreach($hotlinksGroups as $groupName => $group): ?>
    <?= !empty($groupName) ? "<br><b>$groupName:</b> " : '' ?>
    <ul class="bPopularSection"<? if (!empty($promoStyle['bPopularSection'])): ?> style="<?= $promoStyle['bPopularSection'] ?>"<? endif ?>>
        <? foreach ($group as $hotlink): ?>
            <li class="bPopularSection__eItem"><a class="bPopularSection__eText" href="<?= $hotlink['url'] ?>"><?= $hotlink['title'] ?></a></li>
        <? endforeach ?>
    </ul>
<? endforeach ?>
<!-- SEO теги -->

<? };