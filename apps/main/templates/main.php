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
    <script type="text/javascript" src="/js/adfox.asyn.code.ver3.js"> </script>
  </head>

  <body data-template="main">
    <?php LastModifiedHandler::setLastModified(); ?>

    <div class="bannersbox">
      <div class="bannersboxinner">
        <div class="banner banner3"><img class="rightImage" src="" alt="" /></div>
        <div class="banner banner4"><img class="leftImage" src="" alt="" /></div>
      </div>
    </div>

    <?php include_component('banner', 'show', array('view' => 'main')) ?>

    <div class="allpage">
<!-- ________________________AdFox Asynchronous code START__________________________ -->
<!--enter-->
<!--Площадка: Enter.ru / * / *-->
<!--Тип баннера: 980х-->
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
var dl = escape(document.location);
var pr1 = Math.floor(Math.random() * 1000000);

document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emvi&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
// -->
</script>
<!-- _________________________AdFox Asynchronous code END___________________________ -->


      <div class="bHeaderWrap">
        <div class="bHeader">
          <a href class='bToplogo'></a>
          <?php include_component('productCategory', 'root_list') ?>
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

<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>

<?php if ('live' == sfConfig::get('sf_environment')): ?>
    <?php include_partial('default/yandexMetrika') ?>
  <script type="text/javascript">
  (function() {
  document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random() + '" ></sc' + 'ript>');
  })();
  </script>
<?php endif ?>

    <?php if (has_slot('seo_counters_advance')): ?>
      <?php include_slot('seo_counters_advance') ?>
    <?php endif ?>


  <script type="text/javascript">
      (function(d){
          var HEIAS_PARAMS = [];
          HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
          HEIAS_PARAMS.push(['pb', '1']);
          if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
          window.HEIAS.push(HEIAS_PARAMS);
          var scr = d.createElement('script');
          scr.async = true;
          scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
          var elem = d.getElementsByTagName('script')[0];
          elem.parentNode.insertBefore(scr, elem);
      }(document));
  </script>
  <?php include_component('default', 'adriver') ?>
  </body>
</html>
