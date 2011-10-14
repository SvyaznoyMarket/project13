<?php if (false): ?>
<table class="table">
  <tr>
    <th>Товар</th>
    <th>Количество</th>
    <th>Услуги F1</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($list as $item): ?>
  <tr>
    <td><?php echo $item['name'] ?></td>
    <td><?php echo $item['quantity'] ?></td>
    <td>
      <?php foreach ($item['service'] as $service): ?>
      <?php echo "[".$service['quantity']."] ".$service['name']." [".link_to('добавить', 'cart_service_add', array('product' => $item['token'], 'service' => $service['token'], 'quantity' => 1, ))."]"." [".link_to('удалить', 'cart_service_delete', array('product' => $item['token'], 'service' => $service['token'], ))."]" ?><br />
      <?php endforeach; ?>
    </td>
    <td><?php echo link_to('удалить', 'cart_delete', array('product' => $item['token']), array('class' => 'cart cart-delete')) ?></td>
  </tr>
  <?php endforeach ?>
</table>
<?php endif ?>
    <!-- Basket -->
  <?php foreach ($list as $item): ?>
    <div class="basketline">
        <div class="basketleft">
            <a href="<?php echo url_for('productCard', $item['product']) ?>"><?php echo $item['photo'] ?></a>
            <?php if (count($item['service'])): ?>
            <div class="ac font11"><a href="" class="f1link">Сервис F1</a> Сервис F1</div>
            <?php endif ?>
        </div>
        <div class="basketright">
            <div class="goodstitle">
                <div class="font24 pb5"><?php echo link_to((string)$item['product'], 'productCard', $item['product']) ?></div>
                <div class="font11">Есть в наличии</div>
            </div>
            <div class="basketinfo pb15">
                <div class="left font11">Цена:<br /><span class="font12"><?php echo $item['price'] ?> <span class="rubl">p</span></span></div>
                <div class="right"><div class="numerbox"><?php echo ($item['quantity'] > 1) ? link_to('<b title="Уменьшить"></b>', 'cart_add', array('product' => $item['product']->token, 'quantity' => -1, )) : '<b title="Уменьшить"></b>' ?><span><?php echo $item['quantity'] ?> шт.</span><?php echo link_to('<b title="Увеличить"></b>', 'cart_add', array('product' => $item['product']->token, 'quantity' => 1, )) ?></div></div>
            </div>
            <div class="basketinfo">
                <div class="left font24"><?php echo ($item['total']) ?> <span class="rubl">p</span></div>
                <div class="right"><a href="<?php echo url_for('cart_delete', array('product' => $item['product']->token, )) ?>" class="button whitelink mr5">Удалить</a><!--a href="" class="button whitelink">Добавить в список желаний</a--></div>
            </div>

            <div class="clear pb15"></div>

            <?php if (count($item['service'])): ?>
            <div class="service form">
                <div class="font11 pb10">Выберите услуги:</div>
                <ul>

            <?php foreach ($item['service'] as $service): ?>
            <?php //echo "[".$service['quantity']."] ".$service['name']." [".link_to('добавить', 'cart_service_add', array('product' => $item['token'], 'service' => $service['token'], 'quantity' => 1, ))."]"." [".link_to('удалить', 'cart_service_delete', array('product' => $item['token'], 'service' => $service['token'], ))."]" ?><br />
                    <li>
                        <div class="pricedata" style="display:block">
                            <div class="left"><?php echo $service['price'] ?> <span class="rubl">&#8399;</span></div>
                            <div class="right"><a href="" class="underline">Убрать</a></div>
                        </div>
                        <label for="checkbox-2"><?php echo $service['name'].' ('.$service['price'].' Р)' ?></label><input id="checkbox-2" name="checkbox-1" type="checkbox" value="checkbox-2" checked="checked" />
                    </li>
            <?php endforeach; ?>

                </ul>
            </div>
            <?php endif ?>
        </div>
    </div>
  <?php endforeach ?>
    <!-- /Basket -->
