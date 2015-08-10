<?php
/**
 * @var $exception \Exception
 */

$page = new \View\Error\IndexPage();
$helper = new \Helper\TemplateHelper();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Enter.ru</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <link href="/css/global.min.css" rel="stylesheet" type="text/css"/>
    <link href="/styles/global.min.css" rel="stylesheet" type="text/css"/>
    <script src="http://yandex.st/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
    <script src="/js/prod/LAB.min.js" type="text/javascript"></script>
    <script src="/js/prod/html5.min.js" type="text/javascript"></script>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotUserConfig() ?>
</head>
<body class='b404' data-template="page404" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
<?= $page->slotConfig() ?>


<div class="errPage">
    <a class="errPage_lg" href='/'></a>

    <div class="errPage_cnt">
        <div class="errPage_cnt_t">
            <span><?= \App::config()->debug ? $exception->getMessage() : 'Упс! Запрашиваемая вами страница не найдена' ?></span>

            <h2><b>Вы легко можете найти то,<br> что искали!</b></h2>

            <? $productCount = number_format(\App::config()->product['totalCount'], 0, ',', ' ') ?>
            <form class="errPage_f" action="<?= $page->url('search') ?>" method="get" id="searchForm">
                <input id="searchStr" name="q" type='text' class="errPage_tx" value="Поиск среди десятков тысяч товаров<?//= $productCount ?>" onBlur="var field = document.getElementById('searchStr'); if(field.value == ''){field.value = 'Поиск среди десятков тысяч товаров<?//= $productCount ?>'};return false;" onFocus="var field = document.getElementById('searchStr'); if(field.value == 'Поиск среди десятков тысяч товаров<?//= $productCount ?>'){field.value = ''};return false;">
                <a class='bOrangeButton' href onclick="document.getElementById('searchForm').submit(); return false;">Найти</a>
            </form>

            <span>или позвоните нам в&nbsp;Контакт-сENTER <b>+7 (800) 700 00 09</b><br> Звонок бесплатный. Радость в&nbsp;подарок.</span><br><br>
            <a class='bBigOrangeButton' href='/'>Перейти на&nbsp;главную</a>
        </div>

        <div class="errPage_cnt_b">
            <div class="slidew slidew-br1">
            <? if (\App::config()->product['pullRecommendation']): ?>
                <?= $helper->render('product/__slider', [
                    'type'           => 'main',
                    'title'          => 'Мы рекомендуем',
                    'products'       => [],
                    'count'          => null,
                    'limit'          => \App::config()->product['itemsInSlider'],
                    'page'           => 1,
                    'url'            => $page->url('main.recommended', [
                        'class'  => 'slideItem-7item',
                        'sender' => [
                            'position' => '404',
                        ],
                    ]),
                ]) ?>
            <? endif ?>
            </div>
        </div>
    </div>
</div>


<?= $page->slotBodyJavascript() ?>
<?= $page->slotInnerJavascript() ?>
<?= $page->slotPartnerCounter() ?>

<script type="text/javascript">
    var _gaq = _gaq || [];
    var nowURL = document.URL;
    _gaq.push(['_setAccount', 'UA-25485956-1']);
    _gaq.push(['_setDomainName', 'enter.ru']);
    _gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
    _gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
    _gaq.push(['_addOrganic', 'nigma.ru', 's']);
    _gaq.push(['_addOrganic', 'webalta.ru', 'q']);
    _gaq.push(['_addOrganic', 'aport.ru', 'r']);
    _gaq.push(['_addOrganic', 'poisk.ru', 'text']);
    _gaq.push(['_addOrganic', 'km.ru', 'sq']);
    _gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
    _gaq.push(['_addOrganic', 'quintura.ru', 'request']);
    _gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
    _gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
    _gaq.push(['_addOrganic', 'gogo.ru', 'q']);
    _gaq.push(['_addOrganic', 'ru.yahoo.com', 'p']);
    _gaq.push(['_addOrganic', 'images.yandex.ru', 'q', true]);
    _gaq.push(['_addOrganic', 'blogsearch.google.ru', 'q', true]);
    _gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
    _gaq.push(['_addOrganic', 'ru.search.yahoo.com', 'p']);
    _gaq.push(['_addOrganic', 'ya.ru', 'q']);
    _gaq.push(['_addOrganic', 'm.yandex.ru', 'query']);
    _gaq.push(['_trackPageview', '/page404' + location.pathname]);
    _gaq.push(['_trackEvent', 'Errors', '404', nowURL]);
    (function () {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();


    <?php /* Universal Google Analytics  */ ?>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    if ( 'function' === typeof(ga) ) {
        ga('create', 'UA-25485956-5', 'enter.ru');
        ga('send', 'pageview', '/404' + document.location.pathname, {
            'dimension5': '404'
        });
    }
</script>

</body>
</html>
