<?php

return function (
    \Helper\TemplateHelper $helper,
    \View\Order\NewForm\Form $form,
    array $paymentMethods,
    array $banks,
    array $creditData
) {
    /**
     * @var \Model\PaymentMethod\Entity[] $paymentMethods
     * @var \Model\CreditBank\Entity[]    $banks
     */

    $typeById = [
        \Model\PaymentMethod\Entity::TYPE_ON_RECEIPT => [
            'name' => 'При получении заказа',
        ],
        \Model\PaymentMethod\Entity::TYPE_NOW        => [
            'name' => 'Прямо сейчас',
        ],

        \Model\PaymentMethod\Entity::TYPE_ALL        => [
            'name' => null,
        ],
    ];

    $paymentMethodsByType = [];
    foreach ($paymentMethods as $paymentMethod) {
        $paymentMethodsByType[$paymentMethod->getPayOnReceipt()][] = $paymentMethod;
    }
?>

    <? foreach ($paymentMethodsByType as $typeId => $paymentMethods): ?>
    <?
        $type = isset($typeById[$typeId]) ? $typeById[$typeId] : null;
        if (!$type) continue;

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
            'typeId' => $typeId,
        );
    ?>
    <div data-vars="<?= $helper->json($blockData) ?>"<? // div блока с возможными методами оплаты: "Прямо сейчас", "При получении", ...
        if ( \Model\PaymentMethod\Entity::TYPE_ON_RECEIPT !== $typeId ):
            // TYPE_ON_RECEIPT — при получении заказа, доступно в любом случае
            ?>data-bind="payBlockVisible: <?= $typeId ?>"<?
        endif ?>>
        <? if (isset($type['name'])): ?>
            <h2 class="bTitle"><?= $type['name'] ?></h2>
        <? endif ?>

        <? foreach ($paymentMethods as $paymentMethod): ?>
        <?
            /*  @var $paymentMethod  \Model\PaymentMethod\Entity */
            $elementId = sprintf('paymentMethod-%s', $paymentMethod->getId());
        ?>
            <div class="bPayMethod<? if (\Model\PaymentMethod\Entity::TYPE_ALL == $typeId): ?> mMethodOption<? endif ?>"
                 data-value="<?= $helper->json([
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