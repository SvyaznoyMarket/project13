<?php if ( count($list)):
    $num = 0;
?>
<?php
include_component('product', 'f1_lightbox', array('f1' => $list,'product'=>$product))
?>
 <?php if (!$selectedNum) { ?>    
<div class="service form bBacketServ mSmall mBR5">
               <table cellspacing="0">
 					<tbody><tr><th colspan="3">Для этого товара есть услуги:</th></tr>
                        <?php foreach ($list as $service): ?>
                        <?php if ($num==3) break; ?>                           
                            <tr><td><?php echo $service['name'] ?></td>
                                <td class="mPrice"></td> <td class="mEdit"></td>
                            </tr>
                        <?php $num++; ?>        
                        <?php endforeach; ?>                    
                                                                                  
                    <tr>
                        <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
						<td></td><td></td>
                    </tr>
				</tbody></table>
</div>
<?php } else { ?>
<div class="service form bBacketServ mBig mBR5">
               <table cellspacing="0">
 					<tbody><tr><th colspan="3">Для этого товара есть услуги:</th></tr>
                            
                        <?php foreach ($list as $service): ?>                            
                        <?php if (!$service['selected']) break; ?>                           
                            
                        <tr>
                            <td>
                                <?php echo $service['name'] ?><br>
                                <a class="bBacketServ__eMore" href="<?php echo url_for('service_show', array('service'=>$service['token'])); ?>">Подробнее об услуге</a>
                            </td>
                            <td class="mPrice"><?php echo $service['price'] ?>&nbsp;<span class="rubl">p</span></td> 
                            <td class="mEdit">
                                <div class="numerbox mInlineBlock mVAMiddle">
                                    <?php if ($service['quantity'] > 1) { ?>
                                    <a href="<?php echo url_for('cart_service_add', array('service'=>$service['token'], 'quantity'=>-1, 'product'=>$product->token)); ?>">
                                        <b title="Уменьшить" class="ajamore"></b>
                                    </a>
                                    <?php } else { ?>
                                        <b title="Уменьшить" class="ajamore"></b>
                                    <?php } ?>
                                    <span><?php echo $service['quantity'] ?> шт.</span>
                                    <a href="<?php echo url_for('cart_service_add', array('service'=>$service['token'], 'product'=>$product->token)); ?>">
                                        <b title="Увеличить" class="ajamore"></b>
                                    </a>
                                </div>
                                <a class="button whitelink ml5 mInlineBlock mVAMiddle" href="<?php echo url_for('cart_service_delete', array('service'=>$service['token'], 'product'=>$product->token)); ?>">Отменить</a>
                            </td>
                        </tr>                            
                        <?php endforeach; ?>                              
                    <tr>
                        <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
						<td></td><td></td>
                    </tr>
				</tbody></table>
</div>

<?php } ?>        

<?php endif ?>