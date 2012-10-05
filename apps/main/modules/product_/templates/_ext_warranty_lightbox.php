<?php
/** @var $item ProductEntity */
?>

<?php
$cart = sfContext::getInstance()->getUser()->getCart();
?>

<?php if (sfConfig::get('app_warranty_enabled')) { ?>
<!-- всплывающее окно с выбором доп.гарантии-->
<div class="hideblock mGoods extWarranty">
	<i class="close" title="Закрыть">Закрыть</i>
	<h2>Выбор дополнительной гарантии</h2>
	<div>
		<table>
			<tbody>

        <?php foreach($item->getWarrantyList() as $warranty): ?>
				<tr>
					<td class="bF1Block_eInfo">
						<?php echo $warranty->getName() ?><br>
						<a href="#">Подробнее об услуге</a>
					</td>
					<td class="bF1Block_eBuy">
						<span class="bF1Block_ePrice"><?php echo $warranty->getPrice() ?>&nbsp;	<span class="rubl">p</span></span>
						<input class="button yellowbutton<?php echo $cart->hasWarranty($item->getId(), $warranty->getId()) ? ' active' : '' ?>" type="button" value="<?php echo $cart->hasWarranty($item->getId(), $warranty->getId()) ? 'Выбрана' : 'Выбрать' ?>"
                            data-ewid="<?php echo $warranty->getId() ?>"
                            data-f1title="<?php echo $warranty->getName() ?>"
                            data-f1price="<?php echo $warranty->getPrice() ?>"
                            data-url="<?php echo url_for('cart_warranty_set', array('product' => $item->getId(), 'warranty' => $warranty->getId())) ?>"
                            data-deleteurl="<?php echo url_for('cart_warranty_delete', array('product' => $item->getId(), 'warranty' => $warranty->getId())) ?>" />
					</td>
				</tr>
        <?php endforeach ?>

			</tbody>
		</table>
	</div>
</div>
<!-- END всплывающее окно с выбором доп.гарантии-->
<? } ?>