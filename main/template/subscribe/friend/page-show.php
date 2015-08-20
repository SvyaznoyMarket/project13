<?php
/**
 * @var $page                   \View\DefaultLayout
 * @var $alreadySubscribed      bool Уже подписан
 * @var $successfullySubscribed bool Успешно подписан
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<? if ($alreadySubscribed): ?>
    <div class="subscribe-block subscribe-block2" data-type="background" data-speed="10">
        <div class="scrolled-bg"></div>
        <div class="subscribe-content">
            <?= $helper->render('subscribe/friend/__alreadySubscribed') ?>
        </div>
    </div>
<? elseif ($successfullySubscribed): ?>
    <div class="subscribe-block subscribe-block3" data-type="background" data-speed="10">
        <div class="scrolled-bg"></div>
        <div class="subscribe-content">
            <?= $helper->render('subscribe/friend/__successfullySubscribed') ?>
        </div>
    </div>
<? else: ?>
    <div class="subscribe-page">
        <div class="subscribe-block subscribe-block1" data-type="background" data-speed="10">
            <div class="scrolled-bg"></div>
            <div class="subscribe-content">
                <div class="subscribe-text">
                    <span class="subscribe-text__big">Подпишитесь и получите<span class="absolute">в подарок</span></span>
                    <div class="subscribe-text__img"><img src="http://content.enter.ru/wp-content/uploads/2015/04/300rub.png"></div>
                </div>
                <?= $helper->render('subscribe/friend/__form') ?>
            </div>
        </div>
    </div>
<? endif ?>

<div class="subscribe-block subscribe-block4">
    <div class="subscribe-content">
        <div class="subscribe-letter">
            <div class="subscribe-letter__header">В каждом письме</div>
            <ul class="subscribe-letter-list">
                <li class="subscribe-letter-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/percent.png">
                    <span class="subscribe-letter-list-item__text">Получайте специальные купоны<br/> и скидки только для подписчиков</span>
                </li>
                <li class="subscribe-letter-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/gift.png">
                    <span class="subscribe-letter-list-item__text">Будьте в курсе актуальных<br/> акций и предложений</span>
                </li>
                <li class="subscribe-letter-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/man.png">
                    <span class="subscribe-letter-list-item__text">Узнайте первым, когда начнется<br/> следующая распродажа</span>
                </li>
                <li class="subscribe-letter-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/fishka.png">
                    <span class="subscribe-letter-list-item__text">Не пропустите специальную<br/> фишку Enter Prize 24 часа</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="subscribe-block subscribe-block5">
    <div class="subscribe-content">
        <div class="subscribe-advantages">
            <ul class="subscribe-advantages-list">
                <li class="subscribe-advantages-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/clock.png">
                    <span class="subscribe-advantages-list-item__text">На связи 24/7<br/> Звонок бесплатный<br/><b>+7 (495) 775-00-06</b></span>
                </li>
                <li class="subscribe-advantages-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/phone.png">
                    <span class="subscribe-advantages-list-item__text">Широкий выбор<br/> товаров на сайте<br/> и в приложении</span>
                </li>
                <li class="subscribe-advantages-list-item">
                    <img src="/styles/subscribe/img/self.png">
                    <span class="subscribe-advantages-list-item__text">Бесплатная доставка<br/> в магазины Enter</span>
                </li>
                <li class="subscribe-advantages-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/shield.png">
                    <span class="subscribe-advantages-list-item__text">Безопасные покупки<br/> на сайте</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="subscribe-block subscribe-block6" data-type="background" data-speed="30">
    <div class="scrolled-bg"></div>
    <div class="subscribe-content">
        <? if ($alreadySubscribed): ?>
            <?= $helper->render('subscribe/friend/__alreadySubscribed') ?>
        <? elseif ($successfullySubscribed): ?>
            <?= $helper->render('subscribe/friend/__successfullySubscribed') ?>
        <? else: ?>
            <div class="subscribe-content__wrap">
                <?= $helper->render('subscribe/friend/__form') ?>
            </div>
        <? endif ?>
    </div>
</div>
</div>
