<?php

return function (
    \Helper\TemplateHelper $helper,
    \View\Order\NewForm\Form $form,
    array $paymentGroups,
    array $banks,
    array $creditData
) {
    /**
     * @var \Model\PaymentMethod\Group\Entity[] $paymentGroups
     * @var \Model\CreditBank\Entity[]          $banks
     */
?>


    <? foreach ($paymentGroups as $groupId => $group):
        /** @var \Model\PaymentMethod\Entity[] $paymentMethods */
        $blockData = array(
            'toHide' => [
                // Для пикпойнтов нужно скрыть любой* payBlock (кроме дефолтного TYPE_ON_RECEIPT)

                // Скрыть для всех* PaymentMethods при типе доставки PICKPOINT:
              \Model\DeliveryType\Entity::TYPE_PICKPOINT_ID,

                // Либо перичислим те PaymentMethods, для которых скрываем при типе доставки PICKPOINT
                /*
                \Model\DeliveryType\Entity::TYPE_PICKPOINT_ID=> [
                    \Model\PaymentMethod\Entity::TYPE_NOW,
                    \Model\PaymentMethod\Entity::TYPE_ALL,
                ],
                */

            ],
            'groupId' => $groupId,
        );
        ?>
        <div data-vars="<?= $helper->json($blockData) ?>"<? // div блока с возможными методами оплаты: "Прямо сейчас", "При получении", ...
        if ( \Model\PaymentMethod\Entity::TYPE_ON_RECEIPT !== $groupId ):
//      TYPE_ON_RECEIPT — при получении заказа, доступно в любом случае
        ?>data-bind="payBlockVisible: <?= $groupId ?>"<?
        endif ?>>
            <? if ($group->getName() && \Model\PaymentMethod\Entity::TYPE_ALL !== $groupId): ?>
                <h2 class="bTitle"><?= $group->getName() ?></h2>
            <? endif ?>

            <? foreach ($group->getPaymentMethods() as $paymentMethod):
                /*  @var $paymentMethod  \Model\PaymentMethod\Entity */
                $elementId = sprintf('paymentMethod-%s', $paymentMethod->getId());
                ?>
                <div class="bPayMethod<? if (\Model\PaymentMethod\Entity::TYPE_ALL == $groupId): ?> mMethodOption<? endif ?>"
                     data-value="<?= $helper->json([
                         'min-sum' => $paymentMethod->getIsCredit() ? \App::config()->product['minCreditPrice'] : null,
                         'max-sum' => in_array($paymentMethod->getId(), [\Model\PaymentMethod\Entity::QIWI_ID, \Model\PaymentMethod\Entity::WEBMONEY_ID]) ? App::config()->order['maxSumOnline'] : null,
                         'method_id' => $paymentMethod->getId(),
                         'isAvailableToPickpoint' => $paymentMethod->getIsAvailableToPickpoint(),
                     ]) ?>"
                     data-bind="paymentMethodVisible: totalSum">
                    <input
                        <? if ($paymentMethod->getId() == $form->getPaymentMethodId()): ?> checked="checked"<?endif ?>
                        class="jsCustomRadio bCustomInput mCustomRadioBig" id="<?= $elementId ?>"
                        type="radio"
                        name="order[payment_method_id]"
                        value="<?= $paymentMethod->getId() ?>"
                        />

                    <label for="<?= $elementId ?>" class="bCustomLabel mCustomLabelRadioBig"><?= $paymentMethod->getName() ?></label>

                    <? if ($paymentMethod->getDescription()): ?>
                        <div class="bPayMethodDesc"><?= $paymentMethod->getDescription() ?></div>
                    <? endif ?>

                    <? if ($paymentMethod->getIsCredit()): ?>
                        <?= $helper->render('order/newForm/__paymentMethod-credit', ['paymentMethod' => $paymentMethod, 'banks' => $banks, 'creditData' => $creditData]) ?>
                    <? elseif ($paymentMethod->isCertificate()): ?>
                        <?= $helper->render('order/newForm/__paymentMethod-certificate', ['paymentMethod' => $paymentMethod]) ?>
                    <? elseif ($paymentMethod->isQiwi()): ?>
                        <?= $helper->render('order/newForm/__paymentMethod-qiwi', ['paymentMethod' => $paymentMethod]) ?>
                    <? endif ?>
                </div>
            <? endforeach ?>
        </div>
    <? endforeach ?>
<? };