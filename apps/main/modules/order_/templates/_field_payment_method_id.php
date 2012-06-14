<dl class='bBuyingLine'>
  <dt>Выберите удобный для вас способ*</dt>

  <dd>
  <?php foreach ($choices as $k => $v): ?>
    <div id="payment_method_<?php echo $v['token']?>-field">
      <p></p>
      <label class='<?php if ($k == $value) echo 'mChecked' ?>' for="<?php echo $v['id'] ?>">
        <b></b> <?php echo $v['label'] ?>
        <input id="<?php echo $v['id'] ?>" class='bBuyingLine__eRadio' name='<?php echo $name ?>' type='radio' value="<?php echo $k ?>" <?php if ($k == $value) echo 'checked="checked"' ?> />
      </label>
      <i>
        <div><?php echo $v['description'] ?></div>
        <?php if ($v['is_credit']) { ?>
            <div>Выберите банк:</div>
            <div class="bankWrap">
                <div class="fl bSelect mFastInpSmall">
                    <span > <?php echo reset($v['credit_bank'])->getName(); ?></span>
                    <div class="bSelect__eArrow"></div>
                </div>
                <div class="fl creditHref"><a href="#">Условия кредита <span>(<?php echo reset($v['credit_bank'])->getName(); ?>)</span></a></div>
                <div class="clear"></div>
            </div>
        <?php } ?>
      </i>
    </div>
  <?php endforeach ?>
  </dd>
</dl>
