<?php

return function(
    \Helper\TemplateHelper $helper,
    $message
) {
    /**
     * @var $user \Model\User\Entity|null
     */
    ?>

    <?= $helper->render('order-v3/lifegift/_header') ?>

    <section class="orderLgift">
    <div class="orderLgift_hd">
        <h1 class="orderLgift_t">ВЫ ОФОРМИЛИ ЗАКАЗ В ПОДАРОК РЕБЕНКУ, КОТОРОГО ПОДДЕРЖИВАЕТ ФОНД "ПОДАРИ ЖИЗНЬ".</h1>
        <p class="orderLgift_slgn"><?= $message ?></p>
    </div>
    <div class="orderCompl orderCompl_final clearfix">
        <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
    </div>
    </section>

<? } ?>