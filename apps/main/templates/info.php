<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-25485956-2']);
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
    _gaq.push(['_trackPageview']);
    _gaq.push(['_trackPageLoadTime']);
    (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
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
            <div class="point"><a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>" class="f1">F1 сервис</a></div>
            <div class="point"><?php include_partial('default/user') ?></div>
            <div class="point next"><a href="<?php echo url_for('default_show', array('page' => 'how_make_order',)) ?>">Помощь покупателю</a></div>
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
          <div class="breadcrumbs"><a href="<?php echo url_for('homepage') ?>">Enter.ru</a> &gt; <strong>Помощь пользователю</strong></div>

          <div class="clear"></div>
          <?php if (has_slot('title')): ?>
            <h1><?php include_slot('title') ?></h1>
          <?php endif ?>

          <div class="searchbox">
            <?php include_component('search', 'form') ?>
          </div>
          <div class="clear pb20"></div>
          <div class="line"></div>
        </div>
        <!-- Page head -->

        <?php if (has_slot('left_column')): ?>
          <!-- Column685 -->
          <div class="float100">
            <div class="column685">
              <?php echo $sf_content ?>
            </div>
          </div>
          <!-- /Column685-->

          <!-- Column215 -->
          <div class="column215">
            <?php include_slot('left_column') ?>
          </div>
          <!-- /Column215 -->

        <?php else: ?>
          <?php echo $sf_content ?>
        <?php endif ?>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>

    </div>

    <?php include_component('default', 'footer') ?>

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
          <div class="photo"><b class="delete" title="Удалить"></b><a href=""><img src="images/photo34.jpg" alt="" width="120" height="120" /></a></div>
          <a href="">Смартфон Samsung Galaxy S II (GT-I9100 )16 Гб</a> <strong>64 543 <span class="rubl">p</span></strong>
        </li>
        <li>
          <div class="photo"><b class="delete" title="Удалить"></b><a href=""><img src="images/photo63.jpg" alt="" width="120" height="120" /></a></div>
          <a href="">Смартфон HTC Wildfire S White 16 Гб</a> <strong>64 543 <span class="rubl">p</span></strong>
        </li>
        <li>
          <div class="photo"><b class="delete" title="Удалить"></b><a href=""><img src="images/photo64.jpg" alt="" width="120" height="120" /></a></div>
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
  <!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true});
        }
        catch(e) { }
    });
})(window, "yandex_metrika_callbacks");
</script></div>
<script src="//mc.yandex.ru/metrika/watch_visor.js" type="text/javascript" defer="defer"></script>
<noscript><div><img src="//mc.yandex.ru/watch/10503055" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
