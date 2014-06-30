<?php
/**
 * @var $page       \View\User\IndexPage
 * @var $user       \Session\User
 * @var $orderCount int
 */
?>
<div class="personalPage">

    <!-- навигация по личному кабинету -->
    <nav class="personalControl personalControl-links">
        <li class="personalControl_item personalControl_item-active">
            <a href="" class="personalControl_link">Заказы</a>
        </li>

        <li class="personalControl_item">
            <a href="" class="personalControl_link">Личные данные и пароль</a>
        </li>

        <li class="personalControl_item">
            <a href="" class="personalControl_link">Подписки e-mail и sms</a>
        </li>

        <li class="personalControl_item personalControl_item-text fl-r">
            <a href="http://my.enter.ru/community/pravo?offset=0&count=5&solved=0" class="td-underl">cEnter защиты прав потребителей</a>
        </li>
    </nav>
    <!-- /навигация по личному кабинету -->

    <div class="personalTitle">Текущие заказы <span class="personalTitle_count">3</span></div>

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
                46 740 <span class="rubl">p</span>
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
                46 740 <span class="rubl">p</span>
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
                46 740 <span class="rubl">p</span>
                <strong class="s dblock">Покупка в кредит</strong>
                <span class="s dblock">К оплате: <span class="m">434 <span class="rubl">p</span></span></span>
            </div>

            <div class="personalTable_cell">
                Доставка
                <span class="s dblock">11 июл. 2014 9:00…18:00</span>
            </div>

            <div class="personalTable_cell">В обработке</div>

            <div class="personalTable_cell personalTable_cell-last personalTable_cell-mark ta-r">
                <button class="tableBtn btnLightGrey">Заполнить заявку<br/>на кредит</button>
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
                46 740 <span class="rubl">p</span>
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

    <div class="personalTitle">История <span class="personalTitle_count">3</span></div>

    <div class="personalTableWrap">
        <!-- таблица истории заказов -->
        <div class="personalTable personalTable-border personalTable-bg">
            <div class="personalTable_row personalTable_row-head">
                <div class="personalTable_cell personalTable_cell-w90">№ заказа</div>

                <div class="personalTable_cell personalTable_cell-w212">Состав</div>

                <div class="personalTable_cell personalTable_cell-w115 ta-c">Сумма</div>

                <div class="personalTable_cell personalTable_cell-w175">Получение</div>

                <div class="personalTable_cell">Статус</div>

                <div class="personalTable_cell"></div>
            </div>
            
            <!-- кликаем по всему диву, что бы раскрыть блок с заказами -->
            <div class="personalTable_rowgroup personalTable_rowgroup-head">
                <div class="personalTable_cell">
                    <div class="personalTable_cell_rowspan">
                        <strong class="textCorner textCorner-open">2014</strong> <span class="colorGrey">4 заказа</span>
                    </div>
                </div>
            </div>
            <!--/ кликаем по всему диву, что бы раскрыть блок с заказами -->

            <div class="personalTable_rowgroup">

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
                        46 740 <span class="rubl">p</span><br/>
                        <span class="textStatus">оплачено</span>
                    </div>

                    <div class="personalTable_cell">Доставка</div>

                    <div class="personalTable_cell">Получен</div>

                    <div class="personalTable_cell personalTable_cell-last ta-r">
                        <button class="tableBtn btnLightGrey">Добавить в корзину</button>
                    </div>
                </div>

                <!-- ----------------- -->
                
                <!-- отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->
                <div class="personalTable_row personalTable_row-canceled colorGrey">
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
                        46 740 <span class="rubl">p</span><br/>
                        <span class="textStatus">оплачено</span>
                    </div>

                    <div class="personalTable_cell">Доставка</div>

                    <div class="personalTable_cell">Отменён</div>

                    <div class="personalTable_cell personalTable_cell-last ta-r">
                        <button class="tableBtn btnLightGrey">Добавить в корзину</button>
                    </div>
                </div>
                <!--/ отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->

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
                        46 740 <span class="rubl">p</span><br/>
                        <span class="textStatus">оплачено</span>
                    </div>

                    <div class="personalTable_cell">Доставка</div>

                    <div class="personalTable_cell">Получен</div>

                    <div class="personalTable_cell personalTable_cell-last ta-r">
                        <button class="tableBtn btnLightGrey">Добавить в корзину</button>
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
                        46 740 <span class="rubl">p</span><br/>
                        <span class="textStatus">оплачено</span>
                    </div>

                    <div class="personalTable_cell">Доставка</div>

                    <div class="personalTable_cell">Получен</div>

                    <div class="personalTable_cell personalTable_cell-last ta-r">
                        <button class="tableBtn btnLightGrey">Добавить в корзину</button>
                    </div>
                </div>
            </div>

            <!-- кликаем по всему диву, что бы раскрыть блок с заказами -->
            <div class="personalTable_rowgroup personalTable_rowgroup-head">
                <div class="personalTable_cell">
                    <div class="personalTable_cell_rowspan">
                        <strong class="textCorner textCorner">2014</strong> <span class="colorGrey">4 заказа</span>
                    </div>
                </div>
            </div>
            <!--/ кликаем по всему диву, что бы раскрыть блок с заказами -->
        
            <!-- что бы раскрыть историю заказов необходимо добавить класс display: table-row-group, а не display: block  -->
            <div style="display: none" class="personalTable_rowgroup">

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
                        46 740 <span class="rubl">p</span><br/>
                        <span class="textStatus">оплачено</span>
                    </div>

                    <div class="personalTable_cell">Доставка</div>

                    <div class="personalTable_cell">Получен</div>

                    <div class="personalTable_cell personalTable_cell-last ta-r">
                        <button class="tableBtn btnLightGrey">Добавить в корзину</button>
                    </div>
                </div>

                <!-- ----------------- -->
                
                <!-- отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->
                <div class="personalTable_row personalTable_row-canceled colorGrey">
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
                        46 740 <span class="rubl">p</span><br/>
                        <span class="textStatus">оплачено</span>
                    </div>

                    <div class="personalTable_cell">Доставка</div>

                    <div class="personalTable_cell">Отменён</div>

                    <div class="personalTable_cell personalTable_cell-last ta-r">
                        <button class="tableBtn btnLightGrey">Добавить в корзину</button>
                    </div>
                </div>
                <!--/ отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->
            </div>
        </div>
        <!--/ таблица истории заказов -->
    </div>
