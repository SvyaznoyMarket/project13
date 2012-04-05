<?php
#echo $this->getModule();
#$servListId = (array)$servListId['*value'];
if (is_object($servListId)) {
    $servListId = $servListId->getRawValue();
}
?>
<div class="hideblock bF1Block mGoods" style="display: none;">
        <i class="close" title="Закрыть">Закрыть</i>
		<h2>Добавление услуги F1</h2>
        <?php
    if (count($f1) > 3) echo '<div>';
        #else echo '<ul>';
        ?>
		<table>
			<tbody>
              <?php foreach ($f1 as $service):
                  if (!isset($service['site_token'])) {
                      continue;
                  }
                  ?>
                    <tr>

                        <td class="bF1Block_eInfo"><?php echo $service['name'] ?><br>
                            <a href="<?php echo url_for('service_show', array('service'=>$service['site_token'])) ?>">Подробнее об услуге</a>
                        </td>
                        <td class="bF1Block_eBuy" ref="<?php echo $service['token'] ?>">
                            <?php if ($service['price']) { ?>
                            <span class="bF1Block_ePrice">
                                    <?php echo $service['priceFormatted'] ?>
                                <?php if ($service['price'] >= 1) { ?>
                                &nbsp;<span class="rubl">p</span>
                                <?php } ?>
                                </span>
                            <?php } ?>
                            <?php if ($service['only_inshop']) { ?>
                                <span class='bF1Block__eInShop'>доступна в магазине</span>
                            <?php } elseif ($service['in_sale'] && in_array($service['id'], $servListId)) { ?>
                            <input data-f1title="<?php echo $service['name'] ?>" data-f1price="<?php echo $service['priceFormatted'] ?>" data-fid="<?php echo $service['token'];?>"
                                   data-url="<?php echo url_for('cart_service_add', array('service'=>$service['id'], 'product' => $productId)) ?>"
                                   type="button" class="active button yellowbutton" value="В корзине">
                            <?php } elseif ($service['in_sale']) { ?>
                            <input data-f1title="<?php echo $service['name'] ?>" data-f1price="<?php echo $service['priceFormatted'] ?>" data-fid="<?php echo $service['token'];?>"
                                   data-url="<?php echo url_for('cart_service_add', array('service'=>$service['id'], 'product' => $productId)) ?>"
                                   type="button" class="button yellowbutton" value="Купить услугу">
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