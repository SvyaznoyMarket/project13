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
    <p class="bCreditAttantion">Кредит не распространяется на услуги F1 и доставку.<br/> Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.</p>

    <div class="bBankWrap">
        <div>Выберите банк:</div>
        <? foreach ($banks as $bank): ?>
            <div class="bSelectInput">
                <input class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="bankId_<?= $bank->getId() ?>" value="<?= $bank->getId() ?>" name="credit_bank"/>
                <label class="bCustomLabel mCustomLabelRadioBig" for="bankId_<?= $bank->getId() ?>">
                    <span class="bSelectInput__eImg" style="background-image: url('<?= $bank->getImage() ?>');"></span>
                    
                    <span class="bSelectInput__eDesc">
                        <!-- <span class="bCreditPay">
                            Ежемесячный платеж <span class="credit_pay"></span> <span class="rubl"> p</span>
                        </span> -->
                        <a class="bBankLink" target="_blank" href="<?= $helper->escape($bank->getLink()) ?>">Условия кредита</a>
                    </span>
                </label>
            </div>
        <? endforeach ?>
    </div>

    <div id="jsCreditBank" data-value="<?= $helper->json($creditData) ?>"></div>
    <input class="hiddenCheckbox" id="selectedBank" hidden name="order[credit_bank_id]">
</div>


<? };