<?php
/**
 * @var $page           \View\Order\CreatePage
 * @var $user           \Session\User
 * @var $form           \View\Order\NewForm\Form
 * @var $deliveryData   array
 * @var $productsById   \Model\Product\Entity[]
 * @var $paymentMethods \Model\PaymentMethod\Entity[]
 * @var $paymentGroups \Model\PaymentMethod\Group\Entity[]
 * @var $subways        \Model\Subway\Entity[]
 * @var $banks          \Model\CreditBank\Entity[]
 * @var $creditData     array
 * @var $selectCredit   bool
 * @var $bonusCards   \Model\Order\BonusCard\Entity[]
 * @var $bonusCardsData array
 */
?>

<?
$helper = new \Helper\TemplateHelper();
$request = \App::request();

$paypalECS = isset($paypalECS) && (true === $paypalECS);
$lifeGift = isset($lifeGift) && (true === $lifeGift);
$oneClick = isset($oneClick) && (true === $oneClick);
$region = $user->getRegion();
$isCorporative = $user->getEntity() && $user->getEntity()->getIsCorporative();

$backLink = $page->url('cart');
foreach (array_reverse($productsById) as $product) {
	/** @var $product \Model\Product\Entity */
	if ($product->getParentCategory() instanceof \Model\Product\Category\Entity) {
		$backLink = $product->getParentCategory()->getLink();
		break;
	}
}
if ($oneClick && strpos($request->headers->get('referer'), '/product/') > 0) $backLink = $product->getLink();

if ($oneClick) {
    $createUrl = $page->url('order.oneClick.create');
    $deliveryUrl = $page->url('order.delivery', ['oneClick' => 1]);
} else if ($paypalECS) {
    $createUrl = $page->url('order.paypal.create', ['token' => $request->get('token'), 'PayerID' => $request->get('PayerID')]);
    $deliveryUrl = $page->url('order.delivery', ['paypalECS' => 1]);
} else if ($lifeGift) {
    $createUrl = $page->url('order.lifeGift.create');
    $deliveryUrl = $page->url('order.delivery', ['lifeGift' => 1]);
} else {
    $createUrl = $page->url('order.create');
    $deliveryUrl = $page->url('order.delivery');
}

$onlyPartnersProducts = true;
foreach ($productsById as $product) {
    if (count($product->getPartnersOffer()) == 0) $onlyPartnersProducts = false;
}
?>

<!-- Header -->
<div class="bBuyingHead clearfix">
	<a class="bBuyingHead__eLogo" href="<?= $page->url('homepage') ?>"></a>
	
	<div class="bBuyingHead__eTitle">
		<span class="bSubTitle">Оформление заказа</span><br/>
		<span class="bTitle">Финальный шаг :)</span>
	</div>
</div>
<!-- /Header -->

<!-- loader -->
<div class="mLoader" data-bind="visible: !prepareData()"></div>
<!-- /loader -->

<? if (\App::config()->adFox['enabled']): ?>
	<div id="adfox920" class="adfoxWrapper"></div>
<? endif ?>

