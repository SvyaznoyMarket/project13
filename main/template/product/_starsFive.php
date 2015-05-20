<? if(empty($score) && empty($emptyText)) { ?>
  <? for ($i=5; $i > ceil($score); $i--) { ?>
    <span class="star star-empty"></span>
  <? } ?>
<? } elseif(empty($score) && !empty($emptyText)) { ?>
  <span class="gray"><?= $emptyText ?></span>
<? } else { ?>
  <? for ($i=0; $i < (int)$score; $i++) { ?>
    <span class="star star-fill"></span>
  <? } ?>
  <? if(ceil($score) > $score) { ?>
    <span class="star star-half"></span>
  <? } ?>
  <? for ($i=5; $i > ceil($score); $i--) { ?>
    <span class="star star-empty"></span>
  <? } ?>
<? } ?>
