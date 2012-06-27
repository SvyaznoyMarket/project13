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


    <?php if (has_slot('header_meta_og')): ?>
      <?php include_slot('header_meta_og') ?>
    <?php endif ?>

    <?php include_partial('default/googleAnalytics') ?>
    <!-- <script type="text/javascript" src="/js/adfox.asyn.code.ver3.js"> </script> -->
  </head>
  <body data-template="<?php echo $sf_request->getParameter('_template', 'default') ?>">
  <?php if (has_slot('after_body_block')): ?>
    <?php include_slot('after_body_block') ?>
  <?php endif ?>
    <div class="allpage" id="page">
<!--AdFox START-->
<!-- ________________________AdFox Asynchronous code START__________________________ -->
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
AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=enlz&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
// -->
</script>
<!-- _________________________AdFox Asynchronous code END___________________________ -->

     <div class="allpageinner">

        <?php include_partial('default/header') ?>

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
        <?php if (has_slot('navigation_seo')) include_slot('navigation_seo') ?>
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
        <div class="dropbox" style="left:733px; display:none;">
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
    <!-- /Lightbox -->

    <?php include_partial('region/select') ?>

    <script src="/js/LAB.min.js" type="text/javascript"></script>
    <script src="/js/loadjs.js" type="text/javascript"></script>
    <?php if (!include_slot('auth')) include_partial('default/auth') ?>

    <?php //include_partial('default/admin') ?>


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

<?php include_component('default', 'adriver') ?>
  </body>
</html>
