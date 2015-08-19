<?php

$f = function(
    \Helper\TemplateHelper $helper
) {


?>

<style>
    .footer__main {
        height: 518px;
        margin: -518px auto 0;
    }
    .content.mContentMain {
        padding-bottom: 528px;
    }
    #mainPageFooter .bIndexLinks2__eLinks a{
        width: 205px;

    }
    #mainPageFooter .vcardtitle {
        font-size: 16px;
        padding-bottom: 4px;
    }
    #mainPageFooter .address {
        font: 14px Tahoma,sans-serif;
    }
    #mainPageFooter .bIndexLinks2__eBan {
        height: 120px;
        width: 220px;
    }
    #mainPageFooter .bIndexLinks2__mBan {
        width: 232px;
        margin: 30px 0 0;
        text-align: center;
    }
    #mainPageFooter .bIndexLinks2__eLinks {
        margin: 25px 0px 15px;
        position: relative;
    }
    #mainPageFooter .bIndexLinks2__mBanLast {
        margin-right: 0;
        margin-top: 20px;
    }
    .bB2bSec {
        width: 450px;
        margin: 0;
        color: #fff;
        font-size: 16px;
        float: left;
        font-family: "Enter Type";
        line-height: 1.3;
        cursor: pointer;
    }
    .bB2bSec a {
        text-decoration: underline;
        color: #fff;
    }
    .bB2bSec .bB2bSecBlue {
        color: #49c7ed;
        font-size: 28px;
        text-transform: uppercase;
        color: #49c7ed;
    }
    .bMobAppLink {
        display: block;
        float: left;
        margin-right: 15px;
    }
    #mainPageFooter .vcard {
        font-size: 20px;
    }

    #mainPageFooter .social_network a[title="odnoklassniki"] {
        cursor: pointer;
        display: inline-block;
        height: 26px;
        margin: 0 2px;
        vertical-align: middle;
        width: 26px;
        background: url("http://content.enter.ru/wp-content/uploads/2014/07/odnoklass.png") no-repeat 0 0;
    }

    #mainPageFooter .social_network a[title="odnoklassniki"]:hover {
        background-position: 0 100%;
    }