<!-- Общая обертка оформления заказа -->
<div class="bBuyingSteps clearfix" style="display:none" data-bind="style: { display: prepareData() ? 'block' : 'none'}, css: { mLifeGift: $root.lifeGift }">

	<div class="bBuyingLine"><a class="bBackCart" href="<?= $backLink ?>">&lt; Вернуться к покупкам</a></div>

    <? if ($lifeGift): ?>
        <div class="bLifeGiftTitle">
            <span class="bLifeGiftTitle__eText">Обратите внимание</span>

            <div class="bLifeGiftTitle__eImg">
                Вы оформляете заказ на подарок тяжелобольным детям,<br/> которых опекает фонд "Подари жизнь".

                <span class="bViolet">Оплатите заказ онлайн, и Enter доставит новогодний подарок<br/> прямо в больницу.</span>
            </div>
        </div>
    <? endif ?>

	<!-- Order Method -->
	<div class="bBuyingLine clearfix mOrderMethod" data-bind="visible: deliveryTypes().length > 1">
		<h2 class="bBuyingSteps__eTitle">Информация о заказе</h2>

		<div class="bBuyingLine__eLeft">Выберите предпочтительный способ</div>

		<div class="bBuyingLine__eRight bInputList" data-bind="foreach: { data: deliveryTypes }">
			<input class="jsCustomRadio bCustomInput mCustomCheckBig" type="radio" name="radio" data-bind="attr: { 'id': 'method_'+$data.id }" hidden="hidden" />
			<label class="bCustomLabel mCustomLabelBig mLabelStrong jsInitMap" data-bind="
									text: $data.name,
									states: $data.states,
									click: $root.chooseDeliveryTypes,
									attr: { 'for': 'method_'+$data.id }">
			</label>
			<p class="bBuyingLine__eDesc" data-bind="html: $data.description"></p>
		</div>
	</div>
	<!-- Order Method -->

	<!-- Delivery boxes -->
	<div data-bind="foreach: { data: deliveryBoxes, as: 'box' }">
		<div class="bBuyingLineWrap clearfix">
			<div class="bBuyingLine clearfix">
				<div class="bBuyingLine__eLeft">
					<h2 class="bBuyingSteps__eTitle">
						<span data-bind="text: box.deliveryName"></span>
					</h2>

					<div class="bDeliverySelf">
						<span data-bind="visible: box.hasPointDelivery, html: box.choosenPoint().name"></span>
						<div class="bTimeworkPoint" data-bind="visible: box.hasPointDelivery, html: 'режим работы: <br/> <strong>ежедневно ' + box.choosenPoint().regtime + '</strong>'"></div>
					</div>

					<!-- кнопка сменить магазин -->
					<a class="bBigOrangeButton mSelectShop" href="#" data-bind="visible: box.hasPointDelivery && box.pointList.length > 1,
												text: box.changePointButtonText,
												click: box.changePoint">
					</a>
					<!-- /кнопка сменить магазин -->
				</div>

				<div class="bBuyingLine__eRight">
					<!-- Celendar -->
					<div class="bBuyingDates clearfix" data-bind="visible: !$root.lifeGift()">
						<div class="bBuyingDatesItem mLeft" data-bind="click: box.calendarLeftBtn">
							<span class="bArrow"></span>
						</div>

						<div class="bBuyingDatesWrap">
							<ul class="bBuyingDatesList" data-bind="foreach: { data: allDatesForBlock, as: 'calendarDay' }, calendarSlider: box.calendarSliderLeft()">
								<li class="bBuyingDatesItem mDisable" data-bind="css: { mCurrent: calendarDay.value == box.choosenDate().value,
																				mDisable: !calendarDay.avalible,
																				mEnable: calendarDay.avalible },
																				click: function(data){
																					box.clickCalendarDay(data);
																				}">
									<span class="bBuyingDatesItem__eDayNumber" data-bind="text: ( calendarDay.day === 0 ) ? '' : calendarDay.day"></span> 
									<span class="bBuyingDatesItem__eDay" data-bind="text: calendarDay.humanDayOfWeek"></span>
								</li>
							</ul>
						</div>

						<div class="bBuyingDatesItem mRight" data-bind="click: box.calendarRightBtn">
							<span class="bArrow"></span>
						</div>
					</div>
					<!-- /Celendar -->


					<div class="bDeliveryDate" data-bind="visible: !$root.lifeGift() && !box.hasPointDelivery">
						<strong data-bind="text:box.choosenDate().name"></strong>, <span data-bind="text: box.choosenNameOfWeek"></span>
					</div>

					<div class="bDeliveryDate" style="width: 462px;" data-bind="visible: !$root.lifeGift() && box.hasPointDelivery">
						<strong data-bind="text:box.choosenDate().name"></strong>, <span data-bind="text: box.choosenNameOfWeek"></span>
						<span class="bSmsAtantion" data-bind="visible: box.hasPointDelivery, text: 'ожидайте смс о готовности заказа'"></span>
					</div>

					<div class="bSelectWrap mFastInpSmall" data-bind="if: box.choosenDate().intervals.length, visible: !box.hasPointDelivery && box.choosenDate().intervals.length && !$root.lifeGift()">
						<span class="bSelectWrap_eText" data-bind="text: (!box.hasPointDelivery ? 'c ' + box.choosenInterval().start + ' ' : '') + 'до ' + box.choosenInterval().end"></span>
						<select class="bSelect" data-bind="options: box.choosenDate().intervals,
															value: box.choosenInterval,
															optionsText: function(item) {
																return (!box.hasPointDelivery ? 'c ' + item.start + ' ' : '') + 'до ' + item.end;
															}">
						</select>
					</div>

					<!-- Products -->
					<!-- ko foreach: { data: products, as: 'product' } -->
					<div class="bBuyingLine mProductsLine clearfix"<? if (\App::config()->order['prepayment']['enabled']): ?> data-bind="css: product.isPrepayment ? 'mSelectedPay' : ''"<? endif ?>>
						<div class="bBuyingLine__eRight">
							<div class="bOrderItems">
								<div class="bItemsRow mItemImg">
									<img class="bItemsRow__eImgProd" data-bind="attr: { src: product.productImg }" />
								</div>

								<div class="bItemsRow mItemInfo">
									<a target="_blank" data-bind="text: product.name, attr: { href: product.productUrl }"></a>
								</div>

								<div class="bItemsRow mItemCount">
                                    <div class="bCountSection clearfix" data-spinner-for="000" data-bind="attr: { 'data-seturl': product.setUrl }">
                                        <button class="bCountSection__eM">-</button>
                                        <input class="bCountSection__eNum" type="text" value="" data-bind="attr: { value: product.quantity }" />
                                        <button class="bCountSection__eP">+</button>
                                        <span>шт.</span>
                                    </div><!--/counter -->
								    <? /* <span data-bind="text: product.quantity"></span> шт. */?>
								</div>

								<div class="bItemsRow mDelItem">
									<a class="bDelItem" data-bind="attr: { href: product.deleteUrl }, text: 'удалить', click: $root.deleteItem"></a>
								</div>

								<div class="bItemsRow mItemRight"> <span data-bind="html: window.printPrice(product.price)"></span>&nbsp;<span class="rubl">p</span></div>
							</div>
						</div>
					</div>
					<!-- /ko -->
					<!-- /Products -->
				</div>
			</div>

			<? /*<div class="bFootnote" data-bind="visible: !hasPointDelivery">* Дату доставки уточнит специалист Контакт-сENTER</div> */?>

			<!-- Sum -->
			<ul class="bSumOrderInfo">
				<li class="bSumOrderInfo__eLine">
					<span data-bind="text: hasPointDelivery ? 'Самовывоз:&nbsp;&nbsp;': 'Доставка:&nbsp;&nbsp;' "></span>
					
					<span class="bDelivery">
						<span data-bind="text: box.deliveryPrice === 0 ? 'Бесплатно' : box.deliveryPrice"></span>&nbsp;<span class="rubl" data-bind="visible: box.deliveryPrice">p</span>
					</span>
				</li>

				<li class="bSumOrderInfo__eLine">
					Итого с доставкой:&nbsp;&nbsp;

					<span class="bDelivery"<? if (\App::config()->order['prepayment']['enabled']): ?> data-bind="css: { 'mSelect' : box.isExpensiveOrder() }"<? endif ?>>
						<span data-bind="html: window.printPrice( box.totalBlockSum )"></span>&nbsp;<span class="rubl">p</span>
					</span>
				</li>
			</ul>
			<!-- /Sum -->

            <? if (\App::config()->order['prepayment']['enabled']): ?>
                <!-- Prepayment -->
                <div class="bFootnote" data-bind="visible: box.hasProductWithPrepayment || box.isExpensiveOrder">
                    <strong>Внесите предоплату.</strong>&nbsp;&nbsp;
                    <span data-bind="visible: box.hasProductWithPrepayment">В корзине товар, <a style="text-decoration: underline;" href="/how_pay#prePayLink">требующий предоплаты</a>&nbsp;&nbsp;</span>
                    <span data-bind="visible: !box.hasProductWithPrepayment || box.isExpensiveOrder">Сумма заказа превышает <?= $helper->formatPrice(\App::config()->order['prepayment']['priceLimit']) ?> <span class="rubl">p</span>&nbsp;&nbsp;<a style="text-decoration: underline;" target="_blank" href="/how_pay#prePayLink">Подробнее</a></span>
                </div>
                <!-- /Prepayment -->
            <? endif ?>
		</div>
	</div>
	<!-- /Delivery boxes --> 
	
	<? if (!$oneClick && !$onlyPartnersProducts): ?>
	    <? if (\App::config()->coupon['enabled'] || \App::config()->blackcard['enabled']): ?>
		<!-- Sale section -->
		<div class="bBuyingLineWrap bBuyingSale clearfix" data-bind="visible: deliveryBoxes().length == 1 && !/svyaznoy/.test(deliveryBoxes()[0].state) && !$root.lifeGift(), ">
			<div class="bBuyingLine clearfix">
				<div class="bBuyingLine__eLeft">
					<h2 class="bBuyingSteps__eTitle">
						Скидки
					</h2>

	                <? if (\App::config()->blackcard['enabled']): ?> карта Enter SPA <? endif ?>
	                <? if (\App::config()->coupon['enabled'] && \App::config()->blackcard['enabled']): ?> или<? endif ?>
	                <? if (\App::config()->coupon['enabled']): ?> Код фишки, купон, промокод <? endif ?>
				</div>

				<div class="bBuyingLine__eRight">
					<div class="bSaleData" data-bind="couponsVisible: couponsBox()">
						
						<div class="bSaleData__eEmptyBlock">Скидок больше нет</div>

						<ul class="bSaleList bInputList clearfix hiddenCustom">
	                        <? if (\App::config()->coupon['enabled']): ?>
							<li class="bSaleList__eItem" data-type="coupon" data-bind="visible: (deliveryBoxes().length == 1)">
								<input value="<?= $page->url('cart.coupon.apply') ?>" class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="svz_club" name="add_sale" data-bind="checked: couponUrl" />
								<label class="bCustomLabel mCustomLabelRadioBig" for="svz_club">Купон</label>
							</li>
	                        <? endif ?>

	                        <? if (\App::config()->blackcard['enabled']): ?>
							<li class="bSaleList__eItem mEnterSpa" data-type="blackcard">
								<input value="<?= $page->url('cart.blackcard.apply') ?>" class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="black_card" name="add_sale" data-bind="checked: couponUrl" />
								<label class="bCustomLabel mCustomLabelRadioBig" for="black_card">Enter Spa</label>
							</li>
	                        <? endif ?>
						</ul>

						<input class="bBuyingLine__eText mSaleInput" type="text" id="coupon_number" data-bind="value: couponNumber, valueUpdate: 'afterkeydown', disable: couponsBox().length " />

						<button class="bBigOrangeButton mSaleBtn" data-bind="click: checkCoupon, disable: couponsBox().length">Применить</button>

						<p class="bSaleError" data-bind="text: couponError"></p>
					</div>

					<div class="bSalePreview">
						<img class="bSalePreview_img" src="/css/bBuyingSteps/img/bCheck.jpg" />
						<img class="bSalePreview_img bSalePreview_img-eprize" src="/css/bBuyingSteps/img/fishka.jpg" />
					</div>

					 <!-- Coupons -->
					<div class="bBuyingLine mCouponsLine" data-bind="foreach: { data: couponsBox(), as: 'coupon' }">
						<div class="bOrderItems">
							<div class="bItemsRow mItemImg" data-bind="css: { mError: coupon.error }"></div>

							<div class="bItemsRow mItemInfo" data-bind="text: (coupon.error && coupon.error.message) || coupon.name"></div>

