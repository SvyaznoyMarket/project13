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
	<div class="adfoxWrapper" id="adfoxbground"></div>
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
                <?php render_partial('search/templates/_form.php') ?>
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
    <script src="/js/jquery-1.6.4.min.js" type="text/javascript"></script>
    <script src="/js/LAB.min.js" type="text/javascript"></script>
    <script src="/js/loadjs.js" type="text/javascript"></script>

    <?php if (!include_slot('auth')) include_partial('default/auth') ?>

    <?php include_partial('region/select') ?>

    <?php //include_partial('default/admin') ?>

<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <?php include_partial('default/yandexMetrika') ?>

  <div id="adblender" class="jsanalytics"></div>
<?php endif ?>

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
<?php endif ?>

<?php include_component('default', 'adriver') ?>
</body>
</html>
