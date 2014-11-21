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
        <!-- шапка -->
        <div class="header">
            <!-- топбар -->
            <div class="header_t clearfix">
                <div class="header_i hdcontacts">
                    <a class="hdcontacts_lk undrl" href="">Набережные челны</a>
                    <div class="hdcontacts_phone">+7 (495) 775-00-06</div>
                </div>

                <a class="header_i hdcall" href="">
                    <i class="i-header i-header-phone"></i>
                    <span class="hdcall_tx">Звонок<br/>с сайта</span>
                </a>

                <ul class="header_i hdlk">
                    <li class="hdlk_i"><a href="" class="hdlk_lk undrl">Наши магазины</a></li>
                    <li class="hdlk_i"><a href="" class="hdlk_lk undrl">Доставка</a></li>
                </ul>

                <menu class="header_i userbtn">
                    <li class="userbtn_i userbtn_i-lk">
                        <a class="userbtn_lk" href=""><span class="undrl">Войти</span></a>
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
            <!--/ топбар -->
            
            <!-- поиск -->
            <div class="header_c clearfix">
                <a href="/" class="header_i sitelogo"></a>

                <form action="" class="header_i hdsearch">
                    <label class="hdsearch_lbl" for="">Все товары для жизни по выгоным ценам!</label>
                    <div class="hdsearch_itw"><input type="text" class="hdsearch_it" placeholder="Поиск по товарам..."></div>
                    <button class="hdsearch_btn btn3">Найти</button>
                </form>

                <div class="header_i hdep">
                    <div class="hdep_h">Больше скидок</div>
                    <a href="" class="i-header i-header-ep"></a>
                </div>
            </div>
            <!--/ поиск -->
            
            <!-- навигация -->
            <nav class="header_b">
                <ul class="navsite">
                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">C</div>
                            <span class="navsite_tx">МЕБЕЛЬ</span>
                        </a>
                        
                        <ul class="navsite2">
                            <li class="navsite2_i">
                                <a href="" class="navsite2_lk">Планшетные компьютеры</a>

                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Фото и видео</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>

                                    <li class="navsite3_i">
                                        <div class="navitem">
                                            <div class="navitem_tl">ТОВАР ДНЯ</div>

                                            <a href="" class="navitem_cnt">
                                                <img src="styles/index/img/pic/item.png" alt="" class="navitem_img">
                                                <span class="navitem_n">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span>
                                            </a>

                                            <div class="navitem_pr">
                                                22 990 <span class="rubl">p</span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Ноутбуки и моноблоки</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телефоны</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телевизоры, аудио, видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Фото и видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">A</div>
                            <span class="navsite_tx">ТОВАРЫ ДЛЯ ДОМА</span>
                        </a>
                        
                        <ul class="navsite2">
                            <li class="navsite2_i">
                                <a href="" class="navsite2_lk">Планшетные компьютеры</a>

                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Ноутбуки и моноблоки</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телефоны</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телевизоры, аудио, видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Фото и видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                        
                        <ul class="navsite2">
                            <li class="navsite2_i">
                                <a href="" class="navsite2_lk">Планшетные компьютеры</a>

                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Ноутбуки и моноблоки</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телефоны</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телевизоры, аудио, видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Фото и видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_imgw"><img class="navsite_img" src="styles/index/img/pic/nax_img.png" alt=""></div>
                            <span class="navsite_tx">ЗИМНИЕ ТОВАРЫ</span>
                        </a>
                        
                        <ul class="navsite2">
                            <li class="navsite2_i">
                                <a href="" class="navsite2_lk">Планшетные компьютеры</a>

                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Ноутбуки и моноблоки</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телефоны</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Телевизоры, аудио, видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                            <li class="navsite2_i"><a href="" class="navsite2_lk">Фото и видео</a>
                                <ul class="navsite3">
                                    <li class="navsite3_i navsite3_i-tl">Планшетные компьютеры</li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Планшетные компьютеры</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Ноутбуки и моноблоки</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Телефоны</a></li>
                                    <li class="navsite3_i"><a href="" class="navsite3_lk">Фото и видео</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>

                    <li class="navsite_i">
                        <a href="" class="navsite_lk">
                            <div class="navsite_icon">B</div>
                            <span class="navsite_tx">БЫТОВАЯ ТЕХНИКА</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- навигация -->
        </div>
        <!--/ шапка -->

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
        <div class="footer_t">
            <ul class="footer_cmpn clearfix">                                 
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/about_company">О компании</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="http://feedback.enter.ru/">Напишите нам</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/how_pay">Способы оплаты</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/credit">Покупка в кредит</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="http://my.enter.ru/community/job">Работа у нас</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/shops">Корпоративным клиентам </a></li>
                <li class="footer_cmpn_i footer_cmpn_i-last"><a class="footer_cmpn_lk" href="/how_get_order">ЦСИ</a></li>
            </ul>

            <div class="footer_inf">
                <ul class="footer_inf_lst">
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/legal">Правовая информация</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/terms">Условия продажи</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/media_info">Информация о СМИ</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/refurbished-sale">Уцененные товары оптом</a></li>
                </ul>

                <p class="footer_inf_tx">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</p>
            </div>

            <ul class="footer_socnet">
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.facebook.com/enter.ru"><i class="i-share i-share-fb"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://twitter.com/enter_ru"><i class="i-share i-share-tw"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="http://vk.com/public31456119"><i class="i-share i-share-vk"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.youtube.com/user/EnterLLC"><i class="i-share i-share-yt"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="http://www.odnoklassniki.ru/group/53202890129511"><i class="i-share i-share-od"></i></a></li>
            </ul>

            <ul class="footer_bnnr">
                <li class="footer_bnnr_i"><img src="/styles/footer/img/prava-potreb.gif" /></li>
                <li class="footer_bnnr_i"><a href="/akit"><img src="/styles/footer/img/akita.png" /></a></li>
                <li class="footer_bnnr_i"><div class="teleportator" id="teleportator"></div></li>
            </ul>

            <ul class="footer_app">
                <li class="footer_app_i footer_app_i-t">Мобильные приложения</li>
                <li class="footer_app_i"><a target="_blank" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img class="footer_app_img" src="/styles/footer/img/apple.png" /></a></li>
                
                <li class="footer_app_i">
                    <a target="_blank" href="http://www.windowsphone.com/ru-ru/store/app/enter/6f4c5810-682f-47dc-87b2-aced84582787">
                        <img class="footer_app_img" src="/styles/footer/img/wind.png" />
                    </a>
                </li>

                <li class="footer_app_i">
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=ru.enter">
                      <img class="footer_app_img" alt="Get it on Google Play" src="/styles/footer/img/google.png" />
                    </a>
                </li>
            </ul>
        </div>

        <footer class="footer_b">
            <div class="footer_cpy clearfix">
                <a id="jira" class="footer_cpy_r" href="javascript:void(0)">Сообщить об ошибке</a>
                <div class="footer_cpy_l">&copy; ООО «Энтер» 2011–2014. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
                <div class="footer_cpy_c"><a href="" class="footer_cpy_mbl">Мобильный сайт</a></div>
            </div>
        </footer>
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
