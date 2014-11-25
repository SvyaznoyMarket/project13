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

<body class="main" data-template="main" data-debug=<?= $page->json(\App::config()->debug) ?>>

<?= $page->slotConfig() ?>

    <!-- шапка -->
    <div class="header">

        <?= $page->slotTopbar() ?>

        <?= $page->slotSearchBar() ?>

        <?= $page->slotNavigation() ?>

    </div>
    <!--/ шапка -->

    <div class="wrapper">

        <div class="content">

            <?= $page->render('main/_banner2') ?>

            <?= $page->render('main/_infoBlocks') ?>

            <?= $page->render('main/_slidesBox', ['class' => 'slidesBox slidesBox-items']) ?>

            <?= $page->render('main/_slidesBox', ['class' => 'slidesBox slidesBox-bg2 slidesBox-items fl-r']) ?>

            <?= $page->render('main/infoBox') ?>

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
                            <a href="" class="slidesBox_lk"><img src="styles/mainpage/img/pic/banner2.png" alt="" class="slidesBox_img"></a>

                            <ul class="slidesBox_items">
                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>
                            </ul>

                            <a href="" class="slidesBox_btnview">Посмотреть<br/>коллекцию</a>
                        </li>

                        <li class="slidesBox_i">
                            <a href="" class="slidesBox_lk"><img src="styles/mainpage/img/pic/banner2.png" alt="" class="slidesBox_img"></a>

                            <ul class="slidesBox_items">
                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
                                </li>

                                <li class="slidesBox_items_i">
                                    <a href="" class="slidesBox_items_lk"><img src="styles/mainpage/img/pic/item.png" alt="" class="slidesBox_items_img"></a>
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
                            <img src="styles/mainpage/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand2.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                    <li class="lstitem_i">
                        <a class="lstitem_lk" href="">
                            <img src="styles/mainpage/img/pic/brand3.jpg" alt="" class="lstitem_img">
                        </a>
                    </li>
                </ul>
            </div>
        </div><!--/ Контент -->
    </div><!--/ Шаблон -->

    <div class="footer">
        <div class="footer_t clearfix">
            <ul class="footer_cmpn clearfix">
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/about_company">О компании</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="http://feedback.enter.ru/">Напишите нам</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/how_pay">Способы оплаты</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/credit">Покупка в кредит</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="http://my.enter.ru/community/job">Работа у нас</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/b2b">Корпоративным клиентам</a></li>
                <li class="footer_cmpn_i footer_cmpn_i-last"><a class="footer_cmpn_lk" href="/research">ЦСИ</a></li>
            </ul>

            <div class="footer_inf">
                <ul class="footer_inf_lst">
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/legal">Правовая информация</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/terms">Условия продажи</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/media_info">Информация о СМИ</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/refurbished-sale">Уцененные товары оптом</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/adv">Рекламные возможности</a></li>
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
                <li class="footer_app_i"><a target="_blank" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img class="footer_app_img" src="/styles/footer/img/apple.png" /></a></li>

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

        <!-- krible.ru Teleportator -->
        <script type="text/javascript">
        var kribleCode = '5e14662e854af6384a9a84af28874dd8';
        var kribleTeleportParam = {'text': '#ffffff', 'button': '#f99b1c', 'link':'#000000'};
        (function (d, w) {
            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function() {
                    n.parentNode.insertBefore(s, n);
                };
            s.type = "text/javascript";
            s.async = true;
            s.src = 'http://chat.krible.ru/arena/'+
              kribleCode.substr(0,2)+'/'+kribleCode+'/teleport.js';
            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f);
            } else {
                f();
            }
        })(document, window);
        </script>
        <!-- /krible.ru Teleportator end -->
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
