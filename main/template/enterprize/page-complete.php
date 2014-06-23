<?php
/**
 * @var $page \View\DefaultLayout
 */

// TODO - deprecated, по причине таска SITE-3934
?>

<div class="enterPrize">
<div class="enterPrizeListWrap mComplete" style="display: block;">
    <div class="enterPrizeListTitle">Как воспользоваться кодом фишки и получить скидку?</div>
    <?= $page->render('enterprize/__contentHowToGetDiscount') ?>
</div>

<p class="enterprizeMore">Еще фишки! <a href="<?= \App::router()->generate('enterprize') ?>">Посмотреть</a></p>
</div>

﻿



