<?php
/**
 * @var $item ProductEntity
 */
$list = $item->getServiceList();
$listInCart = $item->getServiceListInCart();
?>
<?php if (count($list)): ?>
  <?php render_partial('product_/templates/_f1_lightbox.php', array('item' => $item))?>

  <div class="bF1Info bBlueButton">
    <img class="bF1Info_Logo" src="/images/f1info.png" alt="Улуги F1" />
    <script type="text/html" id="f1look">
      <div ref="<%=fid%>">
        <%=f1title%> - <%=f1price%>&nbsp;
        <span class="rubl"> p</span>
        <br>
        <a class="bBacketServ__eMore"
           href="<?php echo url_for('cart_service_delete', array('service' => 'F1ID', 'product' => $item->getId()));?>">Отменить услугу</a>
      </div>
    </script>
    <?php if (count($listInCart)) { ?>
      <h3>Вы добавили услуги:</h3>
      <?php foreach ($listInCart as $service): ?>
        <div ref="<?php echo $service->getToken();?>">
          <?php echo $service->getName() ?> - <?php echo formatPrice($service->getPrice()) ?>&nbsp;<span class="rubl">p</span><br>
          <a class="bBacketServ__eMore"
             href="<?php echo url_for('cart_service_delete', array('service' => $service->getId(), 'product' => $item->getId()));?>">Отменить услугу</a>
        </div>
      <?php endforeach ?>
    <?php } else { ?>
    <h3>Установка<br />и подключение</h3>
    <?php } ?>
    <a class="link1" href="">Выбрать услуги</a>
  </div>

<?php endif ?>

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
				<tr>
					<td class="bF1Block_eInfo">
						Два года гарантии<br>
						<a href="#">
							Подробнее об услуге
						</a>
					</td>
					<td class="bF1Block_eBuy">
						<span class="bF1Block_ePrice">
							1 950&nbsp;
							<span class="rubl">
								p
							</span>
						</span>
						<input class="button yellowbutton" data-ewid="1" data-ewtitle="Два года гарантии" data-ewprice="1950" type="button" value="Выбрать">
					</td>
				</tr>
				<tr>
					<td class="bF1Block_eInfo">
						Три года гарантии<br>
						<a href="#">
							Подробнее об услуге
						</a>
					</td>
					<td class="bF1Block_eBuy">
						<span class="bF1Block_ePrice">
							2 120&nbsp;
							<span class="rubl">
								p
							</span>
						</span>
						<input class="button yellowbutton" data-ewid="1" data-ewtitle="Три года гарантии" data-ewprice="2120" type="button" value="Выбрать">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<!-- END всплывающее окно с выбором доп.гарантии-->

<div class="bBlueButton extWarranty">
	<img alt="Улуги F1" class="bF1Info_Logo" src="/images/F1_logo_extWarranty.jpg">
	<h3>
		Дополнительная<br />гарантия
	</h3>
	<div id="ew_look" style="display:none;">
		<span class="ew_title"></span> - <span class="ew_price"></span>&nbsp;
		<span class="rubl"> p</span>
		<br>
		<a class="bBacketServ__eMore" href="<?php echo url_for('cart_service_delete', array('service' => 'EWID', 'product' => $item->getId()));?>">Отменить услугу</a>
	</div>
	<a class="link1" href="#">
		Выбрать гарантию
	</a>
</div>