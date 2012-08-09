<dl class='bBuyingLine'>
  <dt>Ваш город:</dt>
  <dd>
    <input type="hidden" name="<?php echo $name?>" value="<?php echo $value ?>" />
    <div>
      <p></p>
      <input disabled="disabled" class='bBuyingLine__eText city' type='text' value="<?php echo $displayValue ?>">
      <a id="jsregion" class='bGrayButton' data-url="<?php echo url_for('region_init') ?>" href="<?php echo url_for('region_change', $sf_data->getRaw('region')->core_id) ?>">Другой город</a>
    </div>
  </dd>
</dl>

