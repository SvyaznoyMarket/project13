<?php
return function(
    $trustfactorContent
) {
?>

  <? foreach ((array)$trustfactorContent as $trustfactorItem) { ?>
    <div class="trustfactorContent">
      <?= empty($trustfactorItem) ? '' : $trustfactorItem ?>
    </div>
  <? } ?>

<? };