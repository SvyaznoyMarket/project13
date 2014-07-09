<!-- шапка оформления заказа -->
<header class="orderHead">
	<img class="orderHead_logo" src="/styles/order/img/logo.png" />

	<ul class="orderHead_steps">
		<li class="orderHead_steps_item">Получатель</li>
		<li class="orderHead_steps_item orderHead_steps_item-active">Самовывоз и доставка</li>	
		<li class="orderHead_steps_item">Оплата</li>				
	</ul>
</header>
<!--/ шапка оформления заказа -->

<section class="orderCont">
	<h1 class="orderCont_title">Оформление заказа</h1>

	<form class="orderUser" action="" method="" accept-charset="utf-8">
		<fieldset class="orderUser_fieldset">
			<div class="orderUser_field">
				<label class="orderUser_label orderUser_label-strict" for="">Телефон</label>
				<input class="orderUser_text textfield" type="text" name="" value="" placeholder="">
				<span class="orderUser_hint">Для смс о состоянии заказа</span>
			</div>

			<div class="orderUser_field">
				<label class="orderUser_label" for="">E-mail</label>
				<input class="orderUser_text textfield" type="text" name="" value="" placeholder="">
			</div>

			<div class="orderUser_field">
				<label class="orderUser_label" for="">Имя</label>
				<input class="orderUser_text textfield" type="text" name="" value="" placeholder="">
				<span class="orderUser_hint">Как к вам обращаться?</span>
			</div>
		</fieldset>

		<fieldset class="orderUser_fieldset">
			<span class="orderBonus">Начислить баллы</span>

			<img class="orderBonusItem" src="/styles/order/img/sClub.png" alt="" />
			<img class="orderBonusItem" src="/styles/order/img/sBank.png" alt="" />
		</fieldset>
	</form>

	<div class="orderAuth">
		<div class="orderAuth_title">Уже заказывали у нас?</div>
		<button class="orderAuth_btn btnLightGrey">Войти с паролем</button>
	</div>

	<div class="orderComplete clearfix">
		<div class="orderComplete_left orderComplete_left-line orderCheck orderCheck-strikt mb10">
			<input type="checkbox" class="customInput customInput-checkbox" id="accept" name="" value="" />
			<label  class="customLabel" for="accept">
				Я ознакомлен и согласен с «Условиями продажи» и «Правовой информацией»
			</label>
		</div>

		<button class="orderComplete_btn btnsubmit">Далее ➜</button>
	</div>
</section>

