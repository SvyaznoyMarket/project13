<?php
/**
 * @var $page          \View\Shop\ShowPage
 * @var $shops         \Model\Shop\Entity[]
 * @var $markers       array
 */
?>

<? if (\App::request()->get('route') == 'tchibo.where_buy') : ?>
    <style>
        .tchibo1 {
            height: 175px;
            padding: 12px 0 0 110px;
            background: url('/css/shopsPage/img/tchibo_top.jpg') no-repeat 0 0;
            margin-bottom: 5px;
        }
        .tchibo1 div {
            font-size: 26px;
            font-weight: bold;
            color: #06275a;
            line-height: 1.2em;
            padding-bottom: 36px;
        }
    </style>
    <div class="tchibo1">
        <div>К сожалению, товары Tchibo не представлены<br>в вашем городе.</div>
        Магазины, в которых можно купить товары Tchibo, указаны на карте.<br>Также вы можете заказать товары Tchibo на сайте и мы бесплатно доставим их вам в любой <a href="http://www.enter.ru/shops">магазин Enter</a>.
    </div>
<? endif; ?>

<? require __DIR__ . '/_map.php' ?>
<div class="pb20"></div>

<? if ((bool)$shops): ?>
    <!-- bMapInfo -->
    <div class='bMapShops__eInfo'>

        <? foreach ($shops as $shop): ?>
            <!-- __eCard -->
            <div class='bShopCard shop_<?=$shop->getRegion()->getId()?>' onclick="window.location='<?= $page->url('shop.show', array('regionToken' => $shop->getRegion()->getToken(), 'shopToken' => $shop->getToken())) ?>'">
                <img class='bShopCard__eIco' src='<?= $shop->getImageUrl(2) ?>' width="162" height="100" />
                <h3 class='bShopCard__eTitle'><?= $shop->getName() ?></h3>
                <? if ($shop->getIsReconstructed()): ?>
                    <p class='bShopCard__eDescription red'>На реконструкции</p>
                <? elseif ($shop->getRegime()) :?>
                    <p class='bShopCard__eDescription'>Работаем <?= $shop->getRegime() ?></p>
                <? endif ?>

                <a href="<?= $page->url('shop.show', ['regionToken' => $shop->getRegion()->getToken(), 'shopToken' => $shop->getToken()]) ?>" class="bShopCard__eView">Подробнее о магазине</a>
            </div>
            <!-- /__eCard -->
        <? endforeach ?>

    </div>
    <!-- /bMapInfo -->

    <div class="clear"></div>
    <br />

    <? if (\App::request()->get('route') == 'tchibo.where_buy') : ?>

        <div style="width: 960px; margin-left: -10px; margin-bottom: 10px; overflow: hidden;">
            <img src="/css/shopsPage/img/tchibo_bottom.jpg" />
        </div>
    <? endif; ?>

<? else: ?>
    <p class="font16">В этом городе пока нет магазинов.</p>

<? endif ?>