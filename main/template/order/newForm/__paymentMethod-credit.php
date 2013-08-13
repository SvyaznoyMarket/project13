<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\PaymentMethod\Entity $paymentMethod,
    array $banks,
    array $creditData
) {
    /**
     * @var \Model\CreditBank\Entity[] $banks
     */
?>

<div class="bPayMethodAction">
    <div>Выберите банк:</div>

    <div class="bBankWrap">
        <div class="bSelectWrap mFastInpSmall">
            <select class="bSelect mFastInpSmall">
            <? foreach ($banks as $bank): ?>
                <option class="bSelect_eItem" value="<?= $bank->getId() ?>" data-link="<?= $helper->escape($bank->getLink()) ?>"><?= $bank->getName() ?></option>
            <? endforeach ?>
            </select>
        </div>

        <a class="bBankLink" target="_blank" href="#">Условия кредита <span>(Тинькофф)</span></a>
    </div>

    <strong>Ежемесячный платеж<sup>**</sup>:
        <span>406</span> <span class="rubl"> p</span>
    </strong>

    <div class="bFootenote">
        <sup>**</sup> Кредит не распространяется на услуги F1 и доставку. Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.
    </div>

    <div id="jsCreditBank" data-value="<?= $helper->json($creditData) ?>"></div>
</div>


<? };