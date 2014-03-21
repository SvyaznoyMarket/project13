<? $isEnter = ( 'enter' == $review['origin'] );

if (!isset($review['title'])) $review['title'] = '';
if (!isset($review['origin'])) $review['origin'] = '';

if (!$isEnter): ?>
    <noindex>
<? endif ?>

    <div class="bReview clearfix productReview">
        <div class="bReview__eAuthtor">
            <?= $page->render('product/_starsFive', ['score' => $review['star_score'], 'emptyText' => 'нет оценки']) ?>
            <h3 class="bReview__eAuthtor-name"><?= empty($review['author']) ? $review['source_name'] : $review['author'] ?></h3>
            <span class="bReview__eAuthtor-date">
                <?= $page->helper->dateToRu($review['date']) ?>
            </span>
        </div>

        <div class="bReview__eText">
            <? if($review['origin'] != 'enter') { ?>
                <span class="mark">&#171;</span>
                <?= empty($review['extract']) ? '' : '<div>' . htmlentities($review['extract']) . '</div>' ?>
            <? } else { ?>
                <span class="mark">&#171;</span>
                <p><?= $review['extract'] ?></p>
            <? } ?>

            <? if(!empty($review['pros'])) { ?>
                <span class="mark">+</span>
                <p><?= str_replace(';', '<br>', $review['pros']) ?></p>
            <? } ?>

            <? if(!empty($review['cons'])) { ?>
              <span class="mark">&#8722;</span>
              <p><?= str_replace(';', '<br>', $review['cons']) ?></p>
            <? } ?>
        </div>

        <div class="bReview__eLogo">
            <? if (!empty($review['source_logo_url'])): ?>
                <? if (!empty($review['url'])) { ?>
                    <a class="reviewLink <?= $review['origin'] ?>" href="<?= $review['url'] ?>" title="<?= $review['title'] ?>" target="_blank">
                <? } ?>
                        <img src="<?= $review['source_logo_url'] ?>" alt="<?= $review['title'] ?>" />
                <? if (!empty($review['url'])) { ?></a><? } ?>
            <? endif ?>
        </div>
    </div>

<? if (!$isEnter): ?>
    </noindex>
<? endif ?>
