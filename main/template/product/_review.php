<? if($review['origin'] != 'enter') { ?>
  <noindex>
<? } ?>
<div class="bReview clearfix productReview<?= $last ? '' : ' bottomLine'?>">
  <div class="bReview__eAuthtor">
    <div class="bReview__eAuthtor-star"><?= $page->render('product/_starsFive', ['score' => $review['star_score'], 'emptyText' => 'нет оценки']) ?></div>  
    <h3 class="bReview__eAuthtor-name"><?= empty($review['author']) ? $review['source_name'] : $review['author'] ?></h3>
    <div class="bReview__eAuthtor-date">
      <?= $page->helper->dateToRu($review['date']) ?>
    </div>
  </div>

  <div class="bReview__eText">
    <? if($review['origin'] != 'enter') { ?>
      <div class="bReview__eText-quote bReview__eText__line clearfix">
        <span class="mark">&#171;</span>
        <div class="p"><?= empty($review['extract']) ? '' : $page->helper->nofollowExternalLinks($review['extract']) ?></div>
      </div>
    <? } else { ?>
      <div class="bReview__eText__line clearfix"><span class="mark">&#171;</span><p><?= $review['extract'] ?></p></div>
    <? } ?>
    <? if(!empty($review['pros'])) { ?>
      <div class="bReview__eText-plus bReview__eText__line clearfix">
          <span class="mark">+</span><p><?= str_replace(';', '<br>', $review['pros']) ?></p>
      </div>
    <? } ?>
    <? if(!empty($review['cons'])) { ?>
      <div class="bReview__eText-minus bReview__eText__line clearfix">
          <span class="mark">&#8722;</span><p><?= str_replace(';', '<br>', $review['cons']) ?></p>
      </div>
    <? } ?>
  </div>

  <div class="bReview__eLogo">
    <? if(!empty($review['source_logo_url'])) { ?>
      <? if(!empty($review['url']) && $review['type'] == 'pro') { ?>
        <a href="<?= $review['url'] ?>">
      <? } ?>
      <img class="bReview__eLogo-img" src="<?= $review['source_logo_url'] ?>">
      <? if(!empty($review['url']) && $review['type'] == 'pro') { ?>
        </a>
      <? } ?>
    <? } ?>
  </div>
</div>
<? if($review['origin'] != 'enter') { ?>
</noindex>
<? } ?>