</style>
<div id="mainPageFooter">

    <? if (\App::config()->partners['alexa']['enabled']) : ?>

    <!-- Start Alexa Certify Javascript -->
    <script type="text/javascript">
        _atrk_opts = { atrk_acct:"mPO9i1acVE000x", domain:"enter.ru",dynamic: true};
        (function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
    </script>
    <noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=mPO9i1acVE000x" style="display:none" height="1" width="1" alt="" /></noscript>
    <!-- End Alexa Certify Javascript -->

    <? endif ?>

    <!--div style="text-align: left; margin-top: 30px; margin-left: 95px;" class="bIndexLinks2__eBan bIndexLinks2__mBan bIndexLinks2__mBanLast"><a href="/how_choose_product"><img src="http://content.enter.ru/wp-content/uploads/2013/12/howchoose.png" /></a></div-->

    <div style="text-align: left; margin-top: 30px; margin-left: 95px;" class="bIndexLinks2__eBan bIndexLinks2__mBan bIndexLinks2__mBanLast"><a href="/life_gift2014"><img src="http://content.enter.ru/wp-content/uploads/2014/11/first.jpg" /></a></div>

    <div class="bIndexLinks2__eBan bIndexLinks2__mBan" style="margin-right: 15px;"><a style="padding-left: 21px;" href="/take_credit"><img src='http://content.enter.ru/wp-content/uploads/2013/10/imgCreditFooter1.jpg'></a></div>

    <div class="bIndexLinks2__eBan bIndexLinks2__mBan bIndexLinks2__mBanLast"><a href="http://www.enter.ru/special_offers"><img src="http://content.enter.ru/wp-content/uploads/2014/10/box_wow_NY.jpg" /></a></div>

    <div style="margin-bottom: 35px"><a href="/enterprize?from_footer_main"><img src="http://content.enter.ru/wp-content/uploads/2014/06/dlinniy_banner1.png" /></a></div>

    <div class="clear"></div>

    <div class="slidew slidew-main">
        <? if (\App::config()->product['pullRecommendation']): ?>
            <?= (new \Helper\TemplateHelper())->render('product/__slider', [
                'type'           => 'main',
                'title'          => 'Популярные товары',
                'products'       => [],
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'url'            => $helper->url('main.recommended', [
                    'namePosition'   => 'top',
                    'class'          => 'slideItem-main',
                    'sender'         => [ // TODO: вынести на уровень выше и переименовать в senders
                        'position' => 'Main',
                    ],
                ]),
            ]) ?>
        <? endif ?>
    </div>

    <div onClick="window.location.href='/b2b'" class="bB2bSec">
        <div class="bB2bSecBlue">Корпоративным клиентам</div>
        Специальные условия на десятки тысяч товаров.<br/>
        <a href="/b2b">Специальные условия</a> сотрудничества для наших партнеров.
    </div>

    <div class="bB2bSec" style="margin-left: 35px;">
        <div class="bB2bSecBlue">Мобильные приложения</div>
        <a class="bMobAppLink" target="_blank" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img src="http://content.enter.ru/wp-content/uploads/2013/10/apple-21.png" style="-webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px;" /></a>

        <a class="bMobAppLink" target="_blank" href="https://play.google.com/store/apps/details?id=ru.enter"><img src="http://content.enter.ru/wp-content/uploads/2013/10/google-2.png"></a>

        <a class="bMobAppLink" style="border: 1px solid #fff; border-radius: 4px; margin-right: 0;" target="_blank" href="http://www.windowsphone.com/ru-ru/store/app/enter/6f4c5810-682f-47dc-87b2-aced84582787"><img style="border-radius: 8px; display: block; height: 38px;" src="http://content.enter.ru/wp-content/uploads/2013/10/thumb_40547.png"></a>
    </div>
    <div class="clear"></div>

    <div class="bIndexLinks2__eLinks"><a href="/about_company">О компании</a><a href="/service_ha">Подключение и сборка </a><a href="/credit">Покупка в кредит</a><a href="http://my.enter.ru/community/job#gsc.tab=0" target="_blank">Работать у нас</a><a href="/shops">Наши магазины</a><a href="/how_make_order">Как сделать заказ</a><a href="/mobile_apps">Мобильные приложения</a></div>
    <div class="copy clearfix">
        <div class="clearfix">
            <div class="pb20 fl" style="">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.<br />
                <a href="/legal">Правовая информация</a><a href="/terms">Условия продажи</a><a href="/media_info">Информация о СМИ</a><a href="/private/">Личный кабинет</a><a href="/research">Наш Центр Стратегических Исследований</a><br /><a href="/refurbished-sale">Уцененные товары оптом</a><a href="/b2b">Для юридических лиц</a></div>

            <div id="teleportator" style="float: right;"></div>

        </div>
        <br />
        <noindex><span class="rights fl"> &copy; ООО «Энтер» 2011&ndash;2015<!--?php echo date("Y") ?-->. <span style="font-size: 11px;"> ENTER<sup>&reg;</sup> ЕНТЕР<sup>&reg;</sup> Enter<sup>&reg;</sup>.</span> Все права защищены. <img src="http://content.enter.ru/wp-content/uploads/2013/03/18-e1362236103519.png"></span></noindex>
        <a href="/akit"><img src="http://content.enter.ru/wp-content/uploads/2012/09/image001.png" class="fr" style="margin: -25px 0px 0px;"></a>
        <div class="social_network" style="margin: 0 50px; width: 230px;"><span class="gray font11">Посетите нас</span> <a target="_blank" title="twitter" href="http://twitter.com/#!/enter_ru"></a><a target="_blank" title="facebook" href="http://www.facebook.com/enter.ru"></a><a target="_blank" title="vkontakte" href="http://vkontakte.ru/public31456119"></a><a target="_blank" title="youtube" href="https://www.youtube.com/user/EnterLLC"> <a target="_blank" title="odnoklassniki" href="http://www.odnoklassniki.ru/group/53202890129511"></a>
        </div>

    </div>
</div>


<? }; return $f;