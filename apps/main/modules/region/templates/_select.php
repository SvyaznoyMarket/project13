<div class="popup" id="region-block" style="width: 640px;">
  <i title="Закрыть" class="close">Закрыть</i>
  <div class="popupbox width694 height250">
    <h2 class="pouptitle">Привет! Укажите, из какого вы города.</h2>

    <form class="ui-css">
      <input id="jscity" data-url-autocomplete="<?php echo url_for('region_autocomplete') ?>" value="" class="bBuyingLine__eText mInputLong" />
      <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton mDisabled" />
      <div id="jscities" style="position:relative"></div>
    </form>

    <div class="pt10">
      <?php include_component('region', 'top_list') ?>
    </div>

  </div>
</div>
