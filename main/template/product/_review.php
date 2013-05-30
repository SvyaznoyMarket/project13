<? if($review['origin'] != 'enter') { ?>
  <noindex><div class="comments">
<? } ?>
<div class="productReview<?= $last ? '' : ' bottomLine'?>">
  <div class="fl width140">
    <div><?= $page->render('product/_starsFive', ['score' => $review['star_score']]) ?></div>  
    <h3><?= empty($review['author']) ? $review['source_name'] : $review['author'] ?></h3>
  </div>
  <div class="fl width545 quote">
    <? if($review['origin'] != 'enter') { ?>
      <?= $page->helper->nofollowExternalLinks($review['extract']) ?>
    <? } else { ?>
      <div><?= $review['extract'] ?></div>
    <? } ?>
    <? if(!empty($review['pros'])) { ?>
      <div class="plus mt5">
          <?= str_replace(';', '<br>', $review['pros']) ?>
      </div>
    <? } ?>
    <? if(!empty($review['cons'])) { ?>
      <div class="minus mt5">
          <?= str_replace(';', '<br>', $review['cons']) ?>
      </div>
    <? } ?>
  </div>
  <div class="fr width140">
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
  <div class="clear"></div>
</div>
<? if($review['origin'] != 'enter') { ?>
  </div></noindex>
<? } ?>
