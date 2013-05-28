<div class="productReview<?= $last ? '' : ' bottomLine'?>">
  <div class="fl width140">
    <h3><?= empty($review['author']) ? $review['source_name'] : $review['author'] ?></h3>
  </div>
  <div class="fl width545 quote">
    <?= $review['extract'] ?>
  </div>
  <div class="fr width140">
    <? if(!empty($review['source_logo_url'])) { ?>
      <? if(!empty($review['url'])) { ?>
        <a href="<?= $review['url'] ?>">
      <? } ?>
      <img class="reviewLogo" src="<?= $review['source_logo_url'] ?>">
      <? if(!empty($review['url'])) { ?>
        </a>
      <? } ?>
    <? } ?>
  </div>
  <div class="clear"></div>
</div>
