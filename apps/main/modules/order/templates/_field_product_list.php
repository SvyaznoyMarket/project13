<div class='bOrderPreloader' data-bind="style: { display: appIsLoaded() ? 'none' : 'block' }">
	<span>Ваш заказ формируется...</span><img src='/images/bPreloader.gif'>
</div>

<div data-bind="style: { display: noSuchItemError() ? 'block' : 'none' }" class="hf">
	<div class='bMobDownWrapAbs customalign'>
		<div class='bMobDownWrapRel'>
			<div class='bMobDown mBR5 mW2 mW750'>
				<div class='bMobDown__eWrap'>
					<img class='fr pt20 mr20' src='/images/error_ajax.gif'/>
					<h2 class="pb30">Кто-то был быстрее вас.<br/>
					Некоторых товаров уже нет в наличии:</h2>
					<!-- ko foreach: stolenItems -->
					<div class='bFormSave'>
						<span data-bind="html: title"></span>
						<h2><span data-bind="html: price"></span> <span class='rubl'>p</span></h2>
					</div>
					<!-- /ko -->
					<a class='bOrangeButton mr20' id="tocontinue" href>Оформить заказ без этих товаров</a>
					<a class='bOrangeButton' data-bind="attr: { href: urlaftererror }">Подобрать похожий товар</a>
				</div>
			</div>
		</div>
	</div>
	<div class="graying"></div>
</div>

<div data-bind="style: { display: RequestError() ? 'block' : 'none' }" class="hf">
	<div class='bMobDownWrapAbs customalign'>
		<div class='bMobDownWrapRel'>
			<div class='bMobDown mBR5 mW2 mW750'>
				<div class='bMobDown__eWrap'>
					<img class='fr pt20 mr20' src='/images/error_ajax.gif'/>
					<h2 class="pb30" data-bind="html: errorText"></h2>
				</div>
			</div>
		</div>
	</div>
	<div class="graying"></div>
</div>

<div data-bind="style: { display: appIsLoaded() ? 'block' : 'none' }" class="hf">
<div class='bBuyingInfo'>
<span>Отличный выбор! Для вашего удобства мы сформировали несколько заказов в зависимости от типа доставки:</span>
</div>

<div data-bind="if: bitems().length > 0">
	<div class='bBuyingLineWrap rapid'>
		
		<dl class='bBuyingLine' data-bind="with: RapidCalend"> 
			<dt><h2>Доставим <span data-bind="text: curDate"></span><br>
				<span data-bind="text: curTime"></span></h2><i>Стоимость доставки 
				<span data-bind="text: $root.addCost"></span> <span class="rubl">p</span></i>
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
		
		<dl class='bBuyingLine' data-bind="foreach: bitems">
			<dt></dt>
			<dd>
				<div>
					<p><span data-bind="text: price"></span> <span class="rubl">p</span></p>
					<p>
						<a class='bImgButton mBacket' href data-bind="click: $parent.removeIt.bind($data, 'rapid' )"></a>
						<!-- ko if: ((dlvr.length > 1 ) || (dlvr[0].lbl() !== 'rapid') && (dlvr.length === 1)) -->
						<a class='bImgButton mArrows' href></a>
						<span class="bButtonPopup" style="left: 203px; display:none">
							<span class="bButtonPopup__eTitle">Переместить товар:</span>
							<!-- ko foreach: dlvr -->
							<!-- ko if: lbl() !== 'rapid' -->
							<a class="bButtonPopup__eLine moveline" 
							data-bind="text: txt, click: $root.shifting.bind($data, $parent, 'rapid' )"></a>
							<!-- /ko -->
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
		
		<div class='bBuyingLineWrap__eSum'>Итого с доставкой: <b>
		<span data-bind="text: totalPrice"></span> <span class="rubl">p</span></b></div>
	</div>
</div>

