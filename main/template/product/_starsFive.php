<? for ($i=0; $i < (int)$score; $i++) { ?>
  <img src="/images/reviews_star.png">
<? } ?>
<? if(ceil($score) > $score) { ?>
  <img src="/images/reviews_star_half.png">
<? } ?>
<? for ($i=5; $i > ceil($score); $i--) { ?>
  <img src="/images/reviews_star_empty.png">
<? } ?>
