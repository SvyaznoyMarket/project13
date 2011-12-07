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
	<script type="text/html" id="f1cartline">
		<tr ref="<%=fid%>">
		<td>
		<%=f1title%>
		<br>
		<a class="bBacketServ__eMore" href="<?php echo url_for('service_show', array('service'=>'F1ID')); ?>">Подробнее об услуге</a>
		</td>
		<td class="mPrice">
		<span class="price"><%=f1price%> </span>
		<span class="rubl">p</span>
		</td>
		<td class="mEdit">
		<div class="numerbox mInlineBlock mVAMiddle">
		<a ref="<?php echo url_for('cart_service_add', array('service'=>'F1ID', 'quantity'=>-1, 'product'=>'PRID')); ?>" href="#">
		<b class="ajaless" title="Уменьшить"></b>
		</a>
		<span class="ajaquant">1 шт.</span>
		<a href="<?php echo url_for('cart_service_add', array('service'=>'F1ID', 'product'=>'PRID')); ?>">
		<b class="ajamore" title="Увеличить"></b>
		</a>
		</div>
		<a class="button whitelink ml5 mInlineBlock mVAMiddle" 
			href="<?php echo url_for('cart_service_delete', array('service'=>'F1ID', 'product'=>'PRID')); ?>">Отменить</a>
		</td>
		</tr>
	</script>   
<?php
/*
$servListId = array();
foreach ($list as $service) {                      
 if ($service['type']!='product') continue;
 $servListId[] = $service['id'];
}*/
?>    
  <?php foreach ($list as $item): ?>
    <?php if ($item['type'] == 'product'): ?>
        <div class="basketline mWrap" ref="<?php echo $item['product']->token ?>">
            <div class="basketleft">
                <a href="<?php echo url_for('productCard', $item['product']) ?>">
                    <?php if (isset($item['photo'])) echo image_tag($item['photo']) ?>
                </a>
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

                
                <?php include_component('service', 'list_for_product_in_cart', array('product' => $item['product'], 'services' => $item['service'])) ?>
            <?php #include_component('product', 'f1_lightbox', array('f1' => $list, 'product'=>$item['product'], 'servListId' => $servListId)) ?>
                                
            </div>
            
    <?php else: ?>
        <div class="basketline mWrap">
            <div class="basketleft">
                <?php
                    if (isset($item['photo'])){
                        echo '<div class="bServiceCard__eLogo"></div>';                            
                    }
                ?>
                <a href="<?php echo url_for('service_show', array('service' => $item['token'])) ?>">
                    <?php
                        if (isset($item['photo'])) echo image_tag($item['photo']);
                        else echo '<div class="bServiceCard__eLogo_free pr_imp"></div>';
                    ?>
                </a>
            </div>
            <div class="basketright">
                <div class="goodstitle">
                    <div class="font24 pb5">
                        <a href="<?php echo url_for('service_show', array('service' => $item['token'])) ?>"><?php echo $item['name'] ?></a>                        
                    </div>
                    <noindex><div class="font11">Есть в наличии</div></noindex>
                </div>
                <div class="basketinfo pb15">
                    <div class="left font11">Цена:<br /><span class="font12"><span class="price"><?php echo (isset($item['priceFormatted'])) ? $item['priceFormatted'] : '' ?></span> <span class="rubl">p</span></span></div>
                    <div class="right">
                        <div class="numerbox">
                            <?php echo ($item['quantity'] > 1) ? link_to('<b class="ajaless" title="Уменьшить"></b>', 'cart_service_add', array('service' => $item['service']->token, 'quantity' => -1, )) : '<b class="ajaless" title="Уменьшить"></b>' ?>
                            <span class="ajaquant">
                            <?php echo $item['quantity'] ?> шт.</span><?php echo link_to('<b class="ajamore" title="Увеличить"></b>', 'cart_service_add', array('service' => $item['service']->token, 'quantity' => 1, )) ?>
                        </div>
                    </div>
                </div>
                <div class="basketinfo">
                    <div class="left font24"><span class="sum"><?php echo $item['total']; ?></span> <span class="rubl">p</span></div>
                    <div class="right"><a href="<?php echo url_for('cart_service_delete', array('service' => $item['token'], )) ?>" class="button whitelink mr5">Удалить</a><!--a href="" class="button whitelink">Добавить в список желаний</a--></div>
                </div>

                <div class="clear pb15"></div>

            </div>
        </div>
    
    <?php endif; ?>
  <?php endforeach ?>
    <!-- /Basket -->