<div data-bind="if: bitems_D().length > 0">
	<div class='bBuyingLineWrap delay'>
		
		<dl class='bBuyingLine' data-bind="with: DelayCalend">
			<dt><h2>Доставим <span data-bind="text: curDate"></span><br>
				<span data-bind="text: curTime"></span></h2><i>Стоимость доставки 
				<span data-bind="text: $root.addCost_D"></span> <span class="rubl">p</span></i>
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
		
		<dl class='bBuyingLine' data-bind="foreach: bitems_D">
			<dt></dt>
			<dd>
				<div>
					<p><span data-bind="text: price"></span> <span class="rubl">p</span></p>
					<p>
						<a class='bImgButton mBacket' href data-bind="click: $parent.removeIt"></a>
						<!-- ko if: ((dlvr.length > 1 ) || (dlvr[0].lbl() !== 'delay') && (dlvr.length === 1)) -->
						<a class='bImgButton mArrows' href></a>
						<span class="bButtonPopup" style="left: 203px; display:none">
							<span class="bButtonPopup__eTitle">Переместить товар:</span>
							<!-- ko foreach: dlvr -->
							<!-- ko if: dlvr[0].lbl() !== 'delay' -->
							<a class="bButtonPopup__eLine moveline" 
							data-bind="text: txt, click: $root.shifting.bind($data, $parent, 'delay' )"></a>
							<!-- /ko -->
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
</div>

<div data-bind="if: $root.totalPrice_S() != '0'">
	<div class='bBuyingLineWrap selfy'>
		
		<dl class='bBuyingLine' data-bind="with: SelfyCalend">
			<dt><h2>Самовывоз <span data-bind="text: curDate"></span></h2><i>Бесплатно</i></dt>
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
						<a class='bImgButton mBacket' href data-bind="click: $root.removeIt.bind($data, 'selfy' )"></a>
						<!-- ko if: ((dlvr.length > 1 ) || (dlvr[0].lbl !== 'selfy') && (dlvr.length === 1)) -->
						<a class='bImgButton mArrows' href></a>
						<span class="bButtonPopup" style="left: 203px; display:none">
							<span class="bButtonPopup__eTitle">Переместить товар:</span>
							<!-- ko foreach: dlvr -->
							<!-- ko if: lbl() !== 'selfy' -->
							<a class="bButtonPopup__eLine moveline" 
							data-bind="text: txt, click: $root.shifting.bind($data, $parent, 'selfy' )"></a>
							<!-- /ko -->
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
</div>

	<dl class='bBuyingLine mSumm'>
		<dt><a href alt="Вернуться в корзину для выбора услуг и увеличения количества товаров" title="Вернуться в корзину для выбора услуг и увеличения количества товаров">Редактировать товары</a></dt>
		<dd>
			<div>Сумма всех заказов: <h3><span data-bind="text: $root.totalSum()"></span> <span class="rubl">p</span></h3></div>
		</dd>
	</dl>

	<div id="map-info_window-container" style="display:none">
		<div class='bMapShops__ePopupRel'>
			<h3 data-name="name"></h3>
			<span data-name="regtime"></span><br>
			<span class="shopnum" style="display:none" data-name="id"></span>
			<a href class='bGrayButton shopchoose'>Забрать из этого магазина</a>
		</div>
	</div>

	<div id="orderMapPopup" class='popup'>
		<i class='close'></i>
		
		<div class='bMapShops__eMapWrap' id="mapPopup" ></div>
		
		<div class='bMapShops__eList' data-bind="with: productforPopup()">
			<div class='bMapShops__eListTitle'>
				<img data-bind="attr: { src: img }" alt=""/>
				<b data-bind="text: title"></b>
				<br/>(<span data-bind="text: price"></span> <span class="rubl">p</span>)
			</div>
			<h3>Выберите магазин Enter для самовывоза</h3>
			<ul data-bind="foreach: $root.popupWithShops">
				<li data-bind="click: $root.shiftAndClose">
					<div class='bMapShops__eListNum'><img data-bind="attr: { src: markerImg }" alt=""/></div>
					<div data-bind="text: title"></div>
					<span>Работаем </span><span data-bind="text: fromto"></span>
				</li>
			</ul>
		</div>
		
	</div>

</div>	