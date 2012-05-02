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

    <?php include_partial('default/googleAnalytics') ?>

  </head>
  <body data-template="<?php echo $sf_request->getParameter('_template', 'infopage') ?>">
    <div class="allpage" id="page">
<!--AdFox START-->
<!--enter-->
<!--Площадка: Enter.ru / * / *-->
<!--Тип баннера: BackGround-->
<!--Расположение: <верх страницы>-->
<script type="text/javascript">
<!--
if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
if (typeof(document.referrer) != 'undefined') {
  if (typeof(afReferrer) == 'undefined') {
    afReferrer = escape(document.referrer);
  }
} else {
  afReferrer = '';
}
var addate = new Date();
document.write('<scr' + 'ipt type="text/javascript" src="http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=enlz&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '"><\/scr' + 'ipt>');
// -->
</script>
<!--AdFox END-->
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
      <?php if (has_slot('navigation_seo')) include_slot('navigation_seo') ?>
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
    <script src="/js/LAB.min.js" type="text/javascript"></script>
    <script src="/js/loadjs.js" type="text/javascript"></script>

    <?php if (!include_slot('auth'))
      include_partial('default/auth') ?>

    <?php include_partial('default/admin') ?>


<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <?php include_partial('default/yandexMetrika') ?>

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
<?php include_component('default', 'admitad') ?>


</body>
</html>
