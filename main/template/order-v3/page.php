<?php

return function(
    \Helper\TemplateHelper $helper
) {

    // FIXME: удалить файл
?>

    <!-- шапка оформления заказа -->
    <header class="orderHd">
        <img class="orderHd_lg" src="/styles/order/img/logo.png" />

        <ul class="orderHd_stps">
            <!-- если шаг пройден то orderHd_stps_i-pass, текущий шаг orderHd_stps_i-act -->
            <li class="orderHd_stps_i orderHd_stps_i-pass">Получатель</li>
            <li class="orderHd_stps_i orderHd_stps_i-act">Самовывоз и доставка</li>
            <li class="orderHd_stps_i">Оплата</li>
        </ul>
    </header>
    <!--/ шапка оформления заказа -->

    <section class="orderCnt">
        <h1 class="orderCnt_t">Оформление заказа</h1>

        <form class="orderU" action="" method="" accept-charset="utf-8">
            <fieldset class="orderU_flds">
                <div class="orderU_fld">
                    <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                    <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
                    <span class="orderU_hint">Для смс о состоянии заказа</span>
                </div>

                <div class="orderU_fld">
                    <label class="orderU_lbl" for="">E-mail</label>
                    <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
                </div>

                <div class="orderU_fld">
                    <label class="orderU_lbl" for="">Имя</label>
                    <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
                    <span class="orderU_hint">Как к вам обращаться?</span>
                </div>
            </fieldset>

            <fieldset class="orderU_flds">
                <span class="orderU_bonus">Начислить баллы</span>

                <img class="orderU_bonusImg" src="/styles/order/img/sClub.png" alt="" />
                <img class="orderU_bonusImg" src="/styles/order/img/sBank.png" alt="" />
            </fieldset>
        </form>

        <div class="orderAuth">
            <div class="orderAuth_t">Уже заказывали у нас?</div>
            <button class="orderAuth_btn btnLightGrey">Войти с паролем</button>
        </div>

        <div class="orderCompl clearfix">
            <div class="orderCompl_l orderCompl_l-ln orderCheck orderCheck-str mb10">
                <input type="checkbox" class="customInput customInput-checkbox" id="accept" name="" value="" />

                <label  class="customLabel" for="accept">
                    Я ознакомлен и согласен с «Условиями продажи» и «Правовой информацией»
                </label>
            </div>

            <button class="orderCompl_btn btnsubmit">Далее ➜</button>
        </div>
    </section>

    <section class="orderCnt">
    <h1 class="orderCnt_t">Самовывоз и доставка</h1>
    <!-- заголовок страницы -->

    <p class="orderInf">Товары будут оформлены как  <strong>3 отдельных заказа</strong></p>

    <div class="orderInf clearfix">
        <div>Ваш регион: <strong>Москва</strong></div>

        <div class="fl-l">От региона зависят доступные способы получения и оплаты заказов.</div>

        <button class="btnLightGrey fl-r">Изменить регион</button>
    </div>

    <!-- блок разбиения заказа -->
    <div class="orderRow clearfix">
        <!-- информация о заказе -->
        <div class="orderCol">
            <div class="orderCol_h">
                <strong>Заказ №1</strong>
                <span class="colorBrightGrey">продавец: ООО «Связной»</span>
            </div>

            <div class="orderCol_cnt clearfix">
                <a href="" class="orderCol_lk">
                    <img class="orderCol_img" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
                </a>

                <a href="" class="orderCol_n">
                    Самокат<br/>
                    JD Bug Classic MS-305 синий
                </a>

                <span class="orderCol_data orderCol_data-summ">5 200 <span class="rubl">p</span></span>
                <span class="orderCol_data orderCol_data-count">1 шт.</span>
                <span class="orderCol_data orderCol_data-price">5 200 <span class="rubl">p</span></span>
            </div>

            <div class="orderCol_f clearfix">
                <div class="orderCol_f_l">
                    <span class="orderCol_f_t brb-dt">Ввести код скидки</span>
                </div>

                <div class="orderCol_f_r">
                    <span class="orderCol_summ">150 <span class="rubl">p</span></span>
                    <span class="orderCol_summt orderCol_summt-m">Доставка:</span>

                    <span class="orderCol_summ">2 334 <span class="rubl">p</span></span>
                    <span class="orderCol_summt">Итого:</span>
                </div>
            </div>
        </div>
        <!--/ информация о заказе -->

        <!-- информация о доставке -->
        <div class="orderCol orderCol-r">
            <menu class="orderCol_delivrLst">
                <li class="orderCol_delivrLst_i orderCol_delivrLst_i-act">Самовывоз</li>
                <li class="orderCol_delivrLst_i">Доставка</li>
            </menu>

            <!-- дата доставки -->
            <div class="orderCol_delivrIn clearfix">
                <div class="orderCol_date">15 сентября 2014, воскресенье</div>
                <span class="orderChange">изменить дату</span>

                <div class="celedr popupFl">
                    <div class="popupFl_clsr"></div>

                    <div class="celedr_t">27 сентября, воскресенье</div>

                    <button class="celedr_btn btn2">Хочу быстрее!</button>

                    <div class="celedr_tb">
                        <div class="celedr_row celedr_row-h clearfix">
                            <div class="celedr_col">Пн</div>
                            <div class="celedr_col">Вт</div>
                            <div class="celedr_col">Ср</div>
                            <div class="celedr_col">Чт</div>
                            <div class="celedr_col">Пт</div>
                            <div class="celedr_col">Сб</div>
                            <div class="celedr_col">Вс</div>
                        </div>

                        <div class="celedr_row clearfix">
                            <div class="celedr_month">Июль</div>
                            <div class="celedr_col celedr_col-disbl ">28</div>
                            <div class="celedr_col celedr_col-disbl ">29</div>
                            <div class="celedr_col  celedr_curr">30</div>
                            <div class="celedr_col  ">31</div>
                            <div class="celedr_col celedr_col-disbl "></div>
                            <div class="celedr_col celedr_col-disbl "></div>
                            <div class="celedr_col celedr_col-disbl "></div>
                            <div class="celedr_month">Август</div>
                            <div class="celedr_col celedr_col-disbl "></div>
                            <div class="celedr_col celedr_col-disbl "></div>
                            <div class="celedr_col celedr_col-disbl "></div>
                            <div class="celedr_col celedr_col-disbl "></div>
                            <div class="celedr_col  ">1</div>
                            <div class="celedr_col  ">2</div>
                            <div class="celedr_col  ">3</div>
                            <div class="celedr_col  ">4</div>
                            <div class="celedr_col  ">5</div>
                            <div class="celedr_col  ">6</div>
                            <div class="celedr_col  ">7</div>
                            <div class="celedr_col  ">8</div>
                            <div class="celedr_col  ">9</div>
                            <div class="celedr_col  ">10</div>
                            <div class="celedr_col  ">11</div>
                            <div class="celedr_col  ">12</div>
                            <div class="celedr_col  ">13</div>
                            <div class="celedr_col  ">14</div>
                            <div class="celedr_col  ">15</div>
                            <div class="celedr_col  ">16</div>
                            <div class="celedr_col  ">17</div>
                            <div class="celedr_col  ">18</div>
                            <div class="celedr_col  ">19</div>
                            <div class="celedr_col  ">20</div>
                            <div class="celedr_col  ">21</div>
                            <div class="celedr_col  ">22</div>
                            <div class="celedr_col  ">23</div>
                            <div class="celedr_col  ">24</div>
                            <div class="celedr_col  ">25</div>
                            <div class="celedr_col  ">26</div>
                            <div class="celedr_col  ">27</div>
                            <div class="celedr_col  ">28</div>
                            <div class="celedr_col  ">29</div>
                            <div class="celedr_col  ">30</div>
                            <div class="celedr_col celedr_col-disbl ">31</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ дата доставки -->

            <!-- способ доставки -->
            <div class="orderCol_delivrIn orderCol_delivrIn-pl">
                <div class="orderCol_delivrIn_t clearfix">
                    <strong>Постамат PickPoint</strong>

                    <span class="orderChange">изменить место</span>
                </div>

                <div class="orderCol_addrs" style="background: red;">
					<span class="orderCol_addrs_tx">
						м. Петровско-Разумовская<br/>
						<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
					</span>
                </div>

                <div class="orderCol_tm">
                    <span class="orderCol_tm_t">Режим работы:</span> с 9.00 до 22.00
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
        <div class="orderCol">
            <div class="orderCol_h">
                <strong>Заказ №1</strong>
                <span class="colorBrightGrey">продавец: ООО «Связной»</span>
            </div>

            <div class="orderCol_cnt clearfix">
                <a href="" class="orderCol_lk">
                    <img class="orderCol_img" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
                </a>

                <a href="" class="orderCol_n">
                    Самокат<br/>
                    JD Bug Classic MS-305 синий
                </a>

                <span class="orderCol_data orderCol_data-summ">5 200 <span class="rubl">p</span></span>
                <span class="orderCol_data orderCol_data-count">1 шт.</span>
                <span class="orderCol_data orderCol_data-price">5 200 <span class="rubl">p</span></span>
            </div>

            <div class="orderCol_f clearfix">
                <div class="orderCol_f_l">
                    <span class="orderCol_f_t brb-dt">Ввести код скидки</span>
                </div>

                <div class="orderCol_f_r">
                    <span class="orderCol_summ">150 <span class="rubl">p</span></span>
                    <span class="orderCol_summt orderCol_summt-m">Доставка:</span>

                    <span class="orderCol_summ">2 334 <span class="rubl">p</span></span>
                    <span class="orderCol_summt">Итого:</span>
                </div>
            </div>
        </div>
        <!--/ информация о заказе -->

        <!-- информация о доставке -->
        <div class="orderCol orderCol-r">
            <menu class="orderCol_delivrLst">
                <li class="orderCol_delivrLst_i orderCol_delivrLst_i-act">Самовывоз</li>
                <li class="orderCol_delivrLst_i">Доставка</li>
            </menu>

            <!-- дата доставки -->
            <div class="orderCol_delivrIn clearfix">
                15 сентября 2014, воскресенье
                <span class="orderChange">изменить дату</span>
            </div>
            <!--/ дата доставки -->

            <!-- способ доставки -->
            <div class="orderCol_delivrIn orderCol_delivrIn-pl orderCol_delivrIn-bg">
                <div class="orderCol_delivrIn_t clearfix">
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
    <div class="orderCol">
        <div class="orderCol_h">
            <strong>Заказ №1</strong>
            <span class="colorBrightGrey">продавец: ООО «Связной»</span>
        </div>

        <div class="orderCol_cnt clearfix">
            <a href="" class="orderCol_lk">
                <img class="orderCol_img" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
            </a>

            <a href="" class="orderCol_n">
                Самокат<br/>
                JD Bug Classic MS-305 синий
            </a>

            <span class="orderCol_data orderCol_data-summ">5 200 <span class="rubl">p</span></span>
            <span class="orderCol_data orderCol_data-count">1 шт.</span>
            <span class="orderCol_data orderCol_data-price">5 200 <span class="rubl">p</span></span>
        </div>

        <div class="orderCol_cnt clearfix">
            <a href="" class="orderCol_lk">
                <img class="orderCol_img" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
            </a>

            <a href="" class="orderCol_n">
                Самокат<br/>
                JD Bug Classic MS-305 синий
            </a>

            <span class="orderCol_data orderCol_data-summ">5 200 <span class="rubl">p</span></span>
            <span class="orderCol_data orderCol_data-count">1 шт.</span>
            <span class="orderCol_data orderCol_data-price">5 200 <span class="rubl">p</span></span>
        </div>

        <div class="orderCol_cnt clearfix">
            <a href="" class="orderCol_lk">
                <img class="orderCol_img" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
            </a>

            <a href="" class="orderCol_n">
                Самокат<br/>
                JD Bug Classic MS-305 синий
            </a>

            <span class="orderCol_data orderCol_data-summ">5 200 <span class="rubl">p</span></span>
            <span class="orderCol_data orderCol_data-count">1 шт.</span>
            <span class="orderCol_data orderCol_data-price">5 200 <span class="rubl">p</span></span>
        </div>

        <div class="orderCol_t">Скидки</div>

        <div class="orderCol_cnt clearfix">
            <a href="" class="orderCol_lk">
                <img class="orderCol_img" src="/styles/order/img/fishka.png" alt="">
            </a>

            <div class="orderCol_n">
                Фишка со скидкой 2% на категорию Электроника<br/>
                Минимальная сумма заказа 6999 руб
            </div>

            <span class="orderCol_data orderCol_data-summ orderCol_i_data-sale">-15 200 <span class="rubl">p</span></span>
            <span class="orderCol_data orderCol_data-del">удалить</span>
        </div>

        <div class="orderCol_cnt clearfix">
            <a href="" class="orderCol_lk">
                <img class="orderCol_img" src="/styles/order/img/enter.png" alt="">
            </a>

            <div class="orderCol_n">
                Подарочный сертификат 5000 руб
            </div>

            <span class="orderCol_data orderCol_data-summ orderCol_data-sale">-15 200 <span class="rubl">p</span></span>
            <span class="orderCol_data orderCol_data-del">удалить</span>
        </div>

        <div class="orderCol_f clearfix">
            <div class="orderCol_f_l">
                <div class="orderCol_f_t">Код скидки, подарочный сертификат</div>

                <input class="cuponField textfieldgrey" type="text" name="" value="" placeholder="" />
                <button class="cuponBtn btnLightGrey">Применить</button>
            </div>

            <div class="orderCol_f_r">
                <span class="orderCol_summ">Бесплатно</span>
                <span class="orderCol_summt orderCol_summt-m">Самовывоз:</span>

					<span class="orderCol_summ">
						<sapn class="td-lineth colorBrightGrey">42 580 <span class="rubl">p</span></sapn><br/>
						2 334 <span class="rubl">p</span>
					</span>
                <span class="orderCol_summt">Итого:</span>
            </div>

            <div class="orderCheck orderCheck-credit clearfix">
                <input type="checkbox" class="customInput customInput-checkbox" id="credit" name="" value="" />
                <label  class="customLabel" for="credit">Купить в кредит, от 2 223 <span class="rubl">p</span> в месяц</label>
            </div>
        </div>
    </div>
    <!--/ информация о заказе -->

    <!-- информация о доставке -->
    <div class="orderCol orderCol-r">
        <menu class="orderCol_delivrLst">
            <li class="orderCol_delivrLst_i orderCol_delivrLst_i-act">Самовывоз</li>
            <li class="orderCol_delivrLst_i">Доставка</li>
        </menu>

        <!-- дата доставки -->
        <div class="orderCol_delivrIn clearfix">
            <div class="orderCol_date">15 сентября 2014, воскресенье</div>
            <span class="orderChange">изменить дату</span>
        </div>
        <!--/ дата доставки -->

        <!-- способ доставки -->
        <div class="orderCol_delivrIn orderCol_delivrIn-pl">
            <div class="orderCol_delivrIn_t clearfix">
                <strong>Магазин</strong>

                <span class="orderChange">изменить место</span>
            </div>

            <div class="selShop popupFl">
                <div class="popupFl_clsr"></div>

                <div class="selShop_h">
                    <div class="selShop_tab selShop_tab-act">Магазины в Москве</div>
                    <div class="selShop_tab">Pick point</div>
                </div>

                <div class="selShop_l">
                    <ul class="shopLst">
                        <li class="shopLst_i">
                            <div style="background: red;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>

                        <li class="shopLst_i">
                            <div style="background: green;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>

                        <li class="shopLst_i">
                            <div style="background: grey;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>

                        <li class="shopLst_i">
                            <div style="background: blue;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>

                        <li class="shopLst_i">
                            <div style="background: red;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>

                        <li class="shopLst_i">
                            <div style="background: green;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>

                        <li class="shopLst_i">
                            <div style="background: grey;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>

                        <li class="shopLst_i">
                            <div style="background: blue;" class="shopLst_addrs">
									<span class="shopLst_addrs_tx">
										м. Петровско-Разумовская<br>
										<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
									</span>

                                <span class="shopLst_addrs_tm">с 9.00 до 22.00</span>
                            </div>
                        </li>
                    </ul>

                </div>

                <div class="selShop_r">
                    <img src="/styles/order/img/map.png" alt="" />
                </div>
            </div>

            <div class="orderCol_addrs" style="background: red;">
					<span class="orderCol_addrs_tx">
						м. Петровско-Разумовская<br/>
						<span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
					</span>
            </div>

            <div class="orderCol_tm">
                <span class="orderCol_tm_t">Режим работы:</span> с 9.00 до 22.00
                <span class="orderCol_tm_t">Оплата при получении: </span>

                <img class="orderCol_tm_img" src="/styles/order/img/cash.png" alt="" />
                <img class="orderCol_tm_img" src="/styles/order/img/cards.png" alt="">
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
        <div class="orderCol">
            <div class="orderCol_h">
                <strong>Заказ №1</strong>
                <span class="colorBrightGrey">продавец: ООО «Связной»</span>
            </div>

            <div class="orderCol_cnt clearfix">
                <a href="" class="orderCol_lk">
                    <img class="orderCol_img" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
                </a>

                <a href="" class="orderCol_n">
                    Самокат<br/>
                    JD Bug Classic MS-305 синий
                </a>

                <span class="orderCol_data orderCol_data-summ">5 200 <span class="rubl">p</span></span>
                <span class="orderCol_data orderCol_data-count">1 шт.</span>
                <span class="orderCol_data orderCol_data-price">5 200 <span class="rubl">p</span></span>
            </div>

            <div class="orderCol_f clearfix">
                <div class="orderCol_f_l">
                    <span class="orderCol_f_t brb-dt">Ввести код скидки</span>
                </div>

                <div class="orderCol_f_r">
                    <span class="orderCol_summ">150 <span class="rubl">p</span></span>
                    <span class="orderCol_summt orderCol_summt-m">Доставка:</span>

                    <span class="orderCol_summ">2 334 <span class="rubl">p</span></span>
                    <span class="orderCol_summt">Итого:</span>
                </div>

                <div class="orderCheck orderCheck-credit clearfix">
                    <input type="checkbox" class="customInput customInput-checkbox" id="credit1" name="" value="" />
                    <label  class="customLabel" for="credit1">Купить в кредит, от 2 223 <span class="rubl">p</span> в месяц</label>
                </div>
            </div>
        </div>
        <!--/ информация о заказе -->

        <!-- информация о доставке -->
        <div class="orderCol orderCol-r">
            <menu class="orderCol_delivrLst">
                <li class="orderCol_delivrLst_i orderCol_delivrLst_i-act">Доставка</li>
            </menu>

            <!-- дата доставки -->
            <div class="orderCol_delivrIn orderCol_delivrIn-sel clearfix">
                <div class="orderCol_date">15 сентября 2014, воскресенье</div>

                <div class="customSel">
                    <span class="customSel_def">10:00…18:00</span>

                    <ul class="customSel_lst popupFl" style="display: block;">
                        <li class="customSel_i">10:00…18:00</li>
                        <li class="customSel_i">8:00…12:00</li>
                        <li class="customSel_i">12:00…18:00</li>
                    </ul>
                </div>

                <span class="orderChange">изменить дату</span>
            </div>
            <!--/ дата доставки -->

            <!-- способ доставки -->
            <div class="orderCol_delivrIn orderCol_delivrIn-bg">
                <div class="orderCol_delivrIn_t clearfix">
                    <strong>Адрес</strong> <span class="colorBrightGrey">для всех заказов с доставкой</span>
                </div>

                <div class="orderCol_addrs">
                    <input class="orderCol_addrs_fld textfield" type="text" name="" value="" placeholder="" />
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
        <div class="orderCol">
            <div class="orderCol_h">
                <strong>Заказ №1</strong>
                <span class="colorBrightGrey">продавец: ООО «Связной»</span>
            </div>

            <div class="orderCol_cnt clearfix">
                <a href="" class="orderCol_lk">
                    <img class="orderCol_img" src="http://fs03.enter.ru/1/1/163/4a/108293.jpg" alt="">
                </a>

                <a href="" class="orderCol_n">
                    Самокат<br/>
                    JD Bug Classic MS-305 синий
                </a>

                <span class="orderCol_data orderCol_data-summ">5 200 <span class="rubl">p</span></span>
                <span class="orderCol_data orderCol_data-count">1 шт.</span>
                <span class="orderCol_data orderCol_data-price">5 200 <span class="rubl">p</span></span>
            </div>

            <div class="orderCol_f clearfix">
                <div class="orderCol_f_l">
                    <span class="orderCol_f_t brb-dt">Ввести код скидки</span>
                </div>

                <div class="orderCol_f_r">
                    <span class="orderCol_summ">150 <span class="rubl">p</span></span>
                    <span class="orderCol_summt orderCol_summt-m">Доставка:</span>

                    <span class="orderCol_summ">2 334 <span class="rubl">p</span></span>
                    <span class="orderCol_summt">Итого:</span>
                </div>
            </div>
        </div>
        <!--/ информация о заказе -->

        <!-- информация о доставке -->
        <div class="orderCol orderCol-r">
            <menu class="orderCol_delivrLst">
                <li class="orderCol_delivrLst_i orderCol_delivrLst_i-act">Доставка</li>
            </menu>

            <!-- дата доставки -->
            <div class="orderCol_delivrIn orderCol_delivrIn-sel clearfix">
                <div class="orderCol_date">15 сентября 2014, воскресенье</div>

                <div class="customSel">
                    <span class="customSel_def">10:00…18:00</span>

                    <ul class="customSel_lst popupFl" style="display: block;">
                        <li class="customSel_i">10:00…18:00</li>
                        <li class="customSel_i">8:00…12:00</li>
                        <li class="customSel_i">12:00…18:00</li>
                    </ul>
                </div>

                <span class="orderChange">изменить дату</span>
            </div>
            <!--/ дата доставки -->
            <!-- способ доставки -->
            <div class="orderCol_delivrIn">
                <div class="orderCol_delivrIn_t clearfix">
                    <strong>Адрес</strong> <span class="colorBrightGrey">для всех заказов с доставкой</span>
                    <span class="orderChange">изменить место</span>
                </div>

                <div class="orderCol_addrs">
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
        <div class="orderComment_t">Дополнительные пожелания</div>

        <textarea class="orderComment_fld textarea"></textarea>
    </div>

    <div class="orderCompl clearfix">
        <p class="orderCompl_l">
            <span class="l">Итого <strong>3</strong> заказа на общую сумму <strong>123 000 <span class="rubl">p</span></strong></span>
            <span class="colorBrightGrey dblock">Вы сможете заполнить заявку на кредит и оплатить онлайн на следующем шаге</span>
        </p>

        <button class="orderCompl_btn btnsubmit">Оформить ➜</button>
    </div>
    </section>

    <section class="orderCnt">
        <h1 class="orderCnt_t">Заказы оформлены</h1>

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

<? };
