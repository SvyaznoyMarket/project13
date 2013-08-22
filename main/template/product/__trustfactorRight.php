<?php
return function(
    $trustfactorRight
) {
?>

  <? foreach ($trustfactorRight as $trustfactorRightItem) { ?>
    <div class="trustfactorRight">
      <?= empty($trustfactorRightItem) ? '' : $trustfactorRightItem ?>
    </div>
  <? } ?>

<? };