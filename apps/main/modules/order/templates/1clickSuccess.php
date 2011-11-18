<div class="popupbox">
    <?php if (isset($message)): ?>
    <div class="pl235"><div class="pb10"><strong class="font16"><?php echo $message ?></strong></div></div>
    <?php elseif (isset($paymentForm)): ?>
    <div class="pl235"><div class="pb10">Ваш заказ <?php echo $order->number ?>. Нажмите "Оплатить заказ" и Вы перейдете на страницу оплаты пластиковой картой.</div><form action="<?php echo $paymentForm->getUrl()?>" method="post"><?php echo $paymentForm ?><input type="submit" class="button bigbutton" value="Оплатить заказ" /></form></div>
    <?php else: ?>
    <form class="form order-form" action="<?php echo url_for('order_1click', array('product_id' => $product->id)) ?>" method="post" style="width: 665px;">
      <?php echo $form->renderHiddenFields() ?>

      <div class="fl width215 mr20"><strong class="font16">Способ получения заказа:</strong></div>
        <div class="fl width430">

      <?php if (empty($form->getObject()->region_id)): ?>
        <?php include_component('order', 'field_region_id', array('form' => $form)) ?>
          <input type="submit" value="Подтвердить" />

      <?php else: ?>
        <?php foreach ($form as $name => $field): ?>
          <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
          <?php if (sfContext::getInstance()->getController()->componentExists('order', 'field_'.$name)): ?>
            <?php include_component('order', 'field_'.$name, array('form' => $form)) ?>
          <?php else: ?>
            <?php echo $form[$name]->renderRow(); ?>
          <?php endif ?>

        <?php endforeach ?>
        </div>
            <div class="line pb20"></div>
            <div class="pl235"><input type="submit" class="button bigbutton" value="Оформить заказ" /></div>

      <?php endif ?>
    </form>
    <?php endif ?>
</div>