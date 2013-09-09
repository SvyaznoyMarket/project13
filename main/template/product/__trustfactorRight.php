<?php
return function(
    $trustfactorRight
) {
?>

  <? foreach ((array)$trustfactorRight as $trustfactorRightItem) { ?>
    <div class="trustfactorRight">
      <?= empty($trustfactorRightItem) ? '' : $trustfactorRightItem ?>
    </div>
  <? } ?>

<? };