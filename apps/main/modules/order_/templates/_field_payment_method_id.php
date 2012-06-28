<dl class='bBuyingLine'>
  <dt>Выберите удобный для вас способ*</dt>

  <dd>
  <?php foreach ($paymentMethodList as $k => $v): ?>
    <div id="payment_method_<?php echo $v->getId() ?>-field">
      <p></p>
      <label class='<?php if ($k == $value) echo 'mChecked' ?>' for="order_payment_method_id_<?php echo $v->getId() ?>">
        <b></b> <?php echo $v->getName() ?>
        <input id="order_payment_method_id_<?php echo $v->getId() ?>" class='bBuyingLine__eRadio' name='<?php echo $name ?>' type='radio' value="<?php echo $v->getId() ?>" <?php if ($k == $value) echo 'checked="checked"' ?> />
      </label>
      <i>
        <div><?php echo $v->getDescription() ?></div>
        <?php if ($v->getIsCredit()) { ?>
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
        <?php } ?>
      </i>
    </div>
  <?php endforeach ?>
  </dd>
</dl>
