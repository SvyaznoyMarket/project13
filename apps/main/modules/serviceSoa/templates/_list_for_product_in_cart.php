<?php if (count($list)):
  $num = 0;
  ?>
<?php
  $servListId = array();
  foreach ($list as $service) {
    if (!$service['selected']) continue;
    $servListId[] = $service['id'];
  }
  ?>



<div class="service form bBacketServ mSmall mBR5" <?php if ($selectedNum) echo ' style="display:none;"';  ?> >
  <table cellspacing="0">
    <tbody>
    <tr>
      <th colspan="3">Для этого товара есть услуги:</th>
    </tr>
      <?php foreach ($list as $service): ?>
      <?php if ($num == 3) break; ?>
    <tr>
      <td><?php echo $service['name'] ?></td>
      <td class="mPrice"></td>
      <td class="mEdit"></td>
    </tr>
      <?php $num++; ?>
      <?php endforeach; ?>

    <tr>
      <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
      <td></td>
      <td></td>
    </tr>
    </tbody>
  </table>
</div>
<?php if (!$selectedNum) { ?>
<div class="service form bBacketServ mBig mBR5" style="display:none;">
  <table cellspacing="0">
    <tbody>
    <tr>
      <th colspan="3">Для этого товара есть услуги:</th>
    </tr>
    <tr>
      <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
      <td></td>
      <td></td>
    </tr>
    </tbody>
  </table>
</div>
<?php } ?>
<?php if ($selectedNum) { ?>
<div class="service form bBacketServ mBig mBR5">
  <table cellspacing="0">
    <tbody>
    <tr>
      <th colspan="3">Для этого товара есть услуги:</th>
    </tr>
      <?php foreach ($list as $service): ?>
      <?php if (!$service['selected'] || !$service['quantity']) continue; ?>
    <tr ref="<?php echo $service['token'] ?>">
      <td>
        <?php echo $service['name'] ?><br>
        <a class="bBacketServ__eMore"
           href="<?php echo url_for('service_show', array('service' => $service['token'])); ?>">Подробнее об услуге</a>
      </td>
      <td class="mPrice"><span class="price"><?php echo $service['totalFormatted'] ?></span>&nbsp;<span
        class="rubl">p</span></td>
      <td class="mEdit">
        <div class="numerbox mInlineBlock mVAMiddle">
          <?php if ($service['quantity'] > 1) { ?>
          <a
            href="<?php echo url_for('cart_service_add', array('service' => $service['id'], 'quantity' => -1, 'product' => $product['id'])); ?>">
            <b title="Уменьшить" class="ajaless"></b>
          </a>
          <?php } else { ?>
          <a href="#"
             ref="<?php echo url_for('cart_service_add', array('service' => $service['id'], 'quantity' => -1, 'product' => $product['id'])); ?>">
            <b title="Уменьшить" class="ajaless"></b>
          </a>
          <?php } ?>
          <span class="ajaquant"><?php echo $service['quantity'] ?> шт.</span>
          <a
            href="<?php echo url_for('cart_service_add', array('service' => $service['id'], 'product' => $product['id'], 'quantity' => 1)); ?>">
            <b title="Увеличить" class="ajamore"></b>
          </a>
        </div>
        <a class="button whitelink ml5 mInlineBlock mVAMiddle"
           href="<?php echo url_for('cart_service_delete', array('service' => $service['id'], 'product' => $product['id'])); ?>">Отменить</a>
      </td>
    </tr>
      <?php endforeach; ?>
    <tr>
      <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
      <td></td>
      <td></td>
    </tr>
    </tbody>
  </table>
</div>

<?php } ?>

<?php include_component('product', 'f1_lightbox', array('f1' => $list, 'product' => $product, 'servListId' => $servListId, 'parentAction' => $this->getActionName())) ?>
<?php endif ?>