<?php
/**
 * @var $page           \View\Main\IndexPage
 */
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <title><?= $page->getTitle() ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noyaca"/>

    <?= $page->slotMeta() ?>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">

    <meta name="viewport" content="width=1000" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="format-detection" content="telephone=no" />

    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotMetaOg() ?>

</head>

<body class="main">
    <div class="wrapper">
        <div class="header">
            <div class="header_t clearfix">
                <div class="header_i hdcontacts">
                    <a class="hdcontacts_lk" href="">Набережные челны</a>
                    <div class="hdcontacts_phone">+7 (495) 775-00-06</div>
                </div>

                <a class="header_i hdcall" href="">
                    <i class="i-header i-header-phone"></i>
                    <span class="hdcall_tx">Звонок<br/>с сайта</span>
                </a>

                <ul class="header_i hdlk">
                    <li class="hdlk_i"><a href="" class="hdlk_lk">Наши магазины</a></li>
                    <li class="hdlk_i"><a href="" class="hdlk_lk">Доставка</a></li>
                </ul>

                <menu class="header_i userbtn">
                    <li class="userbtn_i userbtn_i-lk">
                        <a class="userbtn_lk" href="">Войти</a>
                    </li>

                    <li class="userbtn_i">
                        <span class="userbtn_lk">
                            <i class="userbtn_icon i-header i-header-compare"></i> 
                            <span class="userbtn_tx">Сравнение</span>
                            <span class="userbtn_count">1</span>
                        </span>
                    </li>
                    
                    <li class="userbtn_i userbtn_i-act userbtn_i-cart">
                        <a class="userbtn_lk userbtn_lk-cart" href="">
                            <i class="userbtn_icon i-header i-header-cart"></i> 
                            <span class="userbtn_tx">Корзина</span>
                            <span class="userbtn_count">2</span>
                        </a>
                    </li>
                </menu>
            </div>

            <div class="header_c clearfix">
                <a href="/" class="header_i sitelogo"></a>

                <form action="" class="header_i hdsearch">
                    <label class="hdsearch_lbl" for="">Все товары для жизни по выгоным ценам!</label>
                    <input type="text" class="hdsearch_it">
                    <button class="hdsearch_btn btn3">Найти</button>
                </form>

                <div class="header_i hdep">
                    <div class="hdep_h">Больше скидок</div>
                    <a href="" class="i-header i-header-ep"></a>
                </div>
            </div>

            <div class="header_b">
                
            </div>
        </div>

        <div class="content">
            <div class="slidesbnnr">
                <ul class="slidesbnnr_lst">
                    <li class="slidesbnnr_i">
                        <a href="" class="slidesbnnr_lk"><img src="styles/index/img/pic/banner.png" alt="" class="slidesbnnr_img"></a>
                    </li>

                    <li class="slidesbnnr_i">
                        <a href="" class="slidesbnnr_lk"><img src="styles/index/img/pic/banner.png" alt="" class="slidesbnnr_img"></a>
                    </li>

                    <li class="slidesbnnr_i">
                        <a href="" class="slidesbnnr_lk"><img src="styles/index/img/pic/banner.png" alt="" class="slidesbnnr_img"></a>
                    </li>
                </ul>

                <ul class="slidesbnnr_thmbs">
                    <li class="slidesbnnr_thmbs_i">
                        <img class="slidesbnnr_thmbs_img slidesbnnr_thmbs_img-act" src="styles/index/img/pic/banner.png" alt="">
                    </li>

                    <li class="slidesbnnr_thmbs_i">
                        <img class="slidesbnnr_thmbs_img" src="styles/index/img/pic/banner.png" alt="">
                    </li>

                    <li class="slidesbnnr_thmbs_i">
                        <img class="slidesbnnr_thmbs_img" src="styles/index/img/pic/banner.png" alt="">
                    </li>

                    <li class="slidesbnnr_thmbs_i">
                        <img class="slidesbnnr_thmbs_img" src="styles/index/img/pic/banner.png" alt="">
                    </li>

                    <li class="slidesbnnr_thmbs_i">
                        <img class="slidesbnnr_thmbs_img" src="styles/index/img/pic/banner.png" alt="">
                    </li>
                </ul>
            </div>

            <ul class="shopInfo clearfix">
                <li class="shopInfo_i">
                    <i class="shopInfo_l i-shopInfo i-shopInfo-delivery"></i>
                    <div class="shopInfo_r">
                        <span class="shopInfo_tl undrl">Доставка</span>
                        <p class="shopInfo_tx">Доставляем по всей России</p>
                    </div>
                </li>
                <li class="shopInfo_i">
                    <i class="shopInfo_l i-shopInfo i-shopInfo-deliveryself"></i>
                    <div class="shopInfo_r">
                        <span class="shopInfo_tl undrl">Самовывоз</span>
                        <p class="shopInfo_tx">Более 1000 пунктов выдачи</p>
                    </div>
                </li>
                <li class="shopInfo_i">
                    <i class="shopInfo_l i-shopInfo i-shopInfo-payment"></i>
                    <div class="shopInfo_r">
                        <span class="shopInfo_tl undrl">Удобно платить</span>
                        <p class="shopInfo_tx">Онлайн, кредит, карты, нал</p>
                    </div>
                </li>
                <li class="shopInfo_i">
                    <i class="shopInfo_l i-shopInfo i-shopInfo-wow"></i>
                    <div class="shopInfo_r">
                        <span class="shopInfo_tl undrl">WOW Акции</span>
                        <p class="shopInfo_tx">Лучшие предложения</p>
                    </div>
                </li>
            </ul>

            <div class="shopInfoTab" style="display: block;">
                <ul class="shopInfoTab_lst">
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery1"></i></div></div>
                        <div class="shopInfoTab_tx">ВЫГОДНЫЕ ЦЕНЫ</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery2"></i></div></div>
                        <div class="shopInfoTab_tx">БЕСПЛАТНАЯ ДОСТАВКА<br/>НА РЯД ТОВАРОВ</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery3"></i></div></div>
                        <div class="shopInfoTab_tx">ДОСТАВКА ПО РОССИИ</div>
                    </li>
                    <li class="shopInfoTab_i">
                       <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery4"></i></div></div>
                        <div class="shopInfoTab_tx">ДОСТАВКА ДО ДВЕРИ<br/>НА ЛЮБОЙ ЭТАЖ</div>
                    </li>
                </ul>

                <a href="" class="shopInfoTab_btn">Подробнее об условиях, стоимости, сроках и интервалах доставки</a>
            </div>

            <div class="shopInfoTab shopInfoTab-v2">
                <ul class="shopInfoTab_lst">
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery1"></i></div></div>
                        <div class="shopInfoTab_tx">САМОВЫВОЗ<br/>ИЗ МАГАЗИНА ENTER</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery2"></i></div></div>
                        <div class="shopInfoTab_tx">РЕЗЕРВ ТОВАРА НА 3 ДНЯ</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery3"></i></div></div>
                        <div class="shopInfoTab_tx">СРОК ДОСТАВКИ<br/>В МАГАЗИН 1-4 ДНЯ</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery4"></i></div></div>
                        <div class="shopInfoTab_tx">ПОЛУЧЕНИЕ ТОВАРА<br/>В ПОСТАМАТАХ PICKPOINT</div>
                    </li>
                </ul>
                <a href="" class="shopInfoTab_btn">Подробнее об условиях, стоимости, сроках и интервалах доставки</a>
            </div>

            <div class="shopInfoTab shopInfoTab-v3">
                <ul class="shopInfoTab_lst">
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment1"></i></div></div>
                        <div class="shopInfoTab_tx">НАЛИЧНЫЕ</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment2"></i></div></div>
                        <div class="shopInfoTab_tx">БАНКОВСКАЯ КАРТА</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment3"></i></div></div>
                        <div class="shopInfoTab_tx">КРЕДИТ</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment4"></i></div></div>
                        <div class="shopInfoTab_tx">PAYPAL</div>
                    </li>
                    <li class="shopInfoTab_i">
                        <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment5"></i></div></div>
                        <div class="shopInfoTab_tx">ОНЛАЙН-БАНК</div>
                    </li>
                </ul>

                <a href="" class="shopInfoTab_btn">Подробнее об этих и других способах оплат</a>
            </div>

            <div class="slidesBox slidesBox-items">
                <div class="slidesBox_h">
                    <div class="slidesBox_btn slidesBox_btn-l"></div>

                    <div class="slidesBox_h_c">
                        <div class="slidesBox_t">ПОПУЛЯРНЫЕ ТОВАРЫ</div>

                        <ul class="slidesBox_dott">
                            <li class="slidesBox_dott_i slidesBox_dott_i-act"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                        </ul>
                    </div>
                    
                    <div class="slidesBox_btn slidesBox_btn-r"></div>
                </div>

                <div class="slidesBox_inn">
                    <ul class="slidesBox_lst clearfix">
                        <li class="slidesBox_i">
                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>

                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>
                        </li>

                        <li class="slidesBox_i">
                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>

                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="slidesBox slidesBox-bg2 slidesBox-items fl-r">
                <div class="slidesBox_h">
                    <div class="slidesBox_btn slidesBox_btn-l"></div>

                    <div class="slidesBox_h_c">
                        <div class="slidesBox_t">МЫ РЕКОМЕНДУЕМ</div>

                        <ul class="slidesBox_dott">
                            <li class="slidesBox_dott_i slidesBox_dott_i-act"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                        </ul>
                    </div>
                    
                    <div class="slidesBox_btn slidesBox_btn-r"></div>
                </div>

                <div class="slidesBox_inn">
                    <ul class="slidesBox_lst clearfix">
                        <li class="slidesBox_i">
                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>

                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>
                        </li>

                        <li class="slidesBox_i">
                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>

                            <div class="item">
                                <a href="" class="item_imgw"><img src="styles/index/img/pic/item.png" class="item_img" /></a>
                                <div class="item_n"><a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a></div>
                                <div class="item_pr">22 990 <span class="rubl">p</span></div>
                                <a class="item_btn btn5" href="">Купить</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="infoBox">
                <div class="infoBox_tl">
                    А ЕЩЕ У НАС ПОКУПАЮТ
                </div>

                <ul class="lstitem lstitem-center lstitem-7i clearfix">
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/item.png" alt="" class="lstitem_img">
                            <span class="lstitem_n">Смартфоны</span>
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/item.png" alt="" class="lstitem_img">
                            <span class="lstitem_n">Смартфоны</span>
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/item.png" alt="" class="lstitem_img">
                            <span class="lstitem_n">Смартфоны</span>
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/item.png" alt="" class="lstitem_img">
                            <span class="lstitem_n">Смартфоны</span>
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/item.png" alt="" class="lstitem_img">
                            <span class="lstitem_n">Смартфоны</span>
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/item.png" alt="" class="lstitem_img">
                            <span class="lstitem_n">Смартфоны</span>
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/item.png" alt="" class="lstitem_img">
                            <span class="lstitem_n">Смартфоны</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="slidesBox slidesBox-bg3 slidesBox-full">
                <div class="slidesBox_h">
                    <div class="slidesBox_btn slidesBox_btn-l"></div>

                    <div class="slidesBox_h_c">
                        <div class="slidesBox_t">Новая ювелирная коллекция</div>

                        <ul class="slidesBox_dott">
                            <li class="slidesBox_dott_i slidesBox_dott_i-act"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                            <li class="slidesBox_dott_i"></li>
                        </ul>
                    </div>
                    
                    <div class="slidesBox_btn slidesBox_btn-r"></div>
                </div>

                <div class="slidesBox_inn">
                    <ul class="slidesBox_lst clearfix">
                        <li class="slidesBox_i">
                            <a href="" class="slidesBox_lk"><img src="styles/index/img/pic/banner2.png" alt="" class="slidesBox_img"></a>

                            <ul class="slidesBox_items">
                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>
                            </ul>

                            <a href="" class="slidesBox_btnview">Посмотреть<br/>коллекцию</a>
                        </li>

                        <li class="slidesBox_i">
                            <a href="" class="slidesBox_lk"><img src="styles/index/img/pic/banner2.png" alt="" class="slidesBox_img"></a>

                            <ul class="slidesBox_items">
                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/index/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>
                            </ul>

                            <a href="" class="slidesBox_btnview">Посмотреть<br/>коллекцию</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="infoBox">
                <div class="infoBox_tl">
                    ПОПУЛЯРНЫЕ БРЕНДЫ
                </div>

                <ul class="lstitem clearfix">
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/index/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                </ul>
            </div>
        </div><!--/ Контент -->
    </div><!--/ Шаблон -->

    <div class="footer">

    </div><!--/ Подвал -->

    <?= $page->slotRegionSelection() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotBodyJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotAdriver() ?>
    <?= $page->slotPartnerCounter() ?>
    <?= $page->slotAdblender() ?>
    <?= $page->slotKissMetrics() ?>

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="yandexMetrika" class="jsanalytics"></div>
    <? endif ?>
</body>
</html>
