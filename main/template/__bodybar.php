<?php
return function(\Helper\TemplateHelper $helper, $showBodybar) { ?>

    <div class="bodybar <?php if (!$showBodybar): ?>bodybar-hide<?php endif ?> js-bodybar">
        <?= $helper->render('__subscribebar') ?>

        <div class="bodybar_clsr js-bodybar-hideButton">&#215;</div>
    </div>

<? };