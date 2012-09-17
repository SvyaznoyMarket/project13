<?php if (count($list)): ?>
<?php
  $num = 0;
  $servListId = array();
  foreach ($list as $service) {
    if (!$service['selected']) continue;
    $servListId[] = $service['id'];
  }
  ?>
<div class="mBR5 basketServices">
<div class="service form bBacketServ F1 mSmall" <?php if ($selectedNum) echo ' style="display:none;"';  ?> >
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
<div class="clear"></div>
<div class="service form bBacketServ extWarr mSmall" style="">
    <table cellspacing="0">
        <tbody>
            <tr>
                <th colspan="3">
                    Для этого товара есть дополнительная гарантия:
                </th>
            </tr>
            <tr>
                <td>
                    Год гарантии
                </td>
                <td class="mPrice"></td>
                <td class="mEdit"></td>
            </tr>
            <tr>
                <td>
                    Два года гарантии
                </td>
                <td class="mPrice"></td>
                <td class="mEdit"></td>
            </tr>
            <tr>
                <td>
                    Три года гарантии
                </td>
                <td class="mPrice"></td>
                <td class="mEdit"></td>
            </tr>
            <tr>
                <td class="bBlueButton">
                    <a href="" class="link1">
                        Выбрать услуги
                    </a>
                </td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
</div>
<?php if (!$selectedNum) { ?>
<div class="mBR5 basketServices">
<div class="service form bBacketServ mBig" style="display:none;">
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
</div>
<?php } ?>
<?php if ($selectedNum) { ?>
<div class="mBR5 basketServices">
<div class="service form bBacketServ F1 mBig">
  <table cellspacing="0">
    <tbody>
    <tr>
      <th colspan="3">Для этого товара есть услуги:</th>
    </tr>
      <?php foreach ($list as $service): ?>
      <?php if (!$service['selected']) continue; ?>
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
<div class="service form bBacketServ extWarr mBig">
    <table cellspacing="0">
        <tbody>
            <tr>
                <th colspan="3">
                    Для этого товара есть гарантия:
                </th>
            </tr>
            <tr ref="utilizatsiya-demontirovannoy-mebeli-2120106000105">
                <td>
                    Три года гарантии
                    <br>
                    <a class="bBacketServ__eMore" href="/f1/show/utilizatsiya-demontirovannoy-mebeli-2120106000105">
                        Подробнее об услуге
                    </a>
                </td>
                <td class="mPrice">
                    <span class="price">
                        2 920
                    </span>
                    &nbsp;<span class="rubl">
                        p
                    </span>
                </td>
                <td class="mEdit">
                    
                    <a class="button whitelink ml5 mInlineBlock mVAMiddle" href="/cart/delete_service/16520/_service/158">
                        Отменить
                    </a>
                </td>
            </tr>
            <tr>
                <td class="bBlueButton">
                    <a href="" class="link1">
                        Выбрать услуги
                    </a>
                </td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
</div>
<?php } ?>

<?php
  include_component('product', 'f1_lightbox', array('f1' => $list, 'product' => $product, 'servListId' => $servListId, 'parentAction' => $this->getActionName()))
  ?>
<?php endif ?>