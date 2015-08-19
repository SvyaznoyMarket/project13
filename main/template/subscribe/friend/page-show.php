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

<style>
    .subscribe-page>div{
        width: 100%;
        height: 700px;
    }
    .subscribe-block1,
    .subscribe-block2,
    .subscribe-block3{
        width: 100%;
        height: 700px;
        background: url(http://content.enter.ru/wp-content/uploads/2015/04/only-text-white2.jpg) no-repeat center;
    }
    .subscribe-block4{
        background: url(http://content.enter.ru/wp-content/uploads/2015/04/bg-blue.jpg) no-repeat center;
        width: 100%;
        height: 550px;
    }
    .subscribe-block5{
        background: url(http://content.enter.ru/wp-content/uploads/2015/04/bg-colour.jpg) no-repeat center;
        width: 100%;
        height: 550px;
    }
    .subscribe-block6{
        background: url(http://content.enter.ru/wp-content/uploads/2015/04/form2.jpg) no-repeat center;
        width: 100%;
        height: 450px;
    }
    .subscribe-content{
        display: block;
        position: relative;
        text-align: center;
        width: 960px;
        margin: 0 auto;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -ms-box-sizing: border-box;
        padding-left: 70px;
    }
    .subscribe-text{
        padding-top: 60px;
        text-align: left;
    }
    .subscribe-text__title{
        font-size: 78px;
        color: #f99b1c;
        font-family: "Enter type";
        line-height: 1em;
    }
    .subscribe-text__big{
        position: relative;
        color: #fff;
        font-size: 52px;
        font-family: "Enter type";
        display: block;
        line-height: 1em;
        margin-bottom: 20px;
    }
    .subscribe-text__medium{
        position: relative;
        color: #fff;
        font-size: 34px;
        font-family: "Enter type";
        display: block;
        line-height: 1em;
        margin-bottom: 20px;
    }
    .subscribe-text__small{
        position: relative;
        color: #fff;
        font-size: 20px;
        font-family: "Enter type";
    }
    .subscribe-text__big .absolute{
        position: absolute;
        bottom: -50px;
        right: 400px;
    }
    .subscribe-text__img{
        text-align: left;
        height: 105px;
        margin-bottom: 22px;
    }
    .subscribe-form{
        text-align: left;
    }
    .subscribe-form-group{
        display: inline-block;
    }
    .subscribe-form-group label{
        display: block;
        color: #fff;
        font-size: 18px;
    }
    .subscribe-form-group .subscribe-email{
        background: #fff;
        color: #8a8a8a;
        width: 278px;
        height: 38px;
        line-height: 38px;
        max-height: 38px;
        margin: 0 20px 0 0;
        border: 1px solid #c7c7c7;
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
        border-radius: 6px;
        font-size: 18px;
        float:left;
        padding: 0;
    }
    .subscribe-form-btn{
        width: 200px;
        height: 40px;
        line-height: 35px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -ms-box-sizing: border-box;
        border-bottom: 3px solid #c27916;
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
        border-radius: 6px;
        background-color: #f99b1c;
        color: #ffffff;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        float:left;
    }
    .subscribe-letter{
        padding: 50px 0 0 330px;
        text-align: left;
    }
    .subscribe-letter-list{
        width: 600px;
        margin-top: 30px;
    }
    .subscribe-letter-list-item{
        float:left;
        width: 290px;
        text-align: center;
        margin-bottom: 35px;
    }
    .subscribe-letter-list-item__text{
        display: block;
        font-size: 18px;
        margin-top: 16px;
    }
    .subscribe-letter__header{
        font-family: "Enter";
        font-size: 68px;
        color: #fff;
        text-transform: uppercase;
        margin-left: 30px;
    }
    .subscribe-advantages{
        padding-top: 100px;
    }
    .subscribe-advantages-list{
        width: 460px;
        margin: 0 auto;

    }
    .subscribe-advantages-list-item{
        width: 230px;
        text-align: center;
        float:left;
        margin-bottom: 50px;
    }
    .subscribe-advantages-list-item__text{
        display: block;
        font-size: 20px;
        color: #fff;
        margin-top: 10px;
    }
    .subscribe-content__wrap{
        padding-top: 100px;
        padding-left: 50px;
    }
</style>
<? if ($alreadySubscribed): ?>
    <div class="subscribe-block2">
        <div class="subscribe-content">
            <?= $helper->render('subscribe/friend/__alreadySubscribed') ?>
        </div>
    </div>
<? elseif ($successfullySubscribed): ?>
    <div class="subscribe-block3">
        <div class="subscribe-content">
            <?= $helper->render('subscribe/friend/__successfullySubscribed') ?>
        </div>
    </div>
<? else: ?>
    <div class="subscribe-page">
        <div class="subscribe-block1">
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

<div class="subscribe-block4">
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
<div class="subscribe-block5">
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
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/car.png">
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
<div class="subscribe-block6">
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
