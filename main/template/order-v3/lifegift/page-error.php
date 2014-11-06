<?php

return function(
    \Helper\TemplateHelper $helper,
    $error
) {
    /**
     * @var $user \Model\User\Entity|null
     */
    ?>

    <?= $helper->render('order-v3/lifegift/_header') ?>

    <section class="orderLgift">
        <div class="orderLgift_hd">
            <h1 class="orderLgift_t">ВЫ ОФОРМЛЯЕТЕ ЗАКАЗ В ПОДАРОК РЕБЕНКУ, КОТОРОГО ПОДДЕРЖИВАЕТ ФОНД "ПОДАРИ ЖИЗНЬ".</h1>
            <p class="orderLgift_slgn"><?= $error ?></p>
        </div>

<? } ?>