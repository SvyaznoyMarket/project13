<?php
/**
 * @var $page       \View\User\IndexPage
 * @var $user       \Session\User
 * @var $orderCount int
 */
?>
<div class="personalPage">

    <!-- навигация по личному кабинету -->
    <nav class="personalControl">
        <li class="personalControl_item">
            <a href="" class="personalControl_link personalControl_link-active">Заказы</a>
        </li>

        <li class="personalControl_item">
            <a href="" class="personalControl_link">Личные данные</a>
        </li>

        <li class="personalControl_item">
            <a href="" class="personalControl_link">Подписки e-mail и sms</a>
        </li>

        <li class="personalControl_item personalControl_item-text fl-r td-underl">
            <a href="personalControl_text" class="">cEnter защиты прав потребителей</a>
        </li>
    </nav>
    <!-- /навигация по личному кабинету -->

    <div class="personalTitle">Текущие заказы <span class="personalTitle_count">3</span></div>

    <!-- таблица текущих заказов -->
    <div class="personalTable personalTable-border">
        <div class="personalTable_row personalTable_row-head">
            <div class="personalTable_cell">№ заказа</div>
            <div class="personalTable_cell">Состав</div>
            <div class="personalTable_cell ta-c">Сумма</div>
            <div class="personalTable_cell">Получение</div>
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
                <strong class="s dblock">Покупка в кредит</strong>
                <span class="s dblock">К оплате: <span class="m">434 <span class="rubl">p</span></span></span>
            </div>

            <div class="personalTable_cell">
                Доставка
                <span class="s dblock">11 июл. 2014 9:00…18:00</span>
            </div>

            <div class="personalTable_cell">В обработке</div>

            <div class="personalTable_cell personalTable_cell"><button class="btnLightGrey">Заполнить заявку<br/>на кредит</button></div>
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

    <div class="personalTitle">История</div>

    <!-- таблица истории заказов -->
    <div class="personalTable personalTable-border">
        <div class="personalTable_row personalTable_row-head">
            <div class="personalTable_cell">№ заказа</div>
            <div class="personalTable_cell">Состав</div>
            <div class="personalTable_cell ta-c">Сумма</div>
            <div class="personalTable_cell">Получение</div>
            <div class="personalTable_cell">Статус</div>
            <div class="personalTable_cell"></div>
        </div>
        
        <!-- кликаем по всему диву, что бы раскрыть блок с заказами -->
        <div class="personalTable_rowgroup personalTable_rowgroup-head">
            <div class="personalTable_cell">
                <strong class="textCorner textCorner-open">2014</strong> <span class="colorGrey">4 заказа</span>
            </div>
        </div>
        <!--/ кликаем по всему диву, что бы раскрыть блок с заказами -->

        <div class="personalTable_rowgroup">

            <div class="personalTable_row">
                <div class="personalTable_cell">
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

                <div class="personalTable_cell"><button class="btnLightGrey">Добавить в корзину</button></div>
            </div>

            <!-- ----------------- -->
            
            <!-- отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->
            <div class="personalTable_row personalTable_row-canceled colorGrey">
                <div class="personalTable_cell">
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

                <div class="personalTable_cell"><button class="btnLightGrey">Добавить в корзину</button></div>
            </div>
            <!--/ отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->

            <!-- ----------------- -->

            <div class="personalTable_row">
                <div class="personalTable_cell">
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

                <div class="personalTable_cell"><button class="btnLightGrey">Добавить в корзину</button></div>
            </div>

            <!-- ----------------- -->

            <div class="personalTable_row">
                <div class="personalTable_cell">
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

                <div class="personalTable_cell"><button class="btnLightGrey">Добавить в корзину</button></div>
            </div>
        </div>

        <!-- кликаем по всему диву, что бы раскрыть блок с заказами -->
        <div class="personalTable_rowgroup personalTable_rowgroup-head">
            <div class="personalTable_cell">
                <strong class="textCorner">2014</strong> <span class="colorGrey">4 заказа</span>
            </div>
        </div>
        <!--/ кликаем по всему диву, что бы раскрыть блок с заказами -->
    
        <!-- что бы раскрыть историю заказов необходимо добавить класс display: table-row-group, а не display: block  -->
        <div style="display: none" class="personalTable_rowgroup">

            <div class="personalTable_row">
                <div class="personalTable_cell">
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

                <div class="personalTable_cell"><button class="btnLightGrey">Добавить в корзину</button></div>
            </div>

            <!-- ----------------- -->
            
            <!-- отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->
            <div class="personalTable_row personalTable_row-canceled colorGrey">
                <div class="personalTable_cell">
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

                <div class="personalTable_cell"><button class="btnLightGrey">Добавить в корзину</button></div>
            </div>
            <!--/ отмененный заказ, к ячейком добавляем класс colorGrey, кроме последний двух-->
        </div>
    </div>
    <!--/ таблица истории заказов -->
</div>

<div class="personalPage">
    <div class="personalTitle">
        <a class="td-underl" href="">Текущие заказы</a> <span class="personalTitle_count">3</span>
    </div>

    <div class="personalPage_left">
        <div class="personalPage_head clearfix">
            <h2 class="personalPage_head_left">Заказ COXD-305176 от 06.06.14</h2>

            <div class="personalPage_head_right">К оплате: <span class="orderPrice">42 740 <span class="rubl">p</span></span></div>
        </div>
        
        <!-- статусы заказа -->
        <ul class="personalControl personalControl-arrow">
            <li class="personalControl_item personalControl_item-arrow personalControl_item-active">
                Принят
            </li>

            <li class="personalControl_item personalControl_item-arrow">
                Отправлен
            </li>

            <li class="personalControl_item personalControl_item-last">
                Получен
            </li>
        </ul>
        <!--/ статусы заказа -->
        
        <!-- информация о заказе -->
        <div class="personalInfo">
            <p>Плановый срок получения заказа <mark class="colorBlack">Завтра, 15 июня, воскресенье</mark></p>
            
            <div class="personalTable">
                <div class="personalTable_row">
                    <div class="personalTable_cell">Магазин</div>

                    <div class="personalTable_cell s">
                        <mark class="colorBlack decorColor-bullet" style="color: #b1b4c2"><span class="colorBlack">м. Петровско-Разумовская</span></mark>
                        ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2
                    </div>

                    <div class="personalTable_cell s">
                        <mark class="colorBlack">режим работы</mark><br/>
                        с 9.00 до 22.00
                    </div>

                    <div class="personalTable_cell va-m"><a class="colorBlack">Как добраться?</a></div>
                </div>
            </div>

            <p>В магазине можно оплатить заказ как наличными, так и банковской картой.</p>
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
        <menu class="payCommands">
            <ul class="payCommandsList">
                <li class="payCommandsList_item">
                    <button class="btnPay">Оплатить баллами</button>

                    <span class="descPay">
                        <img src="/styles/personal-page/img/cards/sclub.png" alt="" class="descPay_img" />
                        <img src="/styles/personal-page/img/cards/sber.png" alt="" class="descPay_img" />
                    </span>
                </li>

                <li class="payCommandsList_item">
                    <button class="btnPay">Оплатить онлайн</button>

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
    </aside>
    <!--/ сайдбар онлайн оплаты -->
</div>
