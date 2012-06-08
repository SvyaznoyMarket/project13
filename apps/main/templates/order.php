<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <?php include_stylesheets() ?>
  <?php //include_javascripts() ?>
  <?php include_component('page', 'link_rel_canonical') ?>

  <?php include_partial('default/googleAnalytics') ?>
  <script type="text/javascript" src="/js/adfox.asyn.code.ver3.js"> </script>
</head>

<body data-template="order">
<?php LastModifiedHandler::setLastModified(); ?>
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
  <div class="allpageinner buyingpage">

    <!-- Header -->
    <div class="basketheader">
      <div class="bNLogo"><?php include_slot('title') ?></div>
      <div class="headerright mNLogo">
        <h2>Круглосуточный контакт сEnter</h2>
        <div>8 (800) 700-00-09</div>

        <span>Звонок бесплатный. Радость в подарок :)</span>
      </div>
    </div>
    <!-- /Header -->

    <!-- Page head -->
    <div class="pagehead">
      <div class="clear"></div>
      <div class="line"></div>
      <?php if (has_slot('user')): ?>
      <div class='bFormAuth'>
        <?php include_slot('user') ?>
      </div>
      <div class="line"></div>
      <?php endif ?>
    </div>
    <!-- Page head -->

    <div class="clear"></div>

    <?php if (has_slot('receipt')): ?>
    <?php include_slot('receipt') ?>
    <?php endif ?>

    <?php echo $sf_content ?>

    <div class="clear"></div>
    <?php if (has_slot('navigation_seo')) include_slot('navigation_seo') ?>

  </div>
</div>

<?php include_component('default', 'footer', array('view' => 'compact')) ?>
<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>

<?php if (!include_slot('auth')) include_partial('default/auth') ?>


<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <?php include_partial('default/yandexMetrika') ?>
<script type="text/javascript">
  (function() {
    <?php if (isset($orderSum) && $orderSum > 0 && isset($orderId) && $orderId > 0): ?>
      var orderSum = '<?php echo $orderSum ?>';
      document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/pixel.js?cost=' + escape(orderSum) + '&r=' + Math.random() + '" ></sc' + 'ript>');
      <?php else: ?>
      document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random() + '" ></sc' + 'ript>');
      <?php endif ?>
  })();
</script>
  <?php endif ?>

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
  <?php endif ?>
<?php include_component('default', 'adriver') ?>

</body>
</html>
