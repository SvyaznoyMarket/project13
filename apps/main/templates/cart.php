<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php //include_javascripts() ?>
    <?php include_component('page', 'link_rel_canonical') ?>
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-25485956-1']);
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
    _gaq.push(['_addOrganic', 'ru.search.yahoo.com','p']);
    _gaq.push(['_addOrganic', 'ya.ru', 'q']);
    _gaq.push(['_addOrganic', 'm.yandex.ru','query']);
    _gaq.push(['_trackPageview']);
    _gaq.push(['_trackPageLoadTime']);
    (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();
</script>
  </head>
  <body>
    <div class="allpage">
      <div class="allpageinner">

        <!-- Topbar -->
        <div class="topbar" style="position: relative; z-index: 12;">
          <div class="region" style="margin-top: 7px;">
              <?php include_component('region', 'select') ?>
          </div>
          <noindex>
              <div class="usermenu">
                <div class="point"><a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>" class="f1">F1 сервис</a></div>
                <div class="point"><?php include_partial('default/user') ?></div>
                <div class="point next"><a href="<?php echo url_for('default_show', array('page' => 'how_make_order',)) ?>">Помощь покупателю</a></div>
              </div>
          </noindex>
        </div>
        <!-- /Topbar -->
        <!-- Header -->
        <div class="header">
          <?php include_partial('default/logo') ?>
          <!-- Topmenu -->
          <?php include_component('productCategory', 'root_list') ?>
          <!-- /Topmenu -->
          <div class="headerright" style="font-size: 14px; font-family: Enter; padding-top: 30px;">
            Контакт cENTER
            <div class="vcard"><div class="tel" style="font-size: 24px; line-height: 24px;"><span style="font-size: 14px;">8 (800)</span><br />700-00-09<br /></div></div>
            <div style="margin-top: -8px">круглосуточно</div>
            <!--a href="">Перезвоните мне</a><br />
            <a href="">Отследить мой заказ</a-->
          </div>
          <!-- Extramenu -->
          <?php #include_component('productCategory', 'extra_menu') ?>
          <!-- /Extramenu -->
        </div>
        <!-- /Header -->

        <!-- Page head -->
        <?php if (!include_slot('page_head')): ?>
          <?php include_partial('default/page_head') ?>
        <?php endif ?>
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

    <?php include_javascripts() ?>
    <?php include_component('default', 'footer', array('class' => 'footer_cart', )) ?>

    <?php if (!include_slot('auth')) include_partial('default/auth') ?>

<?php include_partial('default/admin') ?>
<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true, webvisor:true});
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

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
<?php endif ?>
  </body>
</html>
