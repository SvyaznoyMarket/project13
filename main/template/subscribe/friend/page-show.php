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
                    <span class="subscribe-advantages-list-item__text">Более 3800 точек самовывоза</span>
                </li>
                <li class="subscribe-advantages-list-item">
                    <img src="http://content.enter.ru/wp-content/uploads/2015/04/shield.png">
                    <span class="subscribe-advantages-list-item__text">Безопасные покупки<br/> на сайте</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="subscribe-block subscribe-block6" data-type="background" data-speed="10">
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
<div class="subscribe-footer">
    <div class="subscribe-footer__inn">
        <div class="subscribe-footer__top">
            <ul class="footer_socnet clearfix">
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.facebook.com/enter.ru"><i class="i-share i-share-fb"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://twitter.com/enter_ru"><i class="i-share i-share-tw"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://vk.com/public31456119"><i class="i-share i-share-vk"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.youtube.com/user/EnterLLC"><i class="i-share i-share-yt"></i></a></li>
                <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="http://ok.ru/enterllc"><i class="i-share i-share-od"></i></a></li>
            </ul>
            <ul class="footer_app">
                <li class="footer_app_i"><a target="_blank" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img class="footer_app_img" src="/styles/footer/img/apple.png"></a></li>

                <li class="footer_app_i">
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=ru.enter">
                        <img class="footer_app_img" alt="Get it on Google Play" src="/styles/footer/img/google.png">
                    </a>
                </li>
            </ul>
            <div class="sitelogo"></div>
        </div>
        <div class="subscribe-footer__text">
            Нажимая на кнопку «Подписаться», Вы соглашаетесь на получение информационно-рекламной рассылки от Enter.<br>
            Сроки проведения акции — 01.01.2015 — 30.09.2015.<br>
            Купон действителен до 30.09.2015.<br>
            Минимальная сумма заказа 2000 рублей.<br>
            Купон не распространяется на прием платежей, продажу брендированных карт, подарочных сертификатов и услуг.<br>
            Купон действует на покупку любых товаров в магазине Enter, кроме электроники, бытовой техники, Pandora, Tchibo.<br>
            Купон не действует на товары, отмеченные шильдиками «Суперцена» и «SALE».
        </div>
        <div class="footer_cpy clearfix">
            <div class="footer_cpy_l">© ООО «Энтер» 2011–2015. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
        </div>
    </div>
</div>

