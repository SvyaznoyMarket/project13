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
      <?php echo "[".$service['quantity']."] ".$service['name']." [".link_to('добавить', 'cart_service_add', array('product' => $item['token'], 'service' => $service['token'], 'quantity' => 1, ))."]"." [".link_to('удалить', 'cart_service_delete', array('product' => $item['token'], 'service' => $service['token'], ))."]" ?>
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
            <a href="<?php echo url_for('productCard', $item['product']) ?>"><?php echo image_tag($item['photo']) ?></a>
            <?php if (0 && count($item['service'])): ?>
            <div class="ac font11"><a href="" class="f1link">Сервис F1</a> Сервис F1</div>
            <?php endif ?>
        </div>
        <div class="basketright">
            <div class="goodstitle">
                <div class="font24 pb5"><?php echo link_to((string)$item['product'], 'productCard', $item['product']) ?></div>
                <noindex><div class="font11">Есть в наличии</div></noindex>
            </div>
            <div class="basketinfo pb15">
                <div class="left font11">Цена:<br /><span class="font12"><span class="price"><?php echo $item['priceFormatted'] ?></span> <span class="rubl">p</span></span></div>
                <div class="right"><div class="numerbox"><?php echo ($item['quantity'] > 1) ? link_to('<b class="ajaless" title="Уменьшить"></b>', 'cart_add', array('product' => $item['product']->token, 'quantity' => -1, )) : '<b class="ajaless" title="Уменьшить"></b>' ?><span class="ajaquant"><?php echo $item['quantity'] ?> шт.</span><?php echo link_to('<b class="ajamore" title="Увеличить"></b>', 'cart_add', array('product' => $item['product']->token, 'quantity' => 1, )) ?></div></div>
            </div>
            <div class="basketinfo">
                <div class="left font24"><span class="sum"><?php echo ($item['total']) ?></span> <span class="rubl">p</span></div>
                <div class="right"><a href="<?php echo url_for('cart_delete', array('product' => $item['product']->token, )) ?>" class="button whitelink mr5">Удалить</a><!--a href="" class="button whitelink">Добавить в список желаний</a--></div>
            </div>

            <div class="clear pb15"></div>

            <?php if ( 0 && count($item['service'])): ?>
                <?php
                 include_component('product', 'f1_lightbox', array('f1' => $item['service'],))  
                ?>            
                <div class="service form">
                    <div class="font11 pb10">Выберите услуги:</div>
                    <ul>

                <?php $num = 0; ?>        
                <?php foreach ($item['service'] as $service): ?>
                <?php
                    if ($num==3) break;
                    if (!$service['price']) continue;
                ?>       
                        <li>
                            <div class="pricedata" style="display:block">
                                <?php if ($service['quantity']>0) { ?>
                                    <div class="left">
                                        <?php echo $service['quantity'] .' шт. '. $service['priceFormatted'] ?>
                                        <span class="rubl">p</span>
                                    </div>
                                    <div class="right">
                                        <a href="<?php echo url_for('cart_service_delete', array('product' => $item['product']->token, 'service' => $service['token'])) ?>" class="underline">Убрать</a>
                                    </div>
                                <?php } ?>
                            </div>
                            <label class="prettyCheckbox checkbox list" for="checkbox-small-<?php echo $service['id'] ?>">
                                    <?php echo $service['name'].' ('.$service['priceFormatted'].' Р)' ?>
                            </label>
                            <input <?php if ($service['quantity']>0) echo 'checked="checked"'; ?> id="checkbox-small-<?php echo $service['id'] ?>" name="service[<?php echo $service['id'] ?>]" type="checkbox" value="1" />
                        </li>
                <?php $num++; ?>        
                <?php endforeach; ?>
                        <li><a class="underline" href="">подробнее</a></li>
                    </ul>
                </div>
            <?php endif ?>
        </div>
    </div>
  <?php endforeach ?>
    <!-- /Basket -->
