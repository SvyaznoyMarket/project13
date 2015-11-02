<?php
/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity $order
 * @param array $creditData
 * @param \Model\CreditBank\Entity[] $banks
 * @param array $creditDoneOrderIds
 * @param bool $isStatic
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    $creditData,
    $banks,
    $creditDoneOrderIds = [],
    $isStatic = true // временной костыль, FIXME
) {
    if (in_array($order->id, $creditDoneOrderIds)) {
        return '';
    }
?>

    <!-- Блок оплата в кредит -->
    <div id="credit-<?= md5($order->id ?: uniqid()) ?>" class="orderPayment orderPayment--static orderPaymentCr jsCreditBlock">
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
                        <li class="orderPaymentCr_lst-i" data-value="<?= $bank->getId() ?>" data-bank-provider-id="<?= $bank->getProviderId() ?>" data-order-id="<?= $order->getId() ?>">
                            <div class="orderPaymentCr_lst_l">
                                <div><span class="undrl"><?= $bank->getName() ?></span></div>
                                <a href="<?= $bank->getLink() ?>" target="_blank" class="pb-small undrl jsCreditListOnlineMotivRules">Условия кредитования</a>
                            </div>

                            <div class="orderPaymentCr_lst_r">
                                <img class="orderPaymentCr_lst_bank-logo" src="<?= $bank->getImage() ?>">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3">Заполнить</button>
                            </div>
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