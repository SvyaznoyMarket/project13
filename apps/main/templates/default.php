<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <base href="<?php echo $sf_request->getHost() ?>" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
</head>

<body>
<div class="bannersbox">
    <div class="bannersboxinner">
      <!--
        <div class="banner banner2"><a href=""><img src="images/banner2.png" alt="" width="148" height="132" /></a></div>
        <div class="banner banner3"><a href=""><img src="images/banner3.png" alt="" width="159" height="186" /></a></div>
        <div class="banner banner4"><a href=""><img src="images/banner4.png" alt="" width="182" height="236" /></a></div>
        <div class="banner banner5"><a href=""><img src="images/banner5.png" alt="" width="142" height="128" /></a></div>
      -->
      <?php include_component('default', 'slot', array('token' => 'banner_default')) ?>
    </div>
</div>


<div class="allpage">

    <div class="logo">Enter Связной</div>
      <!-- Topmenu -->
      <?php include_component('productCategory', 'root_list') ?>
      <!-- /Topmenu -->

    <div class="searchbox">
    <form action="">
        <input type="text" class="text" value="Поиск среди 30 000 товаров" /><input type="button" class="searchbutton" value="Найти" title="Найти" />
    </form>
    </div>


    <!--div class="bigbanner"><a href=""><img src="images/banner.jpg" alt="" width="768" height="302" /></a></div-->
      <?php include_component('default', 'slot', array('token' => 'big_banner')) ?>


    <div class="content">
        <div class="vcardtitle">Контакт с ENTER</div>
        <div class="vcard"><span class="tel">8 (800) 700 00 09</span></div>
        <div class="address">Звонок бесплатный. Радость в подарок :)</div>
    </div>


    <div class="links">
        <a href="#" class="link1">Сервис</a>
        <a href="http://www.svyaznoybank.ru/" class="link2">Связной</a>
        <a href="http://www.svyaznoybank.ru/" class="link3">Финансовые услуги</a>
    </div>

    <div class="copy">
        <div class="pb5">&copy; &laquo;Enter&raquo; 2011. Все права защищены. Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату. <a href="/">Условия продажи</a></div>
    </div>


    <div class="clear"></div>
</div>

</body>
</html>
