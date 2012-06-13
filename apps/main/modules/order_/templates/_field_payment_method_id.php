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
      <i><?php echo $v['description'] ?></i>
    </div>
    <?php if ($v['is_credit']) { ?>
          <div class="bSelect mFastInpSmall">
              <span >самовывоз</span>
              <div class="bSelect__eArrow"></div>
              <div class="bSelect__eDropmenu" style="display: none;">
                  <?php foreach ($v['credit_bank'] as $bank) { ?>
                      <div ><span data-bind="text: name"><?php echo $bank->getName();?></span></div>
                  <?php } ?>
              </div>
          </div>
    <?php } ?>

  <?php endforeach ?>
  </dd>
</dl>
