<?php
use Model\Product\Trustfactor;
/**
 * @param \Helper\TemplateHelper $helper
 * @param Trustfactor[] $trustfactors
 * @param $type
 * @param null $reviewsData
 */
$f = function (
    \Helper\TemplateHelper $helper,
    $trustfactors,
    $type,
    $reviewsData = null
) {
    $reviewCount = !empty($reviewsData['num_reviews']) ? $reviewsData['num_reviews'] : (!empty($reviewsData['review_list']) ? count($reviewsData['review_list']) : 0);

    if (!is_array($trustfactors)) {
        $trustfactors = [];
    }
?>

    <? foreach ($trustfactors as $trustfactor): ?>

        <? /** @var $trustfactor Trustfactor */
            if ($trustfactor->hasTag(Trustfactor::TAG_NEW_PRODUCT_CARD) ||
                $trustfactor->hasTag(Trustfactor::TAG_NEW_PRODUCT_CARD_PARTNER)) continue;
        ?>

        <? if ($trustfactor->media && ($trustfactor->type === $type)): ?>
            <div class="trustfactor-<?= $type ?>" <?= 'top' === $type && (int)@$reviewsData['num_reviews'] ? 'itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"' : '' ?>>
                <? if ('top' === $type && (int)@$reviewsData['num_reviews']) : ?>
                    <? /* Микроразметка для отзывов */?>
                    <span itemprop="ratingValue" style="display:none"><?= !empty($reviewsData['avg_star_score']) ? $reviewsData['avg_star_score'] : '' ?></span>
                    <meta itemprop="reviewCount" content="<?= $reviewCount ?>" />
                    <meta itemprop="bestRating" content="5" />
                <? endif; ?>

                <? if ('image' === $trustfactor->media->provider): ?>
                    <? if (isset($trustfactor->link)): ?>
                        <a id="trustfactor-<?= $type ?>-<?= md5(json_encode([$trustfactor])) ?>" href="<?= $helper->escape($trustfactor->link) ?>" target="_blank">
                    <? endif ?>

                    <? foreach ($trustfactor->media->sources as $source): ?>
                        <? if ('original' === $source->type): ?>
                            <img src="<?= $helper->escape($source->url) ?>" width="<?= $helper->escape($source->width) ?>" height="<?= $helper->escape($source->height) ?>" alt="<?= $helper->escape($trustfactor->alt) ?>" />
                        <? endif ?>
                    <? endforeach ?>

                    <? if (isset($trustfactor->link)): ?>
                        </a>
                    <? endif ?>
                <? elseif ('file' === $trustfactor->media->provider): ?>
                    <? foreach ($trustfactor->media->sources as $source): ?>
                        <? if ('original' === $source->type): ?>
                            <a id="trustfactor-<?= $type ?>-<?= md5(json_encode([$trustfactor])) ?>" href="<?= $helper->escape($source->url) ?>" target="_blank" class="trustfactor-<?= $type ?>-file"><span><?= $helper->escape($trustfactor->alt) ?></span></a>
                            <? break ?>
                        <? endif ?>
                    <? endforeach ?>
                <? endif ?>
            </div>
        <? endif ?>
    <? endforeach ?>

<? }; return $f;