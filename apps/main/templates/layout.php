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
          <?php include_component('productCategory', 'list_root') ?>
          <!-- /Topmenu -->
          <div class="headerright">
            Заказ и консультации
            <div class="vcard"><div class="tel"><span>(495)</span>555-66-77</div></div>
            <a href="">Перезвоните мне</a><br />
            <a href="">Отследить мой заказ</a>
          </div>
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
          <dt><a href="">Компания Enter</a></dt>
          <dd><a href="">О Компании</a></dd>
          <dd><a href="">Наши преимущества</a></dd>
          <dd><a href="">Контакты</a></dd>
          <dd><a href="">Обратная связь</a></dd>
          <dd><a href="">Карта сайта</a></dd>
        </dl>
        <dl>
          <dt><a href="">Услуги и сервисы</a></dt>
          <dd><a href="">Сервис F1</a></dd>
          <dd><a href="">Доставка товара</a></dd>
          <dd><a href="">Гарантия</a></dd>
          <dd><a href="">Бесплатный возврат</a></dd>
          <dd><a href="">Помощь покупателю</a></dd>
        </dl>
        <dl>
          <dt><a href="">Хотите работать у нас?</a></dt>
          <dd><a href="">Работа в Enter &gt;&gt;&gt;</a></dd>
          <dd>Краткий текст о сайте вакансий, призыв устроиться в Enter.ru</dd>
        </dl>
        <dl class="next">
          <dt>Единая справочная</dt>
          <dd><strong class="font16">8 (800) 555-66-77</strong></dd>
          <dd>Круглосуточно. Без выходных</dd>
        </dl>
      </div>

      <div class="social">
        Посетите нас
        <a href="" class="twitter">twitter</a>
        <a href="" class="facebook">facebook</a>
        <a href="" class="vkontakte">vkontakte</a>
        <a href="" class="livejournal">livejournal</a>
        <a href="" class="youtube">youtube</a>
        <a href="" class="mobile">mobile</a>
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

    <?php if (!include_slot('form_signin'))
      include_partial('default/form_signin') ?>

<?php include_partial('default/admin') ?>
  </body>
</html>
