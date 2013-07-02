<?
if(!empty($layout) && $layout == 'jewel') {
    $reviewsStarSrc = "/images/jewel/reviews_star.png";
    $reviewsStarHalfSrc = "/images/jewel/reviews_star_half.png";
    $reviewsStarEmptySrc = "/images/jewel/reviews_star_empty.png";
} else {
    $reviewsStarSrc = "/images/reviews_star.png";
    $reviewsStarHalfSrc = "/images/reviews_star_half.png";
    $reviewsStarEmptySrc = "/images/reviews_star_empty.png";
}
?>

<? if(empty($score) && empty($emptyText)) { ?>
  <? for ($i=5; $i > ceil($score); $i--) { ?>
    <img src="<?= $reviewsStarEmptySrc ?>">
  <? } ?>
<? } elseif(empty($score) && !empty($emptyText)) { ?>
  <span class="gray"><?= $emptyText ?></span>
<? } else { ?>
  <? for ($i=0; $i < (int)$score; $i++) { ?>
    <img src="<?= $reviewsStarSrc ?>">
  <? } ?>
  <? if(ceil($score) > $score) { ?>
    <img src="<?= $reviewsStarHalfSrc ?>">
  <? } ?>
  <? for ($i=5; $i > ceil($score); $i--) { ?>
    <img src="<?= $reviewsStarEmptySrc ?>">
  <? } ?>
<? } ?>
