<?php
/**
 * @var $exception \Exception
 */

$page = new \View\Error\IndexPage();
$helper = new \Helper\TemplateHelper();

?>

<div class="errPage">
    <a class="errPage_lg" href='/'></a>

    <div class="errPage_cnt">
        <div class="errPage_cnt_t">
            <span>Упс! Запрашиваемая вами страница не найдена</span>

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
