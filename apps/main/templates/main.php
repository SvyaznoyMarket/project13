<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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

  <body data-template="main">
    <?php LastModifiedHandler::setLastModified(); ?>

    <div class="bannersbox">
      <div class="bannersboxinner">
        <div class="banner banner3"><img class="rightImage" src="" alt="" /></div>
        <div class="banner banner4"><img class="leftImage" src="" alt="" /></div>
      </div>
    </div>

    <?php include_slot('banner') ?>

    <div class="allpage">
	<div class="adfoxWrapper" id="adfox980"></div>


      <div class="bHeaderWrap">
        <div class="bHeader">
          <a href class='bToplogo'></a>
          <?php include_component('productCategory_', 'root_list') ?>
          <div class="bHeader__eLong"></div>
        </div>
      </div>

      <noindex>
        <div class="searchbox">
          <?php render_partial('search/templates/_form.php', array('view' => 'main')) ?>
        </div>
      </noindex>

      <div class="bigbanner">
        <div class='bCarouselWrap'>
          <div class='bCarousel'>
            <div class='bCarousel__eBtnL leftArrow'></div>
            <div class='bCarousel__eBtnR rightArrow'></div>
            <img class="centerImage" src="" alt=""/>
          </div>
        </div>
      </div>

      <?php include_component('default', 'footer', array('view' => 'main')) ?>

      <div class="clear"></div>
    </div>
<script src="/js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>

<?php if ('live' == sfConfig::get('sf_environment')): ?>
    <?php include_partial('default/yandexMetrika') ?>
    <div id="adblender" class="jsanalytics"></div>
<?php endif ?>

    <?php if (has_slot('seo_counters_advance')): ?>
      <?php include_slot('seo_counters_advance') ?>
    <?php endif ?>

    <div id="heiasMain" class="jsanalytics"></div>

  <?php include_component('default', 'adriver') ?>
  </body>
</html>