<!--							<div class="bItemsRow mCountItem"></div>-->

							<div class="bItemsRow mDelItem">
								<a class="bDelItem" data-bind="attr: { 'href': coupon.deleteUrl }, click: $root.deleteItem">удалить</a>
							</div>

							<div class="bItemsRow mItemRight" data-bind="visible: !coupon.error"><span data-bind="text: coupon.sum"></span> <span class="rubl">p</span></div>
						</div>
					</div>
					<!-- /Coupons -->
				</div>
			</div>
		</div>
		<!-- /Sale section -->
        <? elseif (!$lifeGift && !$oneClick): ?>
	        Купоны на сайте временно не принимаются. Вы можете использовать их в любом из наших магазинов или обратиться в контакт-центр. Приносим свои извинения
	    <? endif ?>
	<? endif ?>

	<? if (!$oneClick): ?>
		<div class="bBuyingLine mSumm clearfix" data-bind="visible: deliveryBoxes().length">
			<a href="<?= $page->url('cart') ?>" class="bBackCart mOrdeRead">&lt; Редактировать товары</a>

			<div class="bTotalSumm">
				Сумма всех заказов:
				<span class="bTotalSumm__ePrice" data-bind="html: window.printPrice( totalSum() )"></span>&nbsp;<span class="rubl">p</span>
			</div>
		</div>
	<? endif ?>

	<!-- Форма заказа -->
	<div class="bBuyingInfo" data-bind="visible: deliveryBoxes().length">
		<h2 class="bBuyingSteps__eTitle" data-bind="visible: !$root.lifeGift()">Информация о счастливом получателе</h2>

		<p class="bFeildStar"><span class="bFeildStarImg">*</span> Обязательные поля</p>

		<div class="bHeadnote" data-bind="visible: !$root.lifeGift()">
            <? if ($user->getEntity()): ?>
                Привет, <a href="<?= $page->url(\App::config()->user['defaultRoute']) ?>"><strong><?= $user->getEntity()->getName() ?></strong></a>
            <? else: ?>
                Уже покупали у нас?
                <strong><a id="auth-link" class="underline bAuthLink" href="<?= $page->url('user.login') ?>">Авторизуйтесь</a></strong>
                и вы сможете использовать ранее введенные данные
            <? endif ?>
		</div>
		
		<form id="order-form" action="<?= $createUrl ?>" method="post">
			<!-- Info about customer -->

			<div class="bBuyingLine mBuyingFields clearfix">

				<? if ($lifeGift): ?>
					<label for="" class="bBuyingLine__eLeft">Телефон для связи</label>
					<div class="bBuyingLine__eRight mPhone">
						<span class="bFeildStarImg">*</span>
						<span class="bPlaceholder">+7</span> 
						<input type="text" id="order_recipient_phonenumbers" class="bBuyingLine__eText mInputLong" name="order[recipient_phonenumbers]" value="" />
						<span class="phoneHintLg">Если у вас нет номера мобильного телефона, укажите телефон фонда "Подари Жизнь": <strong>+7(926)011-98-53</strong>.</span>
					</div>

                    <label for="" class="bBuyingLine__eLeft">E-mail<? if (\App::abTest()->getTest('other') && 'emails' == \App::abTest()->getTest('other')->getChosenCase()->getKey()): ?><? endif ?></label>
                    <div class="bBuyingLine__eRight">
                        <input type="text" id="order_recipient_email" class="bBuyingLine__eText mInputLong mInput265" name="order[recipient_email]" value="" />

                        <div class="bSubscibeCheck bInputList">
                            <input class="jsCustomRadio bCustomInput mCustomCheckBig" name="order[subscribe]" id="subscribe_gift" type="checkbox" checked="checked" />
                            <label class="bCustomLabel mCustomLabelBig" for="subscribe_gift">Хочу знать об интересных<br/>предложениях</label>
                        </div>
                    </div>

                    <label class="bBuyingLine__eLeft">Добрые пожелания ребенку</label>
                    <div class="bBuyingLine__eRight">
                        <textarea id="order_extra" class="bBuyingLine__eTextarea" name="order[extra]" cols="30" rows="4"></textarea>
                    </div>

				<? elseif ($oneClick): ?>
					<label for="" class="bBuyingLine__eLeft">Имя получателя</label>
					<div class="bBuyingLine__eRight">
						<span class="bFeildStarImg">*</span>
						<input type="text" id="order_recipient_first_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="" />
					</div>

					<label for="" class="bBuyingLine__eLeft">Фамилия получателя</label>
					<div class="bBuyingLine__eRight">
						<input type="text" id="order_recipient_last_name" class="bBuyingLine__eText mInputLong" name="order[recipient_last_name]" value="" />
					</div>

					<label for="" class="bBuyingLine__eLeft">E-mail<? if (\App::abTest()->getTest('other') && 'emails' == \App::abTest()->getTest('other')->getChosenCase()->getKey()): ?><? endif ?></label>
					<div class="bBuyingLine__eRight">
						<input type="text" id="order_recipient_email" class="bBuyingLine__eText mInputLong mInput265" name="order[recipient_email]" value="" />

						<div class="bSubscibeCheck bInputList">
							<input type="checkbox" name="order[subscribe]" id="subscribe_oneclick" class="jsCustomRadio bCustomInput mCustomCheckBig" checked />
							<label class="bCustomLabel mCustomLabelBig" for="subscribe_oneclick">Хочу знать об интересных<br/>предложениях</label>
						</div>
					</div>

					<label for="" class="bBuyingLine__eLeft">Телефон для связи</label>
					<div class="bBuyingLine__eRight mPhone">
						<span class="bFeildStarImg">*</span>
						<span class="bPlaceholder">+7</span> 
						<input type="text" id="order_recipient_phonenumbers" class="bBuyingLine__eText mInputLong" name="order[recipient_phonenumbers]" value="" />
					</div>

                    <?= $helper->render('order/_bonusCard', ['bonusCards' => $bonusCards, 'bonusCardsData' => $bonusCardsData]) // карты лояльности ?>

				<? else: ?>
					<label for="" class="bBuyingLine__eLeft">Имя получателя</label>
					<div class="bBuyingLine__eRight">
						<span class="bFeildStarImg">*</span>
						<input type="text" id="order_recipient_first_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="" />
					</div>

					<label for="" class="bBuyingLine__eLeft">Фамилия получателя</label>
					<div class="bBuyingLine__eRight">
						<input type="text" id="order_recipient_last_name" class="bBuyingLine__eText mInputLong" name="order[recipient_last_name]" value="" />
					</div>

					<label for="" class="bBuyingLine__eLeft">E-mail<? if (\App::abTest()->getTest('other') && 'emails' == \App::abTest()->getTest('other')->getChosenCase()->getKey()): ?><? endif ?></label>
					<div class="bBuyingLine__eRight">
						<input type="text" id="order_recipient_email" class="bBuyingLine__eText mInputLong mInput265" name="order[recipient_email]" value="" />

						<div class="bSubscibeCheck bInputList">
							<input type="checkbox" name="order[subscribe]" id="subscribe_def" class="jsCustomRadio bCustomInput mCustomCheckBig" checked />
							<label class="bCustomLabel mCustomLabelBig" for="subscribe_def">Хочу знать об интересных<br/>предложениях</label>
						</div>
					</div>

					<label for="" class="bBuyingLine__eLeft">Телефон для связи</label>
					<div class="bBuyingLine__eRight mPhone">
						<span class="bFeildStarImg">*</span>
						<span class="bPlaceholder">+7</span> 
						<input type="text" id="order_recipient_phonenumbers" class="bBuyingLine__eText mInputLong" name="order[recipient_phonenumbers]" value="" />
					</div>

					<!-- Address customer -->
					<label class="bBuyingLine__eLeft" style="min-height: 10px;" data-bind="style: { display: hasHomeDelivery() ? 'block' : 'none'}">Адрес доставки</label>
					<div class="bBuyingLine__eRight" style="width: 640px;" data-bind="style: { display: hasHomeDelivery() ? 'block' : 'none'}">
						<div class="bSelectedCity">
							<strong><?= $region->getName() ?></strong> (<a class="jsChangeRegion" href="<?= $page->url('region.change', ['regionId' => $region->getId()]) ?>">изменить</a>)
						</div>

						<div class="bInputAddress jsInputStreet ui-css">
							<span class="bFeildStarImg">*</span>
							<label style="width: 40px;" class="bPlaceholder">Улица</label>
							<input type="text" class="bBuyingLine__eText mInputLong mInputStreet ui-autocomplete-input" id="order_address_street" title="Улица" aria-haspopup="true" aria-autocomplete="list" role="textbox" autocomplete="off" name="order[address_street]" />
						</div>

						<div class="bInputAddress jsInputBuilding ui-css">
							<span class="bFeildStarImg">*</span>
							<label style="width: 27px;" class="bPlaceholder">Дом</label>
							<input type="text" id="order_address_building" class="bBuyingLine__eText mInputShort mInputBuild ui-autocomplete-input" name="order[address_building]" title="Дом" aria-haspopup="true" aria-autocomplete="list" role="textbox" autocomplete="off" />
						</div>

						<div class="bInputAddress">
							<label style="width: 40px;" class="bPlaceholder">Корпус</label>
							<input type="text" id="order_address_number" class="bBuyingLine__eText mInputShort mInputNumber" name="order[address_number]" value="" />
						</div>

						<div class="bInputAddress">
							<label class="bPlaceholder">Квартира</label>
							<input type="text" id="order_address_apartment" class="bBuyingLine__eText mInputShort mInputApartament" name="order[address_apartment]" value="" />
						</div>

						<div class="bInputAddress">
							<label style="width: 27px;" class="bPlaceholder">Этаж</label>
							<input type="text" id="order_address_floor" class="bBuyingLine__eText mInputShort mInputFloor" name="order[address_floor]" value="" />
						</div>

						<? if ((bool)$subways): ?>
						<div class="bInputAddress ui-css jsInputMetro">
							<span class="bFeildStarImg">*</span>
							<label style="width: 40px;" class="bPlaceholder">Метро</label>
							<input type="text" class="bBuyingLine__eText mInputLong ui-autocomplete-input" id="order_address_metro" title="Метро" aria-haspopup="true" aria-autocomplete="list" role="textbox" autocomplete="off" name="order[address_metro]" />
							<div id="metrostations" data-name="<?= $page->json(array_map(function(\Model\Subway\Entity $subway) { return ['val' => $subway->getId(), 'label' => $subway->getName()]; }, $subways)) ?>"></div>
							<input type="hidden" id="order_subway_id" name="order[subway_id]" value="" />
						</div>
						<? endif ?>

                        <div class="bInputAddress" id="map"></div>
					</div>

					<label class="bBuyingLine__eLeft">Пожелания и дополнения</label>
					<div class="bBuyingLine__eRight">
						<textarea id="order_extra" class="bBuyingLine__eTextarea" name="order[extra]" cols="30" rows="4"></textarea>
					</div>

                    <?= $helper->render('order/_bonusCard', ['bonusCards' => $bonusCards, 'bonusCardsData' => $bonusCardsData]) // карты лояльности ?>
				<? endif ?>
			</div>
		

			<!-- Methods of payment -->
			<? if ($oneClick && ($paymentMethod = reset($paymentMethods))): ?>
                <h2 class="bBuyingSteps__eTitle"></h2>

                <div class="bBuyingLine clearfix mPayMethods">
                    <div class="bBuyingLine__eLeft"></div>
                    <div
                        class="bBuyingLine__eRight bInputList"
                        data-bind="paymentMethodVisible: totalSum"
                        data-value="<?= $page->json(['min-sum' => \App::config()->product['minCreditPrice'], 'method_id' => \Model\PaymentMethod\Entity::CREDIT_ID, 'isAvailableToPickpoint' => true]) ?>" >

                        <input class="jsCustomRadio bCustomInput mCustomCheckBig" type="checkbox" name="order[payment_method_id]" value="<?= \Model\PaymentMethod\Entity::CREDIT_ID ?>" id="order_payment_method_id_6" <? if ($selectCredit && !$oneClick): ?>checked="checked"<? endif ?> />
                        <label class="bCustomLabel mCustomLabelBig<? if ($selectCredit && !$oneClick): ?> mChecked<? endif ?>" for="order_payment_method_id_6">
                            Купить в кредит
                        </label>

                        <?= $helper->render('order/newForm/__paymentMethod-credit', ['paymentMethod' => $paymentMethod, 'banks' => $banks, 'creditData' => $creditData]) ?>
                    </div>
                </div>
            <? else: ?>
                <h2 class="bBuyingSteps__eTitle" data-bind="css: { hidden: paypalECS }">Оплата</h2>

                <div class="bBuyingLine clearfix mPayMethods" data-bind="css: { hidden: paypalECS }">
                    <div class="bBuyingLine__eLeft"></div>
                    <div class="bBuyingLine__eRight bInputList">
                        <?= $helper->render('order/newForm/__paymentGroup', [
                            'form' => $form,
                            'paymentGroups' => $paymentGroups,
                            'banks'         => $banks,
                            'creditData'    => $creditData
                        ]) // методы оплаты ?>
                    </div>
                </div>
			<? endif ?>
			<!-- /Methods of payment -->

			<!-- PayPal сумма заказа -->
			<div data-bind="visible: paypalECS" class="bBuyingLine mPaypalLine clearfix">
				<h2 class="bBuyingSteps__eTitle mPaypal">Оплата</h2>

				<div class="bPaypalTotal">
					Итого к оплате: <span class="bPaypalTotal__eSum"><strong class="mr5" data-bind="html: window.printPrice( totalSum() )"></strong><span class="rubl">p</span></span>

					<div data-bind="visible: ( paypalECS() && ( cartSum !== undefined ) && ( totalSum() !== cartSum ) )" class="bPaypalTotalChanged"><strong>Сумма оплаты изменилась</strong></div>
				</div>
			</div>
			<!--/ PayPal сумма заказа -->

			<div class="bBuyingLine mConfirm clearfix">
				<div class="bBuyingLine__eLeft"></div>

				<div class="bBuyingLine__eRight bInputList mRules">

					<!-- Privacy and policy -->
					<span class="bFeildStarImg">*</span>
					<input class="jsCustomRadio bCustomInput mCustomCheckBig" type="checkbox" name="order[agreed]" id="order_agreed"/>
					<label class="bCustomLabel mCustomLabelBig" for="order_agreed">
						Я ознакомлен и согласен с «<a href="<?= $isCorporative ? '/corp-terms' : '/terms' ?>" target="_blank">Условиями продажи</a>» и «<a href="/legal" target="_blank">Правовой информацией</a>»
					</label>

                    <? if ($lifeGift): ?>
                        <br />
                        <input class="jsCustomRadio bCustomInput mCustomCheckBig" type="checkbox" name="order[lifeGift_agreed]" id="order_lifeGift_agreed"/>
                        <label class="bCustomLabel mCustomLabelBig" for="order_lifeGift_agreed">
                            Оформляя и оплачивая настоящий заказ я даю поручение компании ООО «Энтер» передать приобретенный мною товар в качестве дара в Благотворительный фонд помощи детям с онкогематологическими и иными тяжелыми заболеваниями «ПОДАРИ ЖИЗНЬ» (ИНН 7714320009, КПП 771401001, огрн 1067799030639) в срок до 23.12.2013*
                        </label>
                    <? endif ?>

					<div>
						<a
                            id="completeOrder"
                            class="bBigOrangeButton"
                            href="#"
                            data-bind="text: ( paypalECS() && ( cartSum !== undefined ) && ( totalSum() !== cartSum ) ) ? 'Подтвердить сумму' : 'Завершить оформление',
                            			css: { mConfirm : ( paypalECS() && ( cartSum !== undefined ) && ( totalSum() !== cartSum ) ) }"
                        ></a>

                        <!-- Сообщение о редиректе на сайт PayPal <div class="bPaypalFootnote" data-bind="visible: ( paypalECS() && ( cartSum !== undefined ) && ( totalSum() !== cartSum ) )">Вы будете перенаправлены на сайт <img class="bPaypalImgIco" src="/css/bBuyingSteps/img/bPayPalIcoSmall.gif" /></div> -->
					</div>
				</div>
			</div>
		</form>
	</div>
	<!-- /Форма заказа -->
	
	<div id="mapInfoBlock" style="display: none;">
		<div class="bMapShops__ePopupRel">
			<h3>$[properties.name]</h3>
			<span>Работает </span>
			<span>$[properties.regtime]</span>
			<br/>
			<a class="bGrayButton shopchoose" href="#" data-pointid="$[properties.id]" data-parentbox="$[properties.parentBoxToken]">$[properties.buttonName]</a>
		</div>
	</div>

	<!-- Point popup -->
	<div class="bPointPopup popup" data-bind="popupShower: showPopupWithPoints">
		<i class="close" title="Закрыть">Закрыть</i>
		<h2 data-bind="text: popupWithPoints().header"></h2>
        <div>Регион <strong><?= $user->getRegion()->getName() ?></strong> (<a class="jsChangeRegion" href="<?= $page->url('region.change', ['regionId' => $region->getId()]) ?>">изменить</a>)</div>
		<ul class="bPointList" data-bind="foreach: { data: popupWithPoints().points }">
			<li class="bPointInPopup" data-bind="click: $root.selectPoint">
				<div class="bMapShops__eListNum"><img alt="" src="/images/shop.png"></div>
				<div class="bPointInPopup__eName"><span data-bind="html: $data.name"></span> <span class="bTime" data-bind="text: $data.regtime"></span></div>
			</li>
		</ul>
		<div class="bPointPopupMap" id="pointPopupMap"></div>
	</div>
	<!-- /Point popup -->

