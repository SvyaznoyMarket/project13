<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <?php include_component('page', 'link_rel_canonical') ?>

    <?php include_partial('default/googleAnalytics') ?>

  </head>
  <body class='march8'>
    <div class="allpage">
      <div class='march8_inner_left'><div class='march_8_leftbg'><div class='march_8_lefttopbg'></div></div></div>
      <div class='march8_inner_right'><div class='march_8_rightbg'><div class='march_8_righttopbg'></div></div></div>
      <a class='march8_link' onclick="gaq.push(['_trackEvent', 'BannerClick', '8 марта уши']);" href='<?php echo url_for('productCatalog_category', array('productCategory' => 'gifthobby/podarki-na-8-marta-1522')) ?>'></a>
      <div class="allpageinner">

        <?php include_partial('default/header') ?>

        <!-- Page head -->
        <div class="pagehead">
          <?php if (!include_slot('page_breadcrumbs')): ?>
          <div class="breadcrumbs"><a href="<?php echo url_for('homepage') ?>">Enter.ru</a> &gt; <strong>Помощь пользователю</strong></div>
          <?php endif ?>

          <div class="clear"></div>
          <?php if (has_slot('title')): ?>
            <h1><?php include_slot('title') ?></h1>
          <?php endif ?>

          <noindex>
              <div class="searchbox">
                <?php include_component('search', 'form') ?>
              </div>
          </noindex>
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
        <div class="dropbox" style="left:703px; display:none;">
          <p>Перетащите сюда</p>
        </div>
        <!-- Flybox -->
        <ul class="lightboxmenu">
          <li class="fl"><a href="<?php echo url_for('user_signin') ?>" class="point point1"><b></b>Личный кабинет</a></li>
          <li><a href="<?php echo url_for('cart') ?>" class="point point2"><b></b>Моя корзина<span class="total" style="display:none;"><span id="sum"></span> &nbsp;<span class="rubl">p</span></span></a></li>
        </ul>
      </div>
    </div>
    <!-- /Lightbox -->

    <?php if (!include_slot('auth'))
      include_partial('default/auth') ?>

    <?php include_partial('default/admin') ?>


<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <!-- Yandex.Metrika counter -->
  <div style="display:none;"><script type="text/javascript">
  (function(w, c) {
      (w[c] = w[c] || []).push(function() {
          try {
              w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true, webvisor:true, params:window.yaParams||{ }});
          }
          catch(e) { }
      });
  })(window, "yandex_metrika_callbacks");
  </script></div>
  <script src="//mc.yandex.ru/metrika/watch_visor.js" type="text/javascript" defer="defer"></script>
  <noscript><div><img src="//mc.yandex.ru/watch/10503055" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
  <!-- /Yandex.Metrika counter -->
  <!-- AdHands -->
  <script type="text/javascript" src="http://sedu.adhands.ru/js/counter.js"></script>
  <script type="text/javascript">
      var report = new adhandsReport ('http://sedu.adhands.ru/site/');
      report.id('1053');
      report.send();
  </script>
  <noscript>
  <img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123" style="display:none;">
  </noscript>
  <!-- /AdHands -->
  <script type="text/javascript">
  (function() {
  document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random() + '" ></sc' + 'ript>');
  })();
  </script>
<?php endif ?>

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
<?php endif ?>

</body>
</html>
