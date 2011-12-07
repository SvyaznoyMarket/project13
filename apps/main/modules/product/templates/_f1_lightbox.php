<?php 
#echo $this->getModule();
#$servListId = (array)$servListId['*value'];
$servListId = $servListId->getRawValue();
?>
<div class="hideblock bF1Block mGoods" style="display: none;">
        <i class="close" title="Закрыть">Закрыть</i> 
		<h2>Добавление услуги F1</h2>
        <?php if (count($f1) > 3) echo '<div>';
        #else echo '<ul>';
        ?>        
		<table>
			<tbody>
              <?php foreach ($f1 as $service):
                  if (!isset($service->id)) $service = ServiceTable::getInstance()->getById($service['id']);
                  ?>                
                    <tr>
                        <td class="bF1Block_eInfo"><?php echo $service->name ?><br>
                            <a href="<?php echo url_for('service_show', array('service'=>$service->token)) ?>">Подробнее об услуге</a>
                        </td>
                        <td class="bF1Block_eBuy" ref="<?php echo $service->token ?>">
                            <?php if ($service->getFormattedPrice()) { ?>
                                <span class="bF1Block_ePrice 
                                      <?php if ($service->getCurrentPrice() < Service::MIN_BUY_PRICE || !$product->getIsInsale()) echo ' mr110'; ?> 
                                      ">
                                    <?php echo $service->getFormattedPrice() ?>
                                    <?php if ((int)$service->getFormattedPrice()) { ?>
                                        &nbsp;<span class="rubl">p</span>                                    
                                   <?php } ?>     
                                </span>
                            <?php } ?>  
                            <?php if ($service->getCurrentPrice() >= Service::MIN_BUY_PRICE && $product->getIsInsale()) { ?>
                                <?php if ($showInCardButton && in_array($service->id, $servListId)) { ?>
                                    <input data-f1title="<?php echo $service->name ?>" data-f1price="<?php echo $service->getFormattedPrice() ?>" data-fid="<?php echo $service->token;?>" 
                                    data-url="<?php echo url_for('cart_service_add', array('service'=>$service->token, 'product' => $product->token)) ?>" type="button" class="button yellowbutton" value="В корзине">
                               <?php } else { ?>
                                    <input data-f1title="<?php echo $service->name ?>" data-f1price="<?php echo $service->getFormattedPrice() ?>" data-fid="<?php echo $service->token;?>" 
                                    data-url="<?php echo url_for('cart_service_add', array('service'=>$service->token, 'product' => $product->token)) ?>" type="button" class="button yellowbutton" value="Купить услугу">
                               <?php } ?>     
                            <?php } ?>    
                        </td>
                    </tr>                
             <?php  endforeach ?>                                  
			<tr>
				<th colspan="2"><a href="<?php echo url_for('service_list') ?>">Подробнее о Сервисе F1</a></th>
			</tr>
		</tbody></table>
        <?php if (count($f1) > 3) echo '</div>';
        #else echo '</ul>';
        ?>
</div>