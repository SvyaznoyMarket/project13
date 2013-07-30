<? foreach ($paymentMethods as $paymentMethod): ?>
<div class="bPayMethodTitle" id="payment_method_<?= $paymentMethod->getId() ?>-field">
    <label class="<? if ($paymentMethod->getId() == $selectedPaymentMethodId) echo 'mChecked' ?>" for="order_payment_method_id_<?= $paymentMethod->getId() ?>">
        <b></b> 
        <div class="bLabel"><?= $paymentMethod->getName() ?></div>
        <input id="order_payment_method_id_<?= $paymentMethod->getId() ?>" class='bBuyingLine__eRadio' name="order[payment_method_id]" type='radio' value="<?= $paymentMethod->getId() ?>" <? if ($paymentMethod->getId() == $selectedPaymentMethodId) echo 'checked="checked"' ?> />
    </label>
        <div class="bPayMethodDesc"><?= $paymentMethod->getDescription() // ?></div>
        <? if ($paymentMethod->getIsCredit() && ($bank = reset($banks))) {  ?>
        <div class="innerType" id="creditInfo" <? if ($paymentMethod->getId() != $selectedPaymentMethodId) echo 'style="display:none"' ?> >
            <div>Выберите банк:</div>
            <div class="bankWrap">
                <div class="bSelectWrap mFastInpSmall fl">
                    <span class="bSelectWrap_eText"><?= $bank->getName() ?></span>
                    <select class='bSelect mFastInpSmall' data-value="<?= $page->json($bankData) ?>">
                    </select>
                </div>

                <div class="fl creditHref"><a target="_blank" href="<?= $bank->getLink() ?>">Условия кредита <span>(<?= $bank->getName() ?>)</span></a></div>
                <div class="clear"></div>
            </div>
            <input type='hidden' name='order[credit_bank_id]' value='<?= $bank->getId(); ?>' />
            <div id="tsCreditCart" data-value="<?= $page->json($creditData) ?>" ></div>
            <!--div>Сумма заказа: <span class="rubl">p</span></div-->
            <div>
                <strong style="font-size:160%; color: #000;">Ежемесячный платеж<sup>**</sup>:
                    <span id="creditPrice"></span> <span class="rubl"> p</span>
                </strong>
            </div>
            <div><sup>**</sup> Кредит не распространяется на услуги F1 и доставку. Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.</div>
        </div>
        <?php } else if ($paymentMethod->isCertificate()) { ?>
        <div class="orderFinal__certificate bPayMethodAction hidden innerType">
            <script type="text/html" id="processBlock">
                <div class="process">
                    <div class="img <%=typeNum%>"></div>
                    <p><%=text%></p>
                    <div class="clear"></div>
                </div>
            </script>
            <div id="sertificateFields">
                <input name="order[cardnumber]" type="text" class="bBuyingLine__eText cardNumber" placeholder="Номер" />
                <input name="order[cardpin]" type="text" class="bBuyingLine__eText cardPin" placeholder="ПИН" />
            </div>
            <div id="processing"></div>
        </div>
        <?php } else if ($paymentMethod->isQiwi()) { ?>
        <div class="orderFinal__qiwi bPayMethodAction hidden innerType">
            <script type="text/html" id="processBlock">
                <div class="process">
                    <div class="img <%=typeNum%>"></div>
                    <p><%=text%></p>
                    <div class="clear"></div>
                </div>
            </script>
            <div class="phonePH qiwi">
                <span class="placeholder">+7</span> 
                <input id="qiwi_phone" class="bBuyingLine__eText mInputLong" name="order[qiwi_phone]" value="<?= $form->getMobilePhone() ?>">
            </div>
            <div id="processing"></div>
        </div>
        <?php } ?>
</div>
<?php endforeach ?>