<section class="orderCont">
	<h1 class="orderCont_title">Самовывоз и доставка</h1>
	<!-- заголовок страницы -->

	<p class="orderInfo">Товары будут оформлены как  <strong>3 отдельных заказа</strong></p>

	<div class="orderInfo clearfix">
		<div>Ваш регион: <strong>Москва</strong></div>

		<div class="fl-l">От региона зависят доступные способы получения и оплаты заказов.</div>

		<button class="btnLightGrey fl-r">Изменить регион</button>
	</div>

	<!-- блок разбиения заказа -->
	<div class="orderRow clearfix">
		<!-- информация о заказе -->
		<div class="orderCell">
			<div class="orderCellHead">
				<strong>Заказ №1</strong> 
				<span class="colorBrightGrey">продавец: ООО «Связной»</span>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
				</a>

				<a href="" class="orderItem_name">
					Самокат<br/>
					JD Bug Classic MS-305 синий
				</a>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ fl-r">5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-count fl-r">1 шт.</span>
					<span class="orderItem_data_item orderItem_data_item-price fl-r">5 200 <span class="rubl">p</span></span>
				</div>
			</div>

			<div class="orderCellFoot clearfix">
				<div class="orderCellFoot_left">
					<span>Ввести код скидки</span>
				</div>

				<div class="orderCellFoot_right">
					<span class="orderCheckSumm l">150 <span class="rubl">p</span></span>
					<span class="orderCheckTitle">Доставка:</span>
					
					<span class="orderCheckSumm l">2 334 <span class="rubl">p</span></span>
					<span class="orderCheckTitle l">Итого:</span>
				</div>
			</div>
		</div>
		<!--/ информация о заказе -->

		<!-- информация о доставке -->
		<div class="orderCell orderCell-right">
			<menu class="orderDelivery">
				<li class="orderDelivery_item orderDelivery_item-active">Самовывоз</li>
				<li class="orderDelivery_item">Доставка</li>
			</menu>

			<!-- дата доставки -->
			<div class="orderDeliveryInfo clearfix">
				15 сентября 2014, воскресенье 
				<span class="orderChange">изменить дату</span>
			</div>
			<!--/ дата доставки -->

			<!-- способ доставки -->
			<div class="orderDeliveryInfo orderDeliveryInfo-pl">
				<div class="deliveryTitle clearfix">
					<strong>Постамат PickPoint</strong>

					<span class="orderChange">изменить место</span>
				</div>

				<div class="deliveryAddress" style="background: red;">
					<span class="deliveryAddress_text">
						м. Петровско-Разумовская<br/>
						<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
					</span>
				</div>

				<div class="deliveryTime">
					<span class="deliveryTime_title">Режим работы:</span> с 9.00 до 22.00
				</div>
			</div>
			<!--/ способ доставки -->
		</div>
		<!--/ информация о доставке -->
	</div>
	<!--/ блок разбиения заказа -->

	<!-- блок разбиения заказа -->
	<div class="orderRow clearfix">
		<!-- информация о заказе -->
		<div class="orderCell">
			<div class="orderCellHead">
				<strong>Заказ №1</strong> 
				<span class="colorBrightGrey">продавец: ООО «Связной»</span>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
				</a>

				<a href="" class="orderItem_name">
					Самокат<br/>
					JD Bug Classic MS-305 синий
				</a>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ fl-r">5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-count fl-r">1 шт.</span>
					<span class="orderItem_data_item orderItem_data_item-price fl-r">5 200 <span class="rubl">p</span></span>
				</div>
			</div>

			<div class="orderCellFoot clearfix">
				<div class="orderCellFoot_left">
					<span>Ввести код скидки</span>
				</div>

				<div class="orderCellFoot_right">
					<span class="orderCheckSumm l">150 <span class="rubl">p</span></span>
					<span class="orderCheckTitle">Доставка:</span>
					
					<span class="orderCheckSumm l">2 334 <span class="rubl">p</span></span>
					<span class="orderCheckTitle l">Итого:</span>
				</div>
			</div>
		</div>
		<!--/ информация о заказе -->

		<!-- информация о доставке -->
		<div class="orderCell orderCell-right">
			<menu class="orderDelivery">
				<li class="orderDelivery_item orderDelivery_item-active">Самовывоз</li>
				<li class="orderDelivery_item">Доставка</li>
			</menu>

			<!-- дата доставки -->
			<div class="orderDeliveryInfo clearfix">
				15 сентября 2014, воскресенье 
				<span class="orderChange">изменить дату</span>
			</div>
			<!--/ дата доставки -->

			<!-- способ доставки -->
			<div class="orderDeliveryInfo orderDeliveryInfo-pl orderDeliveryInfo-bg">
				<div class="deliveryTitle clearfix">
					<strong>Место самовывоза</strong>
				</div>

				<button class="btnLightGrey">Магазин Enter</button>
				<button class="btnLightGrey">Постамат PickPoint</button>
			</div>
			<!--/ способ доставки -->
		</div>
		<!--/ информация о доставке -->
	</div>
	<!--/ блок разбиения заказа -->

	<!-- блок разбиения заказа -->
	<div class="orderRow clearfix">
		<!-- информация о заказе -->
		<div class="orderCell">
			<div class="orderCellHead">
				<strong>Заказ №1</strong> 
				<span class="colorBrightGrey">продавец: ООО «Связной»</span>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
				</a>

				<a href="" class="orderItem_name">
					Самокат<br/>
					JD Bug Classic MS-305 синий
				</a>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ fl-r">5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-count fl-r">1 шт.</span>
					<span class="orderItem_data_item orderItem_data_item-price fl-r">5 200 <span class="rubl">p</span></span>
				</div>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
				</a>

				<a href="" class="orderItem_name">
					Самокат<br/>
					JD Bug Classic MS-305 синий
				</a>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ fl-r">5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-count fl-r">1 шт.</span>
					<span class="orderItem_data_item orderItem_data_item-price fl-r">5 200 <span class="rubl">p</span></span>
				</div>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
				</a>

				<a href="" class="orderItem_name">
					Самокат<br/>
					JD Bug Classic MS-305 синий
				</a>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ fl-r">5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-count fl-r">1 шт.</span>
					<span class="orderItem_data_item orderItem_data_item-price fl-r">5 200 <span class="rubl">p</span></span>
				</div>
			</div>

			<div class="orderItemTitle">Скидки</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="/styles/order/img/fishka.png" alt="">
				</a>

				<div class="orderItem_name">
					Фишка со скидкой 2% на категорию Электроника<br/>
					Минимальная сумма заказа 6999 руб
				</div>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ orderItem_data_item-sale fl-r">-5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-delete fl-r">удалить</span>
				</div>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="/styles/order/img/enter.png" alt="">
				</a>

				<div class="orderItem_name">
					Подарочный сертификат 5000 руб
				</div>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ orderItem_data_item-sale fl-r">-15 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-delete fl-r">удалить</span>
				</div>
			</div>

			<div class="orderCellFoot clearfix">
				<div class="orderCellFoot_left">
					<div class="orderCellFoot_title">Код скидки, подарочный сертификат</div>

					<input class="cuponField textfieldgrey" type="text" name="" value="" placeholder="" />
					<button class="cuponBtn btnLightGrey">Применить</button>
				</div>

				<div class="orderCellFoot_right">
					<span class="orderCheckSumm l">Бесплатно</span>
					<span class="orderCheckTitle">Самовывоз:</span>
					
					<span class="orderCheckSumm l">
						<sapn class="td-lineth colorBrightGrey">42 580 <span class="rubl">p</span></sapn><br/>
						2 334 <span class="rubl">p</span>
					</span>
					<span class="orderCheckTitle l">Итого:</span>
				</div>

				<div class="orderCheck orderCheck-credit clearfix">
					<input type="checkbox" class="customInput customInput-checkbox" id="credit" name="" value="" />
					<label  class="customLabel" for="credit">Купить в кредит, от 2 223 <span class="rubl">p</span> в месяц</label>
				</div>
			</div>
		</div>
		<!--/ информация о заказе -->

		<!-- информация о доставке -->
		<div class="orderCell orderCell-right">
			<menu class="orderDelivery">
				<li class="orderDelivery_item orderDelivery_item-active">Самовывоз</li>
				<li class="orderDelivery_item">Доставка</li>
			</menu>

			<!-- дата доставки -->
			<div class="orderDeliveryInfo clearfix">
				15 сентября 2014, воскресенье 
				<span class="orderChange">изменить дату</span>
			</div>
			<!--/ дата доставки -->

			<!-- способ доставки -->
			<div class="orderDeliveryInfo orderDeliveryInfo-pl">
				<div class="deliveryTitle clearfix">
					<strong>Магазин</strong>

					<span class="orderChange">изменить место</span>
				</div>

				<div class="deliveryAddress" style="background: red;">
					<span class="deliveryAddress_text">
						м. Петровско-Разумовская<br/>
						<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
					</span>
				</div>

				<div class="deliveryTime">
					<span class="deliveryTime_title">Режим работы:</span> с 9.00 до 22.00
					<span class="deliveryTime_title">Оплата при получении: </span> 

					<img class="deliveryTime_paymentImg" src="/styles/order/img/cash.png" alt="" /> 
					<img class="deliveryTime_paymentImg" src="/styles/order/img/cards.png" alt="">
				</div>
			</div>
			<!--/ способ доставки -->
		</div>
		<!--/ информация о доставке -->
	</div>
	<!--/ блок разбиения заказа -->

	<!-- блок разбиения заказа -->
	<div class="orderRow clearfix">
		<!-- информация о заказе -->
		<div class="orderCell">
			<div class="orderCellHead">
				<strong>Заказ №1</strong> 
				<span class="colorBrightGrey">продавец: ООО «Связной»</span>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
				</a>

				<a href="" class="orderItem_name">
					Самокат<br/>
					JD Bug Classic MS-305 синий
				</a>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ fl-r">5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-count fl-r">1 шт.</span>
					<span class="orderItem_data_item orderItem_data_item-price fl-r">5 200 <span class="rubl">p</span></span>
				</div>
			</div>

			<div class="orderCellFoot clearfix">
				<div class="orderCellFoot_left">
					<span>Ввести код скидки</span>
				</div>

				<div class="orderCellFoot_right">
					<span class="orderCheckSumm l">290 <span class="rubl">p</span></span>
					<span class="orderCheckTitle">Доставка:</span>
					
					<span class="orderCheckSumm l">
						2 334 <span class="rubl">p</span>
					</span>
					<span class="orderCheckTitle l">Итого:</span>
				</div>

				<div class="orderCheck orderCheck-credit clearfix">
					<input type="checkbox" class="customInput customInput-checkbox" id="credit1" name="" value="" />
					<label  class="customLabel" for="credit1">Купить в кредит, от 2 223 <span class="rubl">p</span> в месяц</label>
				</div>
			</div>
		</div>
		<!--/ информация о заказе -->

		<!-- информация о доставке -->
		<div class="orderCell orderCell-right">
			<menu class="orderDelivery">
				<li class="orderDelivery_item orderDelivery_item-active">Доставка</li>
			</menu>

			<!-- дата доставки -->
			<div class="orderDeliveryInfo clearfix">
				15 сентября 2014, воскресенье 
				<span class="orderChange">изменить дату</span>
			</div>
			<!--/ дата доставки -->

			<!-- способ доставки -->
			<div class="orderDeliveryInfo orderDeliveryInfo-bg">
				<div class="deliveryTitle clearfix">
					<strong>Адрес</strong> <span class="colorBrightGrey">для всех заказов с доставкой</span>
				</div>

				<div class="deliveryAddress">
					<input class="deliveryAddress_field textfield" type="text" name="" value="" placeholder="" />
				</div>
			</div>

			<div class="orderCheck mb10">
				<input type="checkbox" class="customInput customInput-checkbox" id="creditCardsPay" name="" value="" />
				<label  class="customLabel" for="creditCardsPay">
					Оплата банковской картой
					<span class="dblock colorBrightGrey s">Иначе курьер сможет принять только наличные</span>
				</label>
			</div>
			<!--/ способ доставки -->
		</div>
		<!--/ информация о доставке -->
	</div>
	<!--/ блок разбиения заказа -->

	<!-- блок разбиения заказа -->
	<div class="orderRow clearfix">
		<!-- информация о заказе -->
		<div class="orderCell">
			<div class="orderCellHead">
				<strong>Заказ №1</strong> 
				<span class="colorBrightGrey">продавец: ООО «Связной»</span>
			</div>

			<div class="orderItem">
				<a href="" class="orderItem_img">
					<img class="orderItem_img_resize" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
				</a>

				<a href="" class="orderItem_name">
					Самокат<br/>
					JD Bug Classic MS-305 синий
				</a>

				<div class="orderItem_data clearfix">
					<span class="orderItem_data_item orderItem_data_item-summ fl-r">5 200 <span class="rubl">p</span></span>
					<span class="orderItem_data_item orderItem_data_item-count fl-r">1 шт.</span>
					<span class="orderItem_data_item orderItem_data_item-price fl-r">5 200 <span class="rubl">p</span></span>
				</div>
			</div>

			<div class="orderCellFoot clearfix">
				<div class="orderCellFoot_left">
					<span>Ввести код скидки</span>
				</div>

				<div class="orderCellFoot_right">
					<span class="orderCheckSumm l">290 <span class="rubl">p</span></span>
					<span class="orderCheckTitle">Доставка:</span>
					
					<span class="orderCheckSumm l">
						2 334 <span class="rubl">p</span>
					</span>
					<span class="orderCheckTitle l">Итого:</span>
				</div>
			</div>
		</div>
		<!--/ информация о заказе -->

		<!-- информация о доставке -->
		<div class="orderCell orderCell-right">
			<menu class="orderDelivery">
				<li class="orderDelivery_item orderDelivery_item-active">Доставка</li>
			</menu>

			<!-- дата доставки -->
			<div class="orderDeliveryInfo clearfix">
				15 сентября 2014, воскресенье

				<div class="customSel">
					<span class="customSel_def"></span>

					<ul class="customSel_lst">
						<li class="customSel_i"></li>
					</ul>
				</div>

				<span class="orderChange">изменить дату</span>
			</div>
			<!--/ дата доставки -->

			<!-- способ доставки -->
			<div class="orderDeliveryInfo">
				<div class="deliveryTitle clearfix">
					<strong>Адрес</strong> <span class="colorBrightGrey">для всех заказов с доставкой</span> 
					<span class="orderChange">изменить место</span>
				</div>

				<div class="deliveryAddress">
					ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2
				</div>
			</div>

			<div class="orderCheck mb10">
				<input type="checkbox" class="customInput customInput-checkbox" id="creditCardsPay" name="" value="" />
				<label  class="customLabel" for="creditCardsPay">
					Оплата банковской картой
					<span class="dblock colorBrightGrey s">Иначе курьер сможет принять только наличные</span>
				</label>
			</div>
			<!--/ способ доставки -->
		</div>
		<!--/ информация о доставке -->
	</div>
	<!--/ блок разбиения заказа -->

	<div class="orderComment">
		<div class="orderComment_title">Дополнительные пожелания</div>

		<textarea class="orderComment_field textarea"></textarea>
	</div>

	<div class="orderComplete clearfix">
		<p class="orderComplete_left">
			<span class="l">Итого <strong>3</strong> заказа на общую сумму <strong>123 000 <span class="rubl">p</span></strong></span>
			<span class="colorBrightGrey dblock">Вы сможете заполнить заявку на кредит и оплатить онлайн на следующем шаге</span>
		</p>

		<button class="orderComplete_btn btnsubmit">Оформить ➜</button>
	</div>
