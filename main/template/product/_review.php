<?
/** @var $review \Model\Review\ReviewEntity */
if (!$review->isEnterReview()): ?>
    <noindex>
<? endif ?>

    <div class="bReview clearfix productReview">
        <div class="bReview__eAuthtor">
            <?= $page->render('product/_starsFive', ['score' => $review->scoreStar, 'emptyText' => 'нет оценки']) ?>
            <h3 class="bReview__eAuthtor-name"><?= empty($review->author) ? $review->sourceName : $review->author ?></h3>
            <span class="bReview__eAuthtor-date">
                <?= \Util\Date::strftimeRu('%e %B2 %G', $review->date->format('U')) ?>
            </span>
        </div>

        <div class="bReview__eText">
            <? if($review->isEnterReview()) { ?>
                <span class="mark">&#171;</span>
                <?= empty($review->extract) ? '' : '<div>' . htmlentities($review->extract) . '</div>' ?>
            <? } else { ?>
                <span class="mark">&#171;</span>
                <p><?= $review->extract ?></p>
            <? } ?>

            <? if(!empty($review->pros)) { ?>
                <span class="mark">+</span>
                <p><?= str_replace(';', '<br>', $review->pros) ?></p>
            <? } ?>

            <? if(!empty($review->cons)) { ?>
              <span class="mark">&#8722;</span>
              <p><?= str_replace(';', '<br>', $review->cons) ?></p>
            <? } ?>
        </div>

        <div class="bReview__eLogo">
            <? if (!empty($review->sourceLogoUrl)): ?>
                <? if (!empty($review->sourceUrl) && !$review->isYandexReview()) { ?>
                    <a class="reviewLink <?= $review->origin ?>" href="<?= $review->sourceUrl ?>" title="<?= $review->title ?>" target="_blank">
                <? } ?>
                        <img src="<?= $review->sourceLogoUrl ?>" alt="<?= $review->title ?>" />
                <? if (!empty($review->sourceUrl) && !$review->isYandexReview()) { ?>
                    </a>
                <? } ?>
            <? endif ?>
        </div>
    </div>

<? if (!$review->isEnterReview()): ?>
    </noindex>
<? endif ?>