</div>
<!-- /Общая обертка оформления заказа -->

<div id="jsOrderDelivery" data-url="<?= $deliveryUrl ?>" data-value="<?= $page->json($deliveryData) ?>"></div>
<div id="jsDeliveryAddress"
     data-value='<?= json_encode(['regionName' => $region->getName(), 'kladr' => \App::config()->kladr])?>'
     data-bind="style: { display: hasHomeDelivery() ? 'block' : 'none'}">
</div>
<div id="jsOrderForm" data-value="<?= $page->json([
	'order[recipient_first_name]'   => $form->getFirstName(),
	'order[recipient_last_name]'    => $form->getLastName(),
	'order[recipient_email]'        => $form->getEmail(),
	'order[recipient_phonenumbers]' => substr($form->getMobilePhone(), -10),
	'order[subway_id]'              => $form->getSubwayId(),
	'order[address_street]'         => $form->getAddressStreet(),
	'order[address_number]'         => $form->getAddressNumber(),
	'order[address_building]'       => $form->getAddressBuilding(),
	'order[address_floor]'          => $form->getAddressFloor(),
	'order[address_apartment]'      => $form->getAddressApartment(),
	'order[payment_method_id]'      => $form->getPaymentMethodId(),
]) ?>"></div>

<?php if (\App::config()->analytics['enabled']): ?>
	<?= $page->tryRender('order/partner-counter/_cityads-create') ?>
<?php endif ?>
