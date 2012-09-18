<?php
/** @var $item ProductEntity */
?>
<!-- всплывающее окно с выбором доп.гарантии-->
<div class="hideblock mGoods extWarranty">
	<i class="close" title="Закрыть">
		Закрыть
	</i>
	<h2>
		Выбор дополнительной гарантии
	</h2>
	<div>
		<table>
			<tbody>

        <?php foreach($item->getWarrantyList() as $warranty): ?>
				<tr>
					<td class="bF1Block_eInfo">
						Год гарантии<br>
						<a href="#">
							Подробнее об услуге
						</a>
					</td>
					<td class="bF1Block_eBuy">
						<span class="bF1Block_ePrice">
  1 500&nbsp;
							<span class="rubl">
  p
							</span>
						</span>
						<input class="button yellowbutton" data-ewid="1" data-ewtitle="Год гарантии" data-ewprice="1500" type="button" value="Выбрать">
					</td>
				</tr>
        <?php endforeach ?>

			</tbody>
		</table>
	</div>
</div>
<!-- END всплывающее окно с выбором доп.гарантии-->
