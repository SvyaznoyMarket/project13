<dl class='bBuyingLine'>
  <dt>Выберите предпочтительный способ</dt>
  <dd>
    <?php foreach ($sf_data->getRaw('choices') as $k => $v): ?>
      <div>
        <p></p>
        <label for="<?php echo $v['id'] ?>" class='<?php if ($k == $value) echo 'mChecked' ?>'><b></b> <?php echo $v['label'] ?>
          <input autocomplete="off" id="<?php echo $v['id'] ?>" class='bBuyingLine__eRadio' name='<?php echo $name ?>' type='radio' value="<?php echo $k ?>" <?php if ($k == $value) echo 'checked="checked"' ?> data-delivery-type="<?php echo $v['type'] ?>" />
        </label>
        <i><?php echo $v['description'] ?></i>
        <?php if ('self' == $v['type']): ?>
          <div class="pl20 pt5">
            <a class="bBigOrangeButton order-shop-button" data-delivery="" style="display: none; font-size: 16px; padding: 6px 30px;" href="#">Выберите магазин</a>
          </div>
        <?php endif ?>
      </div>
    <?php endforeach ?>

  </dd>

</dl>

