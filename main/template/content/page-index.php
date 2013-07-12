<?= $page->getParam('content') ?>

<? if(in_array($page->getParam('token'), ['enter-friends'])) { ?>
  <div class="show_flocktory_popup"></div>
<? } ?>