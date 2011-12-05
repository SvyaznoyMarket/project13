<div class="hideblock bF1Block mGoods" style="display: none;">
        <i class="close" title="Закрыть">Закрыть</i> 
		<h2>Добавление услуги F1</h2>
		<table>
			<tbody>
              <?php foreach ($f1 as $service):
                  if (!isset($service->id)) $service = ServiceTable::getInstance()->getById($service['id']);
                  ?>                
                    <tr>
                        <td class="bF1Block_eInfo"><?php echo $service->name ?><br>
                            <a href="<?php echo url_for('service_show', array('service'=>$service->token)) ?>">Подробнее об услуге</a>
                        </td>
                        <td class="bF1Block_eBuy">
                            <?php if ($service->getFormattedPrice()) { ?>
                                <span class="bF1Block_ePrice"><?php echo $service->getFormattedPrice() ?>
                                <?php if ((int)$service->getFormattedPrice()) { ?>
                                &nbsp;<span class="rubl">p</span></span>
                                <?php } ?>
                            <?php } ?>    
                            <input data-url="<?php echo url_for('cart_service_add', array('service'=>$service->token, 'product' => $product->token)) ?>" type="button" class="button yellowbutton" value="Купить услугу">
                        </td>
                    </tr>                
             <?php  endforeach ?>                                  
			<tr>
				<th colspan="2"><a href="<?php echo url_for('service_list') ?>">Подробнее о Сервисе F1</a></th>
			</tr>
		</tbody></table>
</div>