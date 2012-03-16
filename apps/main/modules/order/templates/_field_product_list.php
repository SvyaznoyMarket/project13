 <!-- IVN: MVVM -->
	<div class='bBuyingLineWrap rapid' data-bind="if: bitems().length > 0">
		
		<dl class='bBuyingLine' data-bind="with: RapidCalend"> 
			<dt><h2>Доставим <span data-bind="text: curDate"></span><br>
				<span data-bind="text: curTime"></span></h2><i>Стоимость доставки 
				<span data-bind="text: $root.addCost"></span> <span class="rubl">p</span><i>
			</dt>
			<dd>
				<div>
					<p></p>
					<ul class='bBuyingDates'>
						<li class='weektoggle bBuyingDates__eLeft' data-bind="click: cWeek, css: { mDisabled: ! weeknum() }"><b></b><span></span></li>
						<!-- ko foreach: dates -->
						<li class="jsdate" data-bind="html: dhtml,
							click: $parent.pickDate,
							css: { bBuyingDates__eEnable: state == 'act', bBuyingDates__eDisable: state == 'dis',
							bBuyingDates__eCurrent: dv == $parent.curDate() , erased:  sw != $parent.weeknum() }"></li>
						<!-- /ko -->
						<li style="display:none" class='bBuyingDates__eEnable'>13 <span>Чт</span></li> 
						<li class='weektoggle bBuyingDates__eRight' data-bind="click: cWeek, css: { mDisabled: weeknum() }"><b></b><span></span></li>
					</ul>
					<!-- ko foreach: dates -->
					<span class="bBuyingDatePopup" data-bind="attr: {ref: dv}" style="top: 2px; display:none">
						<h3 class="bBuyingDatePopup__eTitle" data-bind="text: $parent.curDate"></h3>
						<!-- ko foreach: schedule -->
						<span class="bBuyingDatePopup__eLine">
							<i data-bind="css: { bBuyingDatePopup__eOK: $parents[1].curTime() == txt }"></i>
							<span data-bind="text: txt, click: $parents[1].pickTime">c 9:00 до 14:00</span>
						</span>
						<!-- /ko -->
					</span>
					<!-- /ko -->
				</div>
			</dd>
		</dl>
		
		<dl class='bBuyingLine' data-bind="foreach: bitems"> <!-- IVN: MVVM -->
			<dt></dt>
			<dd>
				<div>
					<p><span data-bind="text: price"></span> <span class="rubl">p</span></p>
					<p>
						<a class='bImgButton mBacket' href data-bind="click: $parent.removeIt"></a>
						<!-- ko if: moveable -->
						<a class='bImgButton mArrows' href></a>
						<span class="bButtonPopup" style="left: 203px; display:none">
							<span class="bButtonPopup__eTitle">Переместить товар:</span>
							<!-- ko foreach: dlvr -->
							<a class="bButtonPopup__eLine moveline" 
							data-bind="text: txt, click: $root.shifting.bind($data, $parent, 'rapid' )"></a>
							<!-- /ko -->
						</span>
						<!-- /ko -->
					</p>
					<img data-bind="attr: { src: img}"> 
					<span class='bBuyingLine__eInfo'>
						<div data-bind="html: title"></div>
						<span>(</span><span data-bind="html: hm"></span><span> шт.)</span>
					</span>
				</div>
			</dd>
		</dl>
		
		<div class='bBuyingLineWrap__eSum'>Итого с доставкой: <b><span data-bind="text: totalPrice"></span> <span class="rubl">p</span></b></div>
	</div>
	
	<div class='bBuyingLineWrap delay' data-bind="if: bitems_D().length > 0">
		
		<dl class='bBuyingLine' data-bind="with: DelayCalend"> <!-- IVN: MVVM -->
			<dt><h2>Доставим <span data-bind="text: curDate"></span><br>
				<span data-bind="text: curTime"></span></h2><i>Стоимость доставки 
				<span data-bind="text: $root.addCost_D"></span> <span class="rubl">p</span><i>
			</dt>
			<dd>
				<div>
					<p></p>
					<ul class='bBuyingDates'>
						<li class='weektoggle bBuyingDates__eLeft' data-bind="click: cWeek, css: { mDisabled: !weeknum() }"><b></b><span></span></li>
						<!-- ko foreach: dates -->
						<li class="jsdate" data-bind="html: dhtml,
							click: $parent.pickDate,
							css: { bBuyingDates__eEnable: state == 'act', bBuyingDates__eDisable: state == 'dis',
							bBuyingDates__eCurrent: dv == $parent.curDate(), erased:  sw != $parent.weeknum() }"></li>
						<!-- /ko -->
						<li style="display:none" class='bBuyingDates__eEnable'>13 <span>Чт</span></li> 
						<li class='weektoggle bBuyingDates__eRight' data-bind="click: cWeek, css: { mDisabled: weeknum() }"><b></b><span></span></li>
					</ul>
					<span class="bBuyingDatePopup" style="top: 2px; display:none">
						<h3 class="bBuyingDatePopup__eTitle" data-bind="text: curDate"></h3>
						<!-- ko foreach: schedule -->
						<span class="bBuyingDatePopup__eLine">
							<i data-bind="css: { bBuyingDatePopup__eOK: $parent.curTime() == $data }"></i>
							<span data-bind="text: $data, click: $parent.pickTime">c 9:00 до 14:00</span>
						</span>
						<!-- /ko -->
					</span>
				</div>
			</dd>
		</dl>
		
		<dl class='bBuyingLine' data-bind="foreach: bitems_D"> <!-- IVN: MVVM -->
			<dt></dt>
			<dd>
				<div>
					<p><span data-bind="text: price"></span> <span class="rubl">p</span></p>
					<p>
						<a class='bImgButton mBacket' href data-bind="click: $parent.removeIt"></a>
						<!-- ko if: moveable -->
						<a class='bImgButton mArrows' href></a>
						<span class="bButtonPopup" style="left: 203px; display:none">
							<span class="bButtonPopup__eTitle">Переместить товар:</span>
							<!-- ko foreach: dlvr -->
							<a class="bButtonPopup__eLine moveline" 
							data-bind="text: txt, click: $root.shifting.bind($data, $parent, 'delay' )"></a>
							<!-- /ko -->
						</span>
						<!-- /ko -->
					</p>
					<img data-bind="attr: { src: img}"> 
					<span class='bBuyingLine__eInfo'>
						<div data-bind="html: title"></div>
						<span>(</span><span data-bind="html: hm"></span><span> шт.)</span>
					</span>
				</div>
			</dd>
		</dl>
		
		<div class='bBuyingLineWrap__eSum'>Итого с доставкой: <b><span data-bind="text: totalPrice_D"></span> <span class="rubl">p</span></b></div>
	</div>

	<div class='bBuyingLineWrap selfy' data-bind="if: shops().length > 0">
		
		<dl class='bBuyingLine' data-bind="with: SelfyCalend">
			<dt><h2>Самовывоз <span data-bind="text: curDate"></span></h2><i>Бесплатно<i></dt>
			<dd>
				<div>
					<p></p>
					<ul class='bBuyingDates'>
						<li class='weektoggle bBuyingDates__eLeft' data-bind="click: cWeek, css: { mDisabled: !weeknum() }"><b></b><span></span></li>
						<!-- ko foreach: dates -->
						<li class="jsdate" data-bind="html: dhtml,
							click: $parent.pickDate,
							css: { bBuyingDates__eEnable: state == 'act', bBuyingDates__eDisable: state == 'dis',
							bBuyingDates__eCurrent: dv == $parent.curDate(), erased:  sw != $parent.weeknum() }"></li>
						<!-- /ko -->
						<li style="display:none" class='bBuyingDates__eEnable'>13 <span>Чт</span></li> 
						<li class='weektoggle bBuyingDates__eRight' data-bind="click: cWeek, css: { mDisabled: weeknum() }"><b></b><span></span></li>
					</ul>
				</div>
			</dd>
		</dl>
		<!-- ko foreach: shops -->
		<!-- ko if: products().length > 0 -->
		<dl class='bBuyingLine'>
			<dt data-bind="text: title"></dt>
			<dd>
				<!-- ko foreach: products -->				
				<div>
					<p><span data-bind="text: price"></span> <span class="rubl">p</span></p>
					<p>
						<a class='bImgButton mBacket' href data-bind="click: $root.removeFromShop.bind($data, $parent )"></a>
						<!-- ko if: moveable -->
						<a class='bImgButton mArrows' href></a>
						<span class="bButtonPopup" style="left: 203px; display:none">
							<span class="bButtonPopup__eTitle">Переместить товар:</span>
							<!-- ko foreach: dlvr -->
							<a class="bButtonPopup__eLine moveline" 
							data-bind="text: txt, click: $root.shifting.bind($data, $parent, 'selfy' )"></a>
							<!-- /ko -->
						</span>
						<!-- /ko -->
						<!-- ko if: locs.length > 1 -->
						<a class='bImgButton mMap' href data-bind="click: $root.fillPopupWithShops.bind($data, $parent )"></a>
						<!-- /ko -->
					</p>
					<img data-bind="attr: { src: img}"> 
					<span class='bBuyingLine__eInfo'>
						<div data-bind="html: title"></div>
						<span>(</span><span data-bind="html: hm"></span><span> шт.)</span>
					</span>
				</div>
				<!-- /ko -->				
				<div class='bBracket'>
					<div class='bBracket__eBg'>
						<div class='bBracket__eBottom'>
							<div class='bBracket__eTop'></div>
							<div class='bBracket__eCurrent'></div>
						</div>
					</div>
				</div>
				
			</dd>
		</dl>
		<!-- /ko -->
		<!-- /ko -->
		<div class='bBuyingLineWrap__eSum'>Итого: <b><span data-bind="text: totalPrice_S"></span> <span class="rubl">p</span></b></div>
	</div>
	
	<dl class='bBuyingLine mSumm'>
		<dt><a href alt="Вернуться в корзину для выбора услуг и увеличения количества товаров" title="Вернуться в корзину для выбора услуг и увеличения количества товаров">Редактировать товары</a></dt>
		<dd>
			<div>Сумма всех заказов: <h3><span data-bind="text: $root.totalSum()"></span> <span class="rubl">p</span></h3></div>
		</dd>
	</dl>
 <!-- IVN: MVVM -->
 
