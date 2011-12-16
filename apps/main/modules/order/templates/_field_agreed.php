<div class="line pb20"></div>

<div class="fl pl20 pb20">
  <?php echo $form['agreed']->renderError() ?>
  <?php echo $form['agreed']->render() ?><label for="order_agreed" style="font-weight: bold; cursor: pointer;">Я ознакомлен и согласен с <a href="<?php echo url_for('default_show', array('page' => 'terms')) ?>" target="_blank">«Условиями продажи»</a> и <a href="<?php echo url_for('default_show', array('page' => 'legal')) ?>" target="_blank">«Правовой информацией»</a></label>
</div>