</section>

<section class="orderCont">
	<h1 class="orderCont_title">Заказы оформлены</h1>

	<p>
		Вы получите смс с номерами заказов. <br/>
		С вами свяжется курьер для уточнения удобного для вас времени доставки.
	</p>

	<p>
		Вы можете оплатить свой заказ онлайн. 
		<img src="/styles/order/img/master.png" alt="" />
		<img src="/styles/order/img/Visa.png" alt="" />
		<img src="/styles/order/img/Maestro.png" alt="" />
		<img src="/styles/order/img/paypal.png" alt="" />
		<img src="/styles/order/img/psb.png" alt="" />
	</p>

	<p>При получении заказа всегда принимаем наличные. </p>

	<!-- таблица текущих заказов -->
    <div class="personalTable personalTable-border personalTable-bg">
        <div class="personalTable_row personalTable_row-head">
            <div class="personalTable_cell personalTable_cell-w90">№ заказа</div>

            <div class="personalTable_cell personalTable_cell-w212">Состав</div>

            <div class="personalTable_cell personalTable_cell-w115 ta-c">Сумма</div>

            <div class="personalTable_cell personalTable_cell-w175">Получение</div>

            <div class="personalTable_cell">Статус</div>

            <div class="personalTable_cell"></div>
        </div>

        <div class="personalTable_row">
            <div class="personalTable_cell ta-c">
                <a href="">COXD-305176</a> 
                <span class="s dblock">06 июн. 2014</span>
            </div>

            <div class="personalTable_cell personalTable_cell-text">
                <ul class="orderItem">
                    <li>Бумажный конструктор 3 шт.</li>
                    <li>Карта памяти microSDHC… 1 шт.</li>
                    <li><a href="">и ещё 3 товара</a></li>
                </ul>
            </div>

            <div class="personalTable_cell ta-r">
                46 740 <span class="rubl">p</span>
                <strong class="s dblock">Заказ оплачен</strong>
            </div>

            <div class="personalTable_cell">
                Доставка
                <span class="s dblock">11 июл. 2014 9:00…18:00</span>
            </div>

            <div class="personalTable_cell">В обработке</div>

            <div class="personalTable_cell"></div>
        </div>
        
        <!-- ----------------- -->

        <div class="personalTable_row">
            <div class="personalTable_cell ta-c">
                <a href="">COXD-305176</a> 
                <span class="s dblock">06 июн. 2014</span>
            </div>

            <div class="personalTable_cell personalTable_cell-text">
                <ul class="orderItem">
                    <li>Бумажный конструктор 3 шт.</li>
                    <li>Карта памяти microSDHC… 1 шт.</li>
                    <li><a href="">и ещё 3 товара</a></li>
                </ul>
            </div>

            <div class="personalTable_cell ta-r">
                46 740 <span class="rubl">p</span>
                <span class="s dblock">Оплачено: <span class="m">43 <span class="rubl">p</span></span></span>
                <span class="s dblock">К оплате: <span class="m">434 <span class="rubl">p</span></span></span>
            </div>

            <div class="personalTable_cell">
                Доставка
                <span class="s dblock">11 июл. 2014 9:00…18:00</span>
            </div>

            <div class="personalTable_cell">Готов к передаче</div>

            <div class="personalTable_cell"></div>
        </div>

        <!-- ----------------- -->

        <div class="personalTable_row">
            <div class="personalTable_cell ta-c">
                <a href="">COXD-305176</a> 
                <span class="s dblock">06 июн. 2014</span>
            </div>

            <div class="personalTable_cell personalTable_cell-text">
                <ul class="orderItem">
                    <li>Бумажный конструктор 3 шт.</li>
                    <li>Карта памяти microSDHC… 1 шт.</li>
                    <li><a href="">и ещё 3 товара</a></li>
                </ul>
            </div>

            <div class="personalTable_cell ta-r">
                46 740 <span class="rubl">p</span>
                <strong class="s dblock">Покупка в кредит</strong>
                <span class="s dblock">К оплате: <span class="m">434 <span class="rubl">p</span></span></span>
            </div>

            <div class="personalTable_cell">
                Доставка
                <span class="s dblock">11 июл. 2014 9:00…18:00</span>
            </div>

            <div class="personalTable_cell">В обработке</div>

            <div class="personalTable_cell personalTable_cell-last personalTable_cell-mark ta-r">
                <button class="tableBtn btnLightGrey s">Заполнить заявку<br/>на кредит</button>
            </div>
        </div>

        <!-- ----------------- -->

        <div class="personalTable_row">
            <div class="personalTable_cell ta-c">
                <a href="">COXD-305176</a> 
                <span class="s dblock">06 июн. 2014</span>
            </div>

            <div class="personalTable_cell personalTable_cell-text">
                <ul class="orderItem">
                    <li>Бумажный конструктор 3 шт.</li>
                    <li>Карта памяти microSDHC… 1 шт.</li>
                    <li><a href="">и ещё 3 товара</a></li>
                </ul>
            </div>

            <div class="personalTable_cell ta-r">
                46 740 <span class="rubl">p</span>
            </div>

            <div class="personalTable_cell">
                Доставка
                <span class="s dblock">11 июл. 2014 9:00…18:00</span>
            </div>

            <div class="personalTable_cell">В обработке</div>

            <div class="personalTable_cell"></div>
        </div>
    </div>
    <!--/ таблица текущих заказов -->
</section>