<!-- IVN map -->
	<input id="map-center" type="hidden" data-content='{"latitude":"55.755798","longitude":"37.617636"}'>

	<div id="map-info_window-container" style="display:none">
		<div class='bMapShops__ePopupRel'>
			<h3 data-name="name"></h3>
			<span data-name="regtime"></span><br>
			<span class="shopnum" style="display:none" data-name="id"></span>
			<a href class='bGrayButton shopchoose'>Забрать из этого магазина</a>
		</div>
	</div>
<!-- /IVN map -->

<!-- IVN popup -->
	<div class='bMobDownWrapAbs mMapPopup' style="display:none">
		<div class='bMobDownWrapRel'>

			<div class='bMobDown mBR5 mW2'>
				<div class='bMobDown__eWrap'>
					<div class='bMobDown__eClose close'></div>
					
					<!-- map api -->
					<div class='bMapShops__eMapWrap' id="mapPopup" >
					</div>
					<!-- /map api -->
					
					<div class='bMapShops__eList' data-bind="with: productforPopup()">
						<div class='bMapShops__eListTitle'>
							<img data-bind="attr: { src: img }" alt=""/>
							<b data-bind="text: title"></b>
							<br/>(<span data-bind="text: price"></span> <span class="rubl">p</span>)
						</div>
						<h3>Выберите магазин Enter для самовывоза</h3>
						<ul data-bind="foreach: $root.popupWithShops">
							<li data-bind="click: $root.shiftingInShops">
								<div class='bMapShops__eListNum'><img data-bind="attr: { src: markerImg }" alt=""/></div>
								<div data-bind="text: title"></div>
								<span>Работаем </span><span data-bind="text: fromto"></span>
							</li>
						</ul>
					</div>
					
				</div>
			</div>

		</div>
	</div>
<!-- /IVN popup -->