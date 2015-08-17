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
    if ( typeof ga == 'function' ) {
        ga('send', 'pageview', '/404' + document.location.pathname, {
            'dimension5': '404'
        });
    }
</script>

</body>
</html>
