<dl class='bBuyingLine'>
  <dt>Выберите способ:</dt>
  <dd>
    <?php foreach ($sf_data->getRaw('choices') as $k => $v): ?>
      <div>
        <p></p>
        <label for="<?php echo $v['id'] ?>" class='<?php if ($k == $value) echo 'mChecked' ?>'><b></b> <?php echo $v['label'] ?>
          <input id="<?php echo $v['id'] ?>" class='bBuyingLine__eRadio' name='<?php echo $name ?>' type='radio' value="<?php echo $k ?>" <?php if ($k == $value) echo 'checked="checked"' ?> />
        </label>
        <i><?php echo $v['description'] ?></i>
      </div>
    <?php endforeach ?>

  </dd>

</dl>

