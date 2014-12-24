<?php
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    $creditData,
    $banks
) { ?>

    <!-- Блок оплата в кредит -->
    <div class="orderPayment orderPaymentCr">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Заявка на кредит
                </div>
                <ul class="orderPaymentCr_lst clearfix jsCreditList jsCreditListOnlineMotiv">

                    <? foreach ($banks as $bank) : ?>
                        <? /** @var $bank \Model\CreditBank\Entity */?>
                        <li class="orderPaymentCr_lst-i" data-value="<?= $bank->getId() ?>" data-bank-provider-id="<?= $bank->getProviderId() ?>">
                            <a href="<?= $bank->getLink() ?>">
                                <img class="orderPaymentCr_lst_bank-logo" src="<?= $bank->getImage() ?>">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3 btn3-shadow">Заполнить</button>
                                    <span class="orderPaymentCr_lst_bank"><?= $bank->getName() ?>
                                        <span class="pb-small">Условия кредитования</span>
                                    </span>
                            </a>
                        </li>

                    <? endforeach ?>

                </ul>

                <? if (isset($creditData[$order->getNumber()])) : ?>
                    <div class="credit-widget" data-value="<?= $helper->json($creditData[$order->getNumber()]) ?>"></div>
                <? endif ?>
                <!--<div class="orderPayment_msg_info">
                    <a href="" class="orderPaymentCr_other_link">
                        Не оформлять кредит
                        <span class="pb-small">оплатить онлайн или при получении</span>
                    </a>
                </div>-->
            </div>
        </div>
    </div>



<? }; return $f;