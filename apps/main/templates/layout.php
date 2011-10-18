<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
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
    <div class="allpage">
      <div class="allpageinner">

        <!-- Topbar -->
        <div class="topbar">
          <div class="region">
            Регион: <?php include_partial('default/region') ?>
          </div>
          <div class="usermenu">
            <div class="point"><a href="<?php echo url_for('service_category') ?>" class="f1">F1 сервис</a></div>
            <div class="point"><?php include_partial('default/user') ?></div>
            <div class="point next"><a href="<?php echo url_for('productHelper') ?>">Помощь покупателю</a></div>
          </div>
        </div>
        <!-- /Topbar -->
        <!-- Header -->
        <div class="header">
          <?php include_partial('default/logo') ?>
          <!-- Topmenu -->
          <?php include_component('productCategory', 'root_list') ?>
          <!-- /Topmenu -->
          <div class="headerright" style="font-size: 14px; font-family: Enter; padding-top: 36px;">
            Контакт cENTER
            <div class="vcard"><div class="tel" style="font-size: 24px; line-height: 24px;"><span style="font-size: 14px;">8 (800)</span><br />700-00-09<br /></div></div>
            <!--a href="">Перезвоните мне</a><br />
            <a href="">Отследить мой заказ</a-->
          </div>
          <!-- Extramenu -->
          <?php include_component('productCategory', 'extra_menu') ?>
          <!-- /Extramenu -->
        </div>
        <!-- /Header -->
        <!-- Page head -->
        <div class="pagehead">
          <div class="breadcrumbs">
            <?php if (has_slot('navigation')): ?>
              <?php include_slot('navigation') ?>
            <?php endif ?>
          </div>
          <div class="clear"></div>
          <?php if (has_slot('title')): ?>
            <h1><?php include_slot('title') ?></h1>
          <?php endif ?>
          <div class="searchbox">
            <form action=""><input type="text" class="searchtext" value="Поиск товаров" onfocus="if (this.value == 'Поиск товаров') this.value = '';" onblur="if (this.value == '') this.value = 'Поиск товаров';"  /><input type="button" class="searchbutton" value="Найти" title="Найти"  id="try-1" /></form>
          </div>
          <div class="clear pb20"></div>
          <div class="line"></div>
        </div>

        <!-- Page head -->

        <?php if (has_slot('left_column')): ?>
          <div class="float100">
            <div class="column685">
              <?php echo $sf_content ?>
            </div>
          </div>
          <div class="column215">
            <?php include_slot('left_column') ?>
          </div>
        <?php else: ?>
          <?php echo $sf_content ?>
        <?php endif ?>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <div class="bottommenu">
        <dl>
          <dt><a href="#">Компания Enter</a></dt>
          <dd><a href="#">О Компании</a></dd>
          <dd><a href="#">Наши преимущества</a></dd>
          <dd><a href="#">Контакты</a></dd>
          <dd><a href="#">Обратная связь</a></dd>
          <dd><a href="#">Карта сайта</a></dd>
        </dl>
        <dl>
          <dt><a href="#">Услуги и сервисы</a></dt>
          <dd><a href="#">Сервис F1</a></dd>
          <dd><a href="#">Доставка товара</a></dd>
          <dd><a href="#">Гарантия</a></dd>
          <dd><a href="#">Бесплатный возврат</a></dd>
          <dd><a href="#">Помощь покупателю</a></dd>
        </dl>
        <dl>
          <dt><a href="">Хотите работать у нас?</a></dt>
          <dd><a href="">Работа в Enter &gt;&gt;&gt;</a></dd>
          <dd>Краткий текст о сайте вакансий, призыв устроиться в Enter.ru</dd>
        </dl>
        <dl class="next">
          <dt>Контакт cENTER</dt>
          <dd><strong class="font16">8 (800) 700 00 09</strong></dd>
          <dd>Звонок бесплатный.<br />Радость в подарок :)</dd>
        </dl>
      </div>

      <div class="social">
        Посетите нас
        <a href="#" class="twitter">twitter</a>
        <a href="#" class="facebook">facebook</a>
        <a href="#" class="vkontakte">vkontakte</a>
        <a href="#" class="livejournal">livejournal</a>
        <a href="#" class="youtube">youtube</a>
        <a href="#" class="mobile">mobile</a>
      </div>

      <div class="subscribeform">
        <form action=""><input type="text" class="text" value="Введите Ваш E-mail" onfocus="if (this.value == 'Введите Ваш E-mail') this.value = '';" onblur="if (this.value == '') this.value = 'Введите Ваш E-mail';"  /><input type="button" class="subscribebutton" value="Подписаться на рассылку" /></form>
      </div>

      <div class="footerbottom">
        <div class="copy">
          &copy; &laquo;Enter&raquo; 2002-2011. Все права защищены.<br />
          Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.
          <div>
            <a href="">Политика конфидециальности</a>
            <a href="">Условия продажи в интернет-магазине</a>
            <b><i class="mistakeimg"></i><a href="" class="orange">сообщить!</a></b>
          </div>
        </div>
        <div class="counter">
          <a href=""><img src="/images/images/counter1.gif" alt="" width="80" height="30" /></a>
          <a href=""><img src="/images/images/counter2.gif" alt="" width="50" height="30" /></a>
          <a href=""><img src="/images/images/counter3.gif" alt="" width="50" height="30" /></a>
        </div>
      </div>
    </div>
    <!-- /Footer -->
    <!-- Lightbox -->
    <div class="lightbox">
      <div class="lightboxinner">
        <!--div class="dropbox" style="left:365px; display:none;">
          <p>Перетащите сюда</p>
        </div>
        <div class="dropbox" style="left:517px; display:none;">
          <p>Перетащите сюда</p>
        </div-->
        <div class="dropbox" style="left:703px; display:none;">
          <p>Перетащите сюда</p>
        </div>
        <!-- Flybox -->
        <ul class="lightboxmenu">
          <li class="fl"><a href="<?php echo url_for('user_signin') ?>" class="point point1"><b></b>Личный кабинет</a></li>
          <li><a href="<?php echo url_for('cart') ?>" class="point point2"><b></b>Моя корзина<span class="total" style="display:none;"><span id="sum"></span> &nbsp;<span class="rubl">p</span></span></a></li>
          <!--li><a href="" class="point point3"><b></b>Список желаний</a></li>
          <li><a href="" class="point point4"><b></b>Сравнение</a></li-->
        </ul>
      </div>
    </div>
    <div id="zaglu" style="display:none"><!-- AJAX emulation zaglushka-->
      <ul class="comparisonblock">
        <li>
          <div class="photo"><b class="delete" title="Удалить"></b><a href=""></a></div>
          <a href="">Смартфон Samsung Galaxy S II (GT-I9100 )16 Гб</a> <strong>64 543 <span class="rubl">p</span></strong>
        </li>
        <li>
          <div class="photo"><b class="delete" title="Удалить"></b><a href=""></a></div>
          <a href="">Смартфон HTC Wildfire S White 16 Гб</a> <strong>64 543 <span class="rubl">p</span></strong>
        </li>
        <li>
          <div class="photo"><b class="delete" title="Удалить"></b><a href=""></a></div>
          <a href="">Смартфон HTC Sensation</a> <strong>64 543 <span class="rubl">p</span></strong>
        </li>
        <li>
          <div class="comparphoto"></div>
          <div class="gray ac">Товар для сравнения</div>
        </li>
      </ul>
      <div class="fl form width230">
        <div class="pb5">Товары, которые вы сравнивали в других разделах:</div>
        <div class="selectbox selectbox225 mb70"><i></i>
          <select class="styled" name="3">
            <option value="1">Электроника</option>
            <option value="2">Товары для дома</option>
            <option value="3">Сделай сам (инструменты)</option>
          </select>
        </div>
        <a href="" class="button bigbuttonlink" value="">Перейти в сравнение</a>
      </div>
    </div>

    <!-- /Lightbox -->

    <?php if (!include_slot('auth'))
      include_partial('default/auth') ?>

<?php include_partial('default/admin') ?>
  </body>
</html>
