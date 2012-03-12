<dl class='bBuyingLine'>
  <dt>Выберите удобный для вас способ:</dt>

  <dd>
  <?php foreach ($choices as $k => $v): ?>
    <div>
      <p></p>
      <label class='<?php if ($k == $value) echo 'mChecked' ?>' for="<?php echo $v['id'] ?>">
        <b></b> <?php echo $v['label'] ?>
        <input class='bBuyingLine__eRadio' name='<?php echo $name ?>' type='radio' value="<?php echo $k ?>" <?php if ($k == $value) echo 'checked="checked"' ?> />
      </label>
      <i><?php echo $v['description'] ?></i>
    </div>
  <?php endforeach ?>
  </dd>
</dl>
