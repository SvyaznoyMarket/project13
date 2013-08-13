<?php

return function (
    \Helper\TemplateHelper $helper,
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
    ?>
        <? if (isset($type['name'])): ?>
            <h2 class="bTitle"><?= $type['name'] ?></h2>
        <? endif ?>

        <? foreach ($paymentMethods as $paymentMethod): ?>
        <?
            $elementId = sprintf('paymentMethod-%s', $paymentMethod->getId());
        ?>
            <div class="bPayMethod<? if (\Model\PaymentMethod\Entity::TYPE_ALL == $typeId): ?> mMethodOption<? endif ?>">
                <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="<?= $elementId ?>" type="radio" name="radio_pay" hidden />

                <label for="<?= $elementId ?>" class="bCustomLabel mCustomLabelRadioBig"><?= $paymentMethod->getName() ?></label>

                <? if ($paymentMethod->getDescription()): ?>
                    <div class="bPayMethodDesc"><?= $paymentMethod->getDescription() ?></div>
                <? endif ?>

                <? if ($paymentMethod->getIsCredit()): ?>
                    <?= $helper->render('order/__form-paymentMethod-credit', ['paymentMethod' => $paymentMethod, 'banks' => $banks, 'creditData' => $creditData]) ?>
                <? elseif ($paymentMethod->isCertificate()): ?>
                    <?= $helper->render('order/__form-paymentMethod-certificate', ['paymentMethod' => $paymentMethod]) ?>
                <? elseif ($paymentMethod->isQiwi()): ?>
                    <?= $helper->render('order/__form-paymentMethod-qiwi', ['paymentMethod' => $paymentMethod]) ?>
                <? endif ?>
            </div>
        <? endforeach ?>

    <? endforeach ?>

<? };