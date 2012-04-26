<div class="clear"></div>
<input type="hidden" id="stockmodel"
	data-value="<?php echo $json ?>"
	link-output='<?php echo url_for('order_1click', array('product' => $product->barcode)) ?>'
	link-input='<?php echo url_for('product_delivery_1click') ?>'
	/>
	
	<div class='bInShop' id="noDlvr" style="display:none">
		<div class='bInShop__eTop'>
			<div class='bInShopError'>
				<h2>В Этом регионе выбранный товар не найден.<br>Отличный повод посетить другой город :)</h2>
				<div class='bInShop__eSelectorsWrap'>
					г. Москва<br>
					<a class='bGrayButton jsChangeRegion' href="">Другой город</a>
				</div>
				<div class='bInShop__eSelectorsLong'></div>
			</div>
		</div>		
	</div>
		
<div class="pb40">
	<div class='bOrderPreloader'>
		<span>Запрос данных...</span><img src='/images/bPreloader.gif'>
	</div>
	<div class='bInShop' id="stockBlock" style="display:none">
		<div class='bInShop__eTop'>
			<table cellpadding=0 cellspacing=0>
				<td class='bInShop__eItemInfo'>
					<div>
						<h3>Артикул #<span data-bind="text: shortcut"></span> 
							<span data-bind="text: priceTxt"></span> <span class="rubl">p</span></h3>
						Выберите магазин для резервирования товара, а мы подготовим его к вашему приезду
					</div>
				</td>
				<!-- ko if:!showMap() -->
				<td class='bInShop__eItemIcon'>
					<div class='bInShop__eLeftArrow'>
						<div class='bInShop__eRightArrow'>
							<img alt="" data-bind="attr: {src: icon}"/>
						</div>
					</div>
				</td>
				<!-- /ko -->
				<td class='bInShop__eSelectors' data-bind="css : { mLong: showMap() }"><!-- mLong optional -->
					<div class="bInShop__eSelectorsWrap" data-bind="if : showMap">
						Заберу:
						<br/>
						<div class="bSelectors">
							<a href="" data-bind="click: function() { toggleTerm(true) },
												  css : { mChecked: today(), mGreen: today()  } ">Сегодня</a>
							<a href="" data-bind="click: function() { toggleTerm(false) },
												  css : { mChecked: !today(), mBlue: !today()  } ">Завтра</a>
						</div>
					</div>
					<div class='bInShop__eSelectorsWrap'>
						Показать магазины:<br>
						<div class='bSelectors'>
							<a href="" data-bind="click: function() { toggleView(false) },
												  css : { mChecked: !showMap(), mOrange: !showMap()  } ">Список</a>
							<a href="" data-bind="click: function() { toggleView(true) },
												  css : { mChecked: showMap(), mOrange: showMap()  } ">На карте</a>
						</div>
					</div>
					<div class='bInShop__eSelectorsWrap'>
						г. Москва<br>
						<a class='bGrayButton jsChangeRegion' href="">Другой город</a>
					</div>
					<div class='bInShop__eSelectorsLong'></div>
				</td>
			</table>
		</div>
		<div class='bInShop__eBottom'>
			<!-- ko if: showMap() -->
			<table cellpadding="0" cellspacing="0">
				<tr><td>
					<div class="bMapShops__eMapWrap"></div>
				</td></tr>
			</table>
			<!-- /ko -->
			<!-- ko if: !showMap() -->
			<table cellpadding="0" cellspacing="0">
				<tr>
				<!-- ko if: todayShops.length > 0 -->
				<td>
					<h2 data-bind="html: todayH2"></h2>
					<div class='bInShop__eListWrap'>
						<!-- ko foreach : todayShops -->
						<label class='bInShop__eList' data-bind="click: $root.chooseShop, css: { mChecked : $root.selectedS() == $data }">
							<span class='bInShop__eDescription' data-bind="text: address"></span>
						</label><br/>
						<!-- /ko -->
					</div>
					<!-- ko if: selectedS().lbl == 'td' -->
					<a class='bBigOrangeButton' href="">Зарезервировать</a>
					<!-- /ko -->
				</td>
				<!-- /ko -->
				<!-- ko if: tomorrowShops.length > 0 -->
				<td>
					<h2 data-bind="html: tomorrowH2"></h2>
					<div class='bInShop__eListWrap'>
						<!-- ko foreach : tomorrowShops -->
						<label class='bInShop__eList' data-bind="click: $root.chooseShop, css: { mChecked : $root.selectedS() == $data }">
							<span class='bInShop__eDescription' data-bind="text: address"></span>
						</label><br/>
						<!-- /ko -->
					</div>
					<!-- ko if: selectedS().lbl == 'tmr' -->
					<a class='bBigOrangeButton' href="">Зарезервировать</a>
					<!-- /ko -->
				</td>
				<!-- /ko -->
				</tr>
			</table>
			<!-- /ko -->
			
		</div>
	</div>
	<!--div class='bInShop__eNoTime'>Некогда приезжать? <a href>Купите этот товар с доставкой</a></div-->   
</div>
<div class="clear"></div>