</div>

<div class="personalPage">
    <div class="personalTitle">
        <a class="td-underl" href="">Текущие заказы</a> <span class="personalTitle_count">3</span>
    </div>

    <div class="personalPage_left">
        <div class="personalPage_head clearfix">
            <h2 class="personalPage_head_left">
                Заказ COXD-305176 от 06.06.14 
                <span class="data">от 06 июл. 2014</span>
            </h2>
            
            <div class="personalPage_head_right">
                Получить номер заказа: 
                <button class="personalPage_head_btn btnLightGrey va-m">SMS</button>
                <button class="personalPage_head_btn btnLightGrey va-m">e-mail</button>
            </div>
        </div>
        
        <!-- статусы заказа -->
        <ul class="personalControl personalControl-arrow">
            <li class="personalControl_item personalControl_item-arrow personalControl_item-past">
                Принят
            </li>

            <li class="personalControl_item personalControl_item-arrow personalControl_item-active">
                Отправлен
            </li>

            <li class="personalControl_item personalControl_item-last">
                Получен
            </li>
        </ul>
        <!--/ статусы заказа -->
        
        <!-- информация о заказе -->
        <div class="personalInfo">
            <p><strong>Самовывоз</strong> <mark class="colorBlack">завтра, 15 июня 2014, воскресенье</mark></p>
            
            <div class="personalTable">
                <div class="personalTable_row">
                    <div class="personalTable_cell w90">из магазина</div>

                    <div class="personalTable_cell">
                        <mark class="decorColor-bullet" style="color: #b1b4c2"><span class="colorBlack">м. Петровско-Разумовская</span></mark>

                        <div class="shopsInfo">
                            <span class="colorBrightGrey">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>

                            <div class="shopsInfo_time">
                                <span class="colorBrightGrey">Режим работы:</span> с 9.00 до 22.00 &nbsp;
                                <span class="colorBrightGrey">Оплата при получении: </span>
                                <img src="/styles/personal-page/img/nal.png" alt=""/>
                                <img src="/styles/personal-page/img/card.png" alt=""/>
                            </div>
                        </div>
                    </div>

                    <div class="personalTable_cell va-m">
                        <a href="td-underl" title="">Как добраться?</a>
                    </div>
                </div>
            </div>
        </div>
        <!--/ информация о заказе -->

        <div class="personalTable personalTable-border nomargin">
            <div class="personalTable_row">
                <div class="personalTable_cell personalTable_cell-mini">
                    <img class="imgProd" src="http://fs10.enter.ru/1/1/120/4b/260016.jpg" alt="" />
                </div>

                <div class="personalTable_cell">
                    Бумажный конструктор<br/> 
                    Jazwares Minecraft Papercraft «Дружелюбные мобы»
                </div>

                <div class="personalTable_cell l colorGrey ta-r">
                    200 <span class="rubl">p</span><br/>
                </div>

                <div class="personalTable_cell l colorGrey ta-r">2 шт.</div>

                <div class="personalTable_cell l ta-r">
                    400 <span class="rubl">p</span><br/>
                </div>
            </div>

            <div class="personalTable_row">
                <div class="personalTable_cell personalTable_cell-mini">
                    <img class="imgProd" src="http://fs10.enter.ru/1/1/120/4b/260016.jpg" alt="" />
                </div>

                <div class="personalTable_cell">
                    Бумажный конструктор<br/> 
                    Jazwares Minecraft Papercraft «Дружелюбные мобы»
                </div>

                <div class="personalTable_cell l colorGrey ta-r">
                    1 200 <span class="rubl">p</span><br/>
                </div>

                <div class="personalTable_cell l colorGrey ta-r">2 шт.</div>

                <div class="personalTable_cell l ta-r">
                    400 <span class="rubl">p</span><br/>
                </div>
            </div>

            <div class="personalTable_row">
                <div class="personalTable_cell personalTable_cell-mini">
                    <img class="imgProd" src="http://fs10.enter.ru/1/1/120/4b/260016.jpg" alt="" />
                </div>

                <div class="personalTable_cell">
                    Бумажный конструктор<br/> 
                    Jazwares Minecraft Papercraft «Дружелюбные мобы»
                </div>

                <div class="personalTable_cell l colorGrey ta-r">
                    12 200 <span class="rubl">p</span><br/>
                </div>

                <div class="personalTable_cell l colorGrey ta-r">2 шт.</div>

                <div class="personalTable_cell personalTable_cell-l ta-r">
                    400 <span class="rubl">p</span><br/>
                </div>
            </div>
        </div>

        <div class="personalTable personalTable-border">
            <div class="personalTable_caption">
                Скидки
            </div>

            <div class="personalTable_row">
                <div class="personalTable_cell personalTable_cell-mini">
                    <img class="imgProd" src="/styles/personal-page/img/enterLogo.png" alt="" />
                </div>

                <div class="personalTable_cell">
                    Фишка со скидкой 2% на категорию Электроника<br/> 
                    Минимальная сумма заказа 6999 руб
                </div>

                <div class="personalTable_cell personalTable_cell-right l colorRed ta-r">
                    - 400 <span class="rubl">p</span><br/>
                </div>
            </div>

            <div class="personalTable_row">
                <div class="personalTable_cell personalTable_cell-mini">
                    <img class="imgProd" src="/styles/personal-page/img/fishka.png" alt="" />
                </div>

                <div class="personalTable_cell">
                    Подарочный сертификат 5000 руб
                </div>

                <div class="personalTable_cell personalTable_cell-right l colorRed ta-r">
                    - 2 400 <span class="rubl">p</span><br/>
                </div>
            </div>

            <div class="personalTable_rowgroup">
                <div class="personalTable_row personalTable_row-total ta-r">
                    <div class="personalTable_cell">
                    </div>

                    <div class="personalTable_cell personalTable_cell-long">
                        Самовывоз:
                    </div>

                    <div class="personalTable_cell">
                        Бесплатно
                    </div>
                </div>

                <div class="personalTable_row personalTable_row-total ta-r">
                    <div class="personalTable_cell">
                    </div>

                    <div class="personalTable_cell personalTable_cell-long">
                        Итого:
                    </div>

                    <div class="personalTable_cell l">
                        <span class="colorGrey td-lineth">47 580 <span class="rubl">p</span></span><br/>
                        42 740 <span class="rubl">p</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- сайдбар онлайн оплаты -->
    <aside class="personalPage_right">

    <!--
        <ul class="paySumm">
            <li>Сумма заказа: <span class="paySumm_val">42 740 <span class="rubl">p</span></span></li>
            <li>Оплачено: <span class="paySumm_val">42 740 <span class="rubl">p</span></span></li>
            <li>К оплате: <span class="paySumm_val">42 740 <span class="rubl">p</span></span></li>
        </ul>

        <menu class="payCommands">
            <ul class="payCommandsList">
                <li class="payCommandsList_item mb20">
                    <button class="btnPay btnLightGrey">Оплатить баллами</button>

                    <span class="descPay">
                        <img src="/styles/personal-page/img/cards/sclub.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/sber.png" alt="" class="descPay_img" />
                    </span>
                </li>

                <li class="payCommandsList_item">
                    <button class="btnPay btnLightGrey">Оплатить онлайн</button>

                    <span class="descPay">
                        <img src="/styles/personal-page/img/cards/MasterCard.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/Visa.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/Maestro.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/paypal.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/psb.png" alt="" class="descPay_img" />
                    </span>
                </li>
            </ul>
        </menu>
    -->

    <!--

        <ul class="paySumm">
            <li>Сумма заказа: <span class="paySumm_val">42 740 <span class="rubl">p</span></span></li>
            <li>К оплате: <span class="paySumm_val">42 740 <span class="rubl">p</span></span></li>
        </ul>

        <menu class="payCommands">
            <ul class="payCommandsList payCommandsList-mark">
                <li class="payCommandsList_item">
                    <div class="titlePay">Кредит</div>

                    <button class="btnPay btnLightGrey">Заполнить заявку</button>

                    <span class="descPay">
                        <img src="/styles/personal-page/img/cards/renesans.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/tinkoff.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/otpBank.png" alt="" class="descPay_img" />
                    </span>
                </li>
            </ul>
        </menu>
    
    -->


        <ul class="paySumm">
            <li>Сумма заказа: <span class="paySumm_val">42 740 <span class="rubl">p</span></span></li>
        </ul>

        <div class="payComplete"></div>
    </aside>
    <!--/ сайдбар онлайн оплаты -->
</div>


<div class="personalPage">
    <div class="personalTitle">Изменить мои данные</div>

        <form action="" class="personalData">
            <fieldset class="personalData_left">
                <legend class="legend">Личные данные</legend>
            
                <label class="personalData_label labeltext">Имя:</label>
                <input class="personalData_text textfield" type="text" />

                <label class="personalData_label labeltext">Отчество:</label>
                <input class="personalData_text textfield" type="text" />

                <label class="personalData_label labeltext">Фамилия:</label>
                <input class="personalData_text textfield" type="text" />

                <div class="personalData_col">
                    <label class="personalData_label labeltext">Дата рождения:</label>

                    <select name="day">
                        <option value="">1</option>
                        <option value="">2</option>
                    </select>

                    <select name="month">
                        <option value="">июнь</option>
                        <option value="">июль</option>
                    </select>

                    <select name="years">
                        <option value="">2014</option>
                        <option value="">2013</option>
                    </select>
                </div>

                <div class="personalData_col">
                    <label class="personalData_label labeltext">Пол:</label>

                    <select name="years">
                        <option value="">мужской</option>
                        <option value="">женский</option>
                    </select>
                </div>

                <div class="personalData_warn">
                    <div class="personalData_warn_text">
                        Одно из полей обязательно для заполнения!
                    </div>
                </div>

                <label class="personalData_label labeltext">E-mail:</label>
                <input class="personalData_text textfield" type="email" />

                <label class="personalData_label labeltext">Мобильный телефон:</label>
                <input class="personalData_text textfield" type="text" />

                <label class="personalData_label labeltext">Домашний телефон:</label>
                <input class="personalData_text textfield" type="text" />

                <label class="personalData_label labeltext">Номер карты "Связной-Клуб":</label>
                <input class="personalData_text textfield" type="text" />

                <label class="personalData_label labeltext">Род деятельности:</label>
                <input class="personalData_text textfield" type="text" />
            </fieldset>

        <fieldset class="personalData_right">
            <legend class="legend">Пароль</legend>

            <p style="xs">Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные буквы, цифры или символы, но не должен включать широко распространенные слова и имена.</p>
            <label class="labeltext">Старый пароль:</label>
            <input class="textfield personalData_text"></input>

            <label class="labeltext">Новый пароль:</label>
            <input class="textfield personalData_text"></input>

            <p style="xs">Внимание! После смены пароля Вам придет письмо и SMS с новым паролем</p>
        </fieldset>
        
        <fieldset class="personalData_clear">
            <input class="btnsubmit" type="submit" value="Сохранить изменения" />
        </fieldset>
    </form>
</div>


<div class="personalPage">
    <div class="personalTitle">Подписки</div>

    <form action="" class="personalSubscr">
        <fieldset class="personalSubscr_row">
            <legend class="legend">Email</legend>

            <input class="jsCustomRadio customInput customInput-bigCheck" id="email" type="checkbox"  name="" checked />
            <label class="customLabel customLabel-bigCheck" for="email">Акции, новости и специальные предложения </label>

        </fieldset>

        <fieldset class="personalSubscr_row">
            <legend class="legend">SMS</legend>

            <input class="jsCustomRadio customInput customInput-bigCheck" id="sms" type="checkbox" name="" />
            <label class="customLabel customLabel-bigCheck" for="sms">Акции, новости и специальные предложения </label>

        </fieldset>

        <fieldset class="personalSubscr_clear">
            <input class="btnsubmit" type="submit" value="Сохранить" />
        </fieldset>
    </form>
</div>



