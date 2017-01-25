<?php
/**
 * @param \Model\Seo\Hotlink\Entity[] $hotlinks
 */
return function(
    array $hotlinks
) {

    if (empty($hotlinks)) return '';
?>

<!-- SEO теги -->
<? $hotlinksGroups = [];
foreach ($hotlinks as $hotlink) {
    $hotlinksGroups[$hotlink->getGroupName()][] = $hotlink;
} ?>

<? foreach($hotlinksGroups as $groupName => $group): ?>
    <?= !empty($groupName) ? "<br><b>$groupName:</b> " : '' ?>
    <ul class="bPopularSection js-seo-list">
        <? foreach ($group as $hotlink): ?>
            <? /** @var \Model\Seo\Hotlink\Entity $hotlink */ ?>
            <li class="bPopularSection__eItem js-seo-list-item"><a class="bPopularSection__eText" href="<?= $hotlink->getUrl() ?>"><?= $hotlink->getName() ?></a></li>
        <? endforeach ?>
    </ul>
<? endforeach ?>
<!-- SEO теги -->

<? };