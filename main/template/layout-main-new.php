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

    <?= $page->render('common/_footer-new') ?>

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
