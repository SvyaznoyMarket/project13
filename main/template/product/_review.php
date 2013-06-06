<? if($review['origin'] != 'enter') { ?>
  <noindex><div class="comments">
<? } ?>
<div class="clearfix productReview<?= $last ? '' : ' bottomLine'?>">
  <div class="commentAuthtor">
    <div class="commentAuthtor__star"><?= $page->render('product/_starsFive', ['score' => $review['star_score'], 'emptyText' => 'нет оценки']) ?></div>  
    <h3 class="commentAuthtor__name"><?= empty($review['author']) ? $review['source_name'] : $review['author'] ?></h3>
    <div class="commentAuthtor__date">
      <?= $page->helper->dateToRu($review['date']) ?>
    </div>
  </div>

  <div class="commentWrap">
    <? if($review['origin'] != 'enter') { ?>
      <div class="commentWrap__text commentWrap__quote clearfix"><span class="mark">&#171;</span><div class="p"><?= $page->helper->nofollowExternalLinks($review['extract']) ?></div></div>
    <? } else { ?>
      <div class="commentWrap__text clearfix"><span class="mark">&#171;</span><p><?= $review['extract'] ?></p></div>
    <? } ?>
    <? if(!empty($review['pros'])) { ?>
      <div class="commentWrap__text commentWrap__plus clearfix">
          <span class="mark">+</span><p><?= str_replace(';', '<br>', $review['pros']) ?></p>
      </div>
    <? } ?>
    <? if(!empty($review['cons'])) { ?>
      <div class="commentWrap__text commentWrap__minus clearfix">
          <span class="mark">&#8722;</span><p><?= str_replace(';', '<br>', $review['cons']) ?></p>
      </div>
    <? } ?>
  </div>

  <div class="commentLogo">
    <? if(!empty($review['source_logo_url'])) { ?>
      <? if(!empty($review['url']) && $review['type'] == 'pro') { ?>
        <a href="<?= $review['url'] ?>">
      <? } ?>
      <img class="reviewLogo" src="<?= $review['source_logo_url'] ?>">
      <? if(!empty($review['url']) && $review['type'] == 'pro') { ?>
        </a>
      <? } ?>
    <? } ?>
  </div>
</div>
<? if($review['origin'] != 'enter') { ?>
  </div></noindex>
<? } ?>
