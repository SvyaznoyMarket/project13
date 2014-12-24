<?php
/**
 * @var $trustfactors []
 * @var $reviewsData []|null
 */

$helper  = new \Helper\TemplateHelper();

$trustfactor = null;
foreach ((array)$trustfactors as $t) {
    if (@$t['type'] === 'top') $trustfactor = $t; break;
}

$reviewCount = !empty($reviewsData['num_reviews'])
    ? $reviewsData['num_reviews']
    : (!empty($reviewsData['review_list']) ? count($reviewsData['review_list']) : 0);

?>
<? if ($trustfactor !== null) : ?>
    <div class="trustfactor-top" <?= (int)@$reviewsData['num_reviews'] ? 'itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"' : '' ?>>
        <? if ((int)@$reviewsData['num_reviews']) : ?>
            <? /* Микроразметка для отзывов */?>
            <span itemprop="ratingValue" style="display:none"><?= !empty($reviewsData['avg_star_score']) ? $reviewsData['avg_star_score'] : '' ?></span>
            <meta itemprop="reviewCount" content="<?= $reviewCount ?>" />
            <meta itemprop="bestRating" content="5" />
        <? endif; ?>

        <? if ('image' === $trustfactor['media']['provider']): ?>
            <? if (isset($trustfactor['link'])): ?>
                <a id="trustfactor-top-<?= md5(json_encode([$trustfactor])) ?>" href="<?= $helper->escape($trustfactor['link']) ?>">
            <? endif ?>

            <? foreach ($trustfactor['media']['sources'] as $source): ?>
                <? if ('original' === $source['type']): ?>
                    <img src="<?= $helper->escape($source['url']) ?>" width="<?= $helper->escape($source['width']) ?>" height="<?= $helper->escape($source['height']) ?>" alt="<?= $helper->escape($trustfactor['alt']) ?>" />
                    <? break ?>
                <? endif ?>
            <? endforeach ?>

            <? if (isset($trustfactor['link'])): ?>
                </a>
            <? endif ?>
        <? elseif ('file' === $trustfactor['media']['provider']): ?>
            <? foreach ($trustfactor['media']['sources'] as $source): ?>
                <? if ('original' === $source['type']): ?>
                    <a id="trustfactor-top-<?= md5(json_encode([$trustfactor])) ?>" href="<?= $helper->escape($source['url']) ?>" target="_blank" class="trustfactor-top-file"><span><?= $helper->escape($trustfactor['alt']) ?></span></a>
                    <? break ?>
                <? endif ?>
            <? endforeach ?>
        <? endif ?>
    </div>
<? endif ?>