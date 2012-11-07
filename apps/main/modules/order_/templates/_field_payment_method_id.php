<dl class='bBuyingLine'>
  <dt>Выберите удобный для вас способ*</dt>

  <dd id="payTypes">
  <?php foreach ($paymentMethodList as $k => $v): ?>
    <?php if (!$showCreditMethod && $v->isCredit()) continue;  ?>
    <?php if ($sf_user->getRegion('region')->getHasTransportCompany() && $v->isCertificate()) continue;  ?>
     <div id="payment_method_<?php echo $v->getId() ?>-field">
      <p></p>
      <label class='<?php if ($v->getId() == $selectedMethodId) echo 'mChecked' ?>' for="order_payment_method_id_<?php echo $v->getId() ?>">
        <b></b> <?php echo $v->getName() ?>
        <input id="order_payment_method_id_<?php echo $v->getId() ?>" class='bBuyingLine__eRadio' name='<?php echo $name ?>' type='radio' value="<?php echo $v->getId() ?>" <?php if ($v->getId() == $selectedMethodId) echo 'checked="checked"' ?> />
      </label>
      <i>
        <div><?php echo $v->getDescription() // ?></div>
        <?php if ($v->isCredit()) {  ?>
          <div class="innerType" id="creditInfo"  <?php if ($v->getId() != $selectedMethodId) echo 'style="display:none"' ?> >
            <div>Выберите банк:</div>
            <div class="bankWrap">
                <div data-value='<?php echo $bankJson; ?>' class="fl bSelect mFastInpSmall">
                    <span > <?php echo reset($creditBankList)->getName(); ?></span>
                    <div class="bSelect__eArrow"></div>
                </div>
                <div class="fl creditHref"><a target="_blank" href="<?php echo reset($creditBankList)->getHref(); ?>">Условия кредита <span>(<?php echo reset($creditBankList)->getName(); ?>)</span></a></div>
                <div class="clear"></div>
            </div>
            <input type='hidden' name='order[credit_bank_id]' value='<?php echo reset($creditBankList)->getId(); ?>' />
            <div id="tsCreditCart" data-value="<?php echo $dataForCredit ?>" ></div>
            <!--div>Сумма заказа: <span class="rubl">p</span></div-->
            <div>
              <strong style="font-size:160%; color: #000;">Ежемесячный платеж<sup>**</sup>:
                  <span id="creditPrice"></span> <span class="rubl"> p</span>
              </strong>
            </div>
            <div><sup>**</sup> Кредит не распространяется на услуги F1 и доставку. Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.</div>
          </div>   
          <?php } else if ($v->isCertificate() && !$sf_user->getRegion('region')->getHasTransportCompany()) { ?>
              <div class="orderFinal__certificate hidden innerType">
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
          <?php } ?>
      </i>
    </div>
  <?php endforeach ?>
  </dd>
</dl